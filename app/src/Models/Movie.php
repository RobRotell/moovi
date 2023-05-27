<?php


namespace Moovi\Models;


use InvalidArgumentException;
use Moovi\Controllers\Database;


class Movie
{
	private ?int $id;

	private ?string $title;
	private ?string $tagline;
	private ?string $director;
	
	private ?string $date;
	private ?string $releaseYear;
	
	private ?string $imageUrl;

	private ?array $prompts;


	/**
	 * Factory method to create new movie object from provided attributes
	 *
	 * @since 0.0.1
	 * 
	 * @throws InvalidArgumentException Missing required properties
	 *
	 * @param array $movie Movie attributes
	 * @return Movie
	 */
	public static function createFromAttrs( array $attrs ): Movie
	{
		$movie = new self;

		$movie->id 				= $attrs['id'] ?? 0;
		$movie->title 			= $attrs['title'] ?? '';
		$movie->tagline 		= $attrs['tagline'] ?? '';
		$movie->director 		= $attrs['director'] ?? '';
		$movie->date 			= $attrs['date'] ?? '';
		$movie->releaseYear		= $attrs['release_year'] ?? '';
		$movie->imageUrl 		= $attrs['image_url'] ?? '';
		$movie->prompts 		= json_decode( $attrs['prompts'] ?? '', true );

		// check for required props
		$missingProps = [];
		foreach( [ 'title', 'tagline', 'imageUrl', 'director', 'releaseYear' ] as $prop ) {
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
		$movie->title 			= $result->title;
		$movie->tagline 		= $result->tagline;
		$movie->director 		= $result->director;
		$movie->releaseYear		= $result->release_year;
		$movie->date 			= $result->date;
		$movie->imageUrl 		= $result->image_url;
		$movie->prompts 		= json_decode( $result->prompts, true );

		return $movie;
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
			'title' 	=> $this->title,
			'tagline' 	=> $this->tagline,
			'director' 	=> $this->director,
			'year' 		=> $this->releaseYear,
			'image' 	=> $this->imageUrl,
		];
	}

}

