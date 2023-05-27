<?php


namespace Moovi\Controllers;


use Exception;
use InvalidArgumentException;
use Moovi\Connectors\Ai;
use Moovi\Datasets\Nationalities;
use Moovi\Helpers;
use Moovi\Models\Movie;


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
	 * Process incoming request
	 *
	 * @since 0.0.1
	 * 
	 * @todo Check for movie before creating? Just to be safe?
	 * @todo Exception handling
	 *
	 * @param string $date Date in Y-m-d format
	 * @return Movie
	 */
	public static function createMovieByDate( string $date ): Movie
	{
		$prompts = Prompts::createPrompts();

		$title 		= Ai::completePrompt( $prompts['title'] );
		$tagline	= Ai::completePrompt( $prompts['tagline'] );
		$poster 	= Ai::generateImage( $prompts['poster'] );
		$director 	= Ai::completePrompt( sprintf( 'Create a %s name.', Nationalities::getSkewedRandomValue( 'American', 50 ) ) );
		
		// don't need an AI to make random numbers
		$releaseYear = Helpers::generateRandomYear();

		$name = sprintf( 
			'%s-%s', 
			$date,  
			sha1( json_encode( $prompts ) ),
		);

		// save original PNG to storage and then create JPG variant for frontend
		$posterFilePath = ImageHandler::save( $poster, $name );

		// frontend URL to save to DB
		$posterUrl = Helpers::getImageUrlFromFilePath( $posterFilePath );

		// save new movie to database
		$pdo = Database::getPdo();

		$sql = $pdo->prepare(
			'INSERT INTO movies ( date, title, tagline, director, release_year, image_url, prompts ) 
			VALUES ( :date, :title, :tagline, :director, :release_year, :image_url, :prompts )'
		);

		$sql->execute(
			[
				'date' => $date,
				'title' => $title,
				'tagline' => $tagline,
				'director' => $director,
				'release_year' => $releaseYear,
				'image_url' => $posterUrl,
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

