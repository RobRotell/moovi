<?php


namespace Moovi\Models;


use InvalidArgumentException;
use Moovi\Controllers\Database;
use Moovi\Helpers;

class Movie
{
	private ?int $id;

	private ?string $title;
	private ?string $tagline;
	private ?string $director;
	private ?string $genre;
	
	private ?string $date;
	private ?string $releaseYear;
	
	private ?array $imageUrls;

	private ?array $prompts;


	/**
	 * Create movie object
	 *
	 * @since 0.0.2
	 * 
	 * @throws InvalidArgumentException Missing required properties
	 *
	 * @param int $id Movie ID
	 */
	public function __construct( int $id ) 
	{
		$id = abs( $id );

		$pdo = Database::getPdo();
		$sql = $pdo->prepare( 'SELECT * FROM movies WHERE `id` = :id LIMIT 1' );
		$sql->execute(
			[
				'id' => $id,
			]
		);

		$result = $sql->fetch();

		if( empty( $result ) ) {
			throw new InvalidArgumentException(
				sprintf( 'Invalid movie ID: "%s"', $id )
			);
		}

		$this->id 				= $result->id;
		$this->title 			= Helpers::stripLeadingTrailingQuotationMarks( $result->title );
		$this->tagline 			= Helpers::stripLeadingTrailingQuotationMarks( $result->tagline );
		$this->director 		= $result->director;
		$this->genre 			= $result->genre;
		$this->releaseYear		= $result->release_year;
		$this->date 			= $result->date;
		$this->imageUrls 		= json_decode( $result->image_urls, true );
		$this->prompts 			= json_decode( $result->prompts, true );
	}


	/**
	 * Package movie data for frontend
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function package(): array
	{
		return [
			'id'		=> $this->id,
			'title' 	=> $this->title,
			'tagline' 	=> $this->tagline,
			'director' 	=> $this->director,
			'genre'		=> $this->genre,
			'year' 		=> $this->releaseYear,
			'image' 	=> $this->imageUrls,
		];
	}	


	/**
	 * Factory method to create new movie object from provided attributes
	 *
	 * @since 0.0.1
	 * 
	 * @throws InvalidArgumentException Missing required properties
	 * 
	 * @deprecated Use constructor instead
	 *
	 * @param array $movie Movie attributes
	 * @return Movie
	 */
	public static function createFromAttrs( array $attrs ): Movie
	{
		$movie = new self;

		$movie->id 				= $attrs['id'] ?? 0;
		$movie->title 			= Helpers::stripLeadingTrailingQuotationMarks( $attrs['title'] ?? '' );
		$movie->tagline 		= Helpers::stripLeadingTrailingQuotationMarks( $attrs['tagline'] ?? '' );
		$movie->director 		= $attrs['director'] ?? '';
		$movie->genre 			= $attrs['genre'] ?? '';
		$movie->date 			= $attrs['date'] ?? '';
		$movie->releaseYear		= $attrs['release_year'] ?? '';
		$movie->imageUrls 		= json_decode( $attrs['image_urls'] ?? '', true );
		$movie->prompts 		= json_decode( $attrs['prompts'] ?? '', true );

		// check for required props
		$missingProps = [];
		foreach( [ 'title', 'tagline', 'imageUrls', 'director', 'releaseYear' ] as $prop ) {
			if( !isset( $movie->{$prop} ) ) {
				$missingProps[] = $prop;
			}
		}

		if( !empty( $missingProps ) ) {
			throw new InvalidArgumentException(
				sprintf( 
					'Movie attributes must contain the following props: %s',
					implode( ', ', array_map( fn( $prop ) => sprintf( '"%s"', $prop ), $missingProps ) )
				)
			);
		}

		return $movie;
	}


	/**
	 * Factory method to create new movie object from movie ID
	 *
	 * @since 0.0.1
	 * 
	 * @throws InvalidArgumentException Missing required properties
	 * 
	 * @deprecated Use constructor instead
	 *
	 * @param int $id Movie ID
	 * @return Movie
	 */
	public static function createFromId( int $id ): Movie
	{
		$id = abs( $id );

		$pdo = Database::getPdo();

		$sql = $pdo->prepare( 'SELECT * FROM movies WHERE `id` = :id LIMIT 1' );
		$sql->execute(
			[
				'id' => $id,
			]
		);

		$result = $sql->fetch();

		if( empty( $result ) ) {
			throw new InvalidArgumentException(
				sprintf( 'Invalid movie ID: "%s"', $id )
			);
		}

		$movie = new self;

		$movie->id 				= $result->id;
		$movie->title 			= Helpers::stripLeadingTrailingQuotationMarks( $result->title );
		$movie->tagline 		= Helpers::stripLeadingTrailingQuotationMarks( $result->tagline );
		$movie->director 		= $result->director;
		$movie->genre 			= $result->genre;
		$movie->releaseYear		= $result->release_year;
		$movie->date 			= $result->date;
		$movie->imageUrls 		= json_decode( $result->image_urls, true );
		$movie->prompts 		= json_decode( $result->prompts, true );

		return $movie;
	}	

}

