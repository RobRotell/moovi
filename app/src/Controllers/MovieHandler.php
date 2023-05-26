<?php


namespace Moovi\Controllers;


use Moovi\Models\Movie;
use InvalidArgumentException;
use Moovi\Connectors\Ai;
use Moovi\Datasets\Nationalities;
use Moovi\Datasets\Styles;
use Moovi\Helpers;


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
		$results = Database::query(
			'SELECT * FROM movies WHERE `date` = :date',
			[
				'date' => $date
			]
		);

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
	 *
	 * @param string $date Date in Y-m-d format
	 * @return Movie
	 */
	public static function createMovieByDate( string $date ): Movie
	{
		[
			'title' 	=> $title,
			'tagline'	=> $tagline,
			'poster' 	=> $poster,
			'__meta'	=> $meta,
		] = Prompts::createPrompts();

		// todo -- uncomment when going live
		// $title 		= Ai::completePrompt( $title );
		// $tagline	= Ai::completePrompt( $tagline );
		// $poster 	= Ai::generateImage( $poster );
		// $director 	= Ai::completePrompt( sprintf( 'Create a %s name.', Nationalities::getSkewedRandomValue( 'American', 50 ) ) );
		
		// don't need an AI to make random numbers
		$releaseYear = Helpers::generateRandomYear();

		// test data
		$title 		= '"The Templar Assassins: The Quest for Hidden Riches"';
		$tagline 	= '"Uncovering a treasure will come at a price."';
		$poster 	= 'https://oaidalleapiprodscus.blob.core.windows.net/private/org-AdNFCORFDCHd5SbReNgGfCD1/user-Y3wXqkFf8yR303GxGk5TTsfQ/img-uHEHeeU9bSmhjYfZ8giPFOqE.png?st=2023-05-26T03%3A19%3A03Z&se=2023-05-26T05%3A19%3A03Z&sp=r&sv=2021-08-06&sr=b&rscd=inline&rsct=image/png&skoid=6aaadede-4fb3-4698-a8f6-684d7786b067&sktid=a48cca56-e6da-484e-a814-9c849652bcb3&skt=2023-05-26T00%3A53%3A14Z&ske=2023-05-27T00%3A53%3A14Z&sks=b&skv=2021-08-06&sig=FPF96DX7p7YdaePyJnKVqXPC/%2B8QtkTpXsPtf4ePIR4%3D';
		$director 	= 'Laila Al-Assad';

		$name = sprintf( 
			'%s-%s', 
			$date,  
			sha1( json_encode( $meta ) ),
		);

		$posterFilePath = ImageHandler::save( $poster, $name );

		// 2023-05-26 :: Continue from here
		/**
		 * - save movie
		 */
	}

}

