<?php


namespace Moovi\Endpoints;


use DateTimeImmutable;
use Exception;
use Moovi\Abstracts\Endpoint;
use Moovi\Controllers\MovieHandler;
use Moovi\Helpers;
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

			$limit = 1;
			$exclude = null;

			// did user request more than one movie?
			if( isset( $_GET['limit'] ) ) {
				$userInputLimit = abs( $_GET['limit'] );

				// todo -- return error to user if less than 1 or more than five
				if( 0 < $userInputLimit && 5 >= $userInputLimit ) {
					$limit = $userInputLimit;
				}
			}

			// did user want to exclude specific movie?
			if( isset( $_GET['exclude'] ) ) {
				$exclude = Helpers::convertToIntArray( $_GET['exclude'] );
			}
	
			$movies = MovieHandler::getRandomMovies( $limit, $exclude );
	
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

