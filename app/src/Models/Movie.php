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
	private ?string $imageUrlOrig;

	private ?string $prompt;

	private $meta = null;


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
		$movie->imageUrlOrig	= $attrs['image_url_orig'] ?? '';
		$movie->prompt 			= $attrs['prompt'] ?? '';
		$movie->meta 			= $attrs['meta'] ?? '';

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

		$result = Database::query(
			'SELECT * FROM movies WHERE `id` = :id LIMIT 1',
			[
				'id' => $id
			]
		);

		if( empty( $result ) ) {
			throw new InvalidArgumentException(
				sprintf( 'Invalid movie ID: "%s"', $id )
			);
		}

		// database will return an array; use first one
		$result = (array)$result[0];

		$movie = new self;

		$movie->id 				= $result['id'];
		$movie->title 			= $result['title'];
		$movie->tagline 		= $result['tagline'];
		$movie->director 		= $result['director'];
		$movie->releaseYear		= $result['release_year'];
		$movie->date 			= $result['date'];
		$movie->imageUrl 		= $result['image_url'];
		$movie->imageUrlOrig	= $result['image_url_orig'];
		$movie->prompt 			= $result['prompt'];
		$movie->meta 			= $result['meta'];

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

