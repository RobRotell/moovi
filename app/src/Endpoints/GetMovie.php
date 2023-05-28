<?php


namespace Moovi\Endpoints;


use DateTimeImmutable;
use Exception;
use Moovi\Abstracts\Endpoint;
use Moovi\Controllers\MovieHandler;
use Moovi\Models\Response;
use Throwable;


class GetMovie extends Endpoint
{
	public string $name = 'get-movie';
	

	/**
	 * Create endpoint instance
	 * 
	 * @todo Support for passing specific date
	 * @todo Exception handling
	 *
	 * @since 0.0.1
	 */
	public function __construct()
	{
		try {
			// $date = new DateTimeImmutable( 'America/New_York' );
			// $date = $date->format( 'Y-m-d' );
	
			$movies = MovieHandler::getRandomMovies();
	
			if( empty( $movies ) ) {
				$movies = [ MovieHandler::createMovie( /* $date */ ) ];
			}

			$movies = array_map( function( $movie ) {
				return $movie->package();
			}, $movies );
	
			$this->response = new Response(
				200,
				[
					'movies' => $movies,
				]
			);

		} catch( Throwable $err ) {
			$this->response = new Response(
				400,
				[
					'error' => $err->getMessage()
				]
			);
		}
	}

}

