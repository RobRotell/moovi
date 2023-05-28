<?php


namespace Moovi\Controllers;


use Exception;
use InvalidArgumentException;
use Moovi\Connectors\AiApi;
use Moovi\Datasets\Nationalities;
use Moovi\Helpers;
use Moovi\Models\Movie;
use PDO;


class MovieHandler
{
	/**
	 * Get movie by date
	 *
	 * @since 0.0.1
	 * 
	 * @throws InvalidArgumentException Invalid date format
	 *
	 * @param string $date Date in Y-m-d format
	 * @return array Movies matching date
	 */
	public static function getMoviesByDate( string $date ): array
	{
		$movies = [];

		if( !Helpers::validateDate( $date, 'Y-m-d' ) ) {
			throw new InvalidArgumentException( 'Argument must be a valid date in a "Y-m-d" format.' );
		}

		// todo -- target specific columns?
		$pdo = Database::getPdo();

		$sql = $pdo->prepare( 'SELECT * FROM movies WHERE `date` = :date' );
		$sql->execute(
			[
				'date' => $date
			]
		);

		$results = $sql->fetchAll();

		if( $results ) {
			foreach( $results as $attrs ) {
				$movies[] = Movie::createFromAttrs( (array)$attrs );
			}
		}

		return $movies;
	}

	
	/**
	 * Get movie by date
	 *
	 * @since 0.0.1
	 *
	 * @return array Random movies
	 */
	public static function getRandomMovies( int $limit = 1 ): array
	{
		$movies = [];

		// should be one or more
		$limit = abs( $limit ) ?: 1;

		// todo -- target specific columns?
		$pdo = Database::getPdo();

		$sql = $pdo->prepare( 
			'SELECT * FROM movies ORDER BY RAND() LIMIT :limit' 
		);

		// limit requires an integer so we need to use bindParam instead of execute
		$sql->bindParam( ':limit', $limit, PDO::PARAM_INT );
		$sql->execute();

		$results = $sql->fetchAll();

		if( $results ) {
			foreach( $results as $attrs ) {
				$movies[] = Movie::createFromAttrs( (array)$attrs );
			}
		}

		return $movies;
	}


	/**
	 * Process incoming request
	 *
	 * @since 0.0.1
	 * 
	 * @todo Record token usage
	 * @todo Exception handling
	 *
	 * @param string $date Date in Y-m-d format
	 * @return Movie
	 */
	public static function createMovie( string $date = null ): Movie
	{
		if( !empty( $date ) && !Helpers::validateDate( $date, 'Y-m-d' ) ) {
			throw new InvalidArgumentException( 'Argument must be a valid date in a "Y-m-d" format.' );
		} else {
			$date = date( 'Y-m-d' );
		}

		$prompts = Prompts::createPrompts();

		$title 		= AiApi::completePrompt( $prompts['title'] );
		$tagline	= AiApi::completePrompt( $prompts['tagline'] );
		$poster 	= AiApi::generateImage( $prompts['poster'] );
		$director 	= AiApi::completePrompt( sprintf( 'Create a %s name between two and four words.', Nationalities::getSkewedRandomValue( 'American', 200 ) ) );

		// remove leading and trailing quotation marks that OpenAI sometimes add
		$title = Helpers::stripLeadingTrailingQuotationMarks( $title );
		$tagline = Helpers::stripLeadingTrailingQuotationMarks( $tagline );
		
		// don't need an AI to make random numbers
		$releaseYear = Helpers::generateRandomYear();

		$imgName = sprintf( 
			'%s-%s', 
			$date,  
			sha1( json_encode( $prompts ) ),
		);

		// save original PNG to storage and then create JPG variant for frontend
		$posterFilePaths = ImageHandler::save( $poster, $imgName );

		// frontend URL to save to DB
		$posterUrls = [];
		foreach($posterFilePaths as $size => $filePath ) {
			$posterUrls[ $size ] = Helpers::getImageUrlFromFilePath( $filePath );
		}

		// save new movie to database
		$pdo = Database::getPdo();

		$sql = $pdo->prepare(
			'INSERT INTO movies ( date, title, tagline, genre, director, release_year, image_urls, prompts ) 
			VALUES ( :date, :title, :tagline, :genre, :director, :release_year, :image_urls, :prompts )'
		);

		$sql->execute(
			[
				'date' => $date,
				'title' => $title,
				'tagline' => $tagline,
				'genre' => $prompts['__meta']['genre'],
				'director' => $director,
				'release_year' => $releaseYear,
				'image_urls' => json_encode( $posterUrls ),
				'prompts' => json_encode( $prompts ),
			]
		);

		// get ID of last inserted row
		$sql = $pdo->prepare( 'SELECT id FROM movies ORDER BY id DESC LIMIT 0,1' );
		$sql->execute();
		
		$result = $sql->fetch();

		if( empty( $result ) ) {
			throw new Exception( 'Failed to save movie.' );
		}

		return Movie::createFromId( $result->id );
	}

}

