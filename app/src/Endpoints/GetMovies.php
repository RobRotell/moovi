<?php


namespace Moovi\Endpoints;


use DateTimeImmutable;
use Exception;
use Moovi\Abstracts\Endpoint;
use Moovi\Controllers\MovieHandler;
use Moovi\Helpers;
use Moovi\Models\Response;
use Throwable;


class GetMovies extends Endpoint
{
	public string $name = 'get-movies';
	

	/**
	 * Create endpoint instance
	 * 
	 * @todo Add paging
	 *
	 * @since 0.0.2
	 */
	public function __construct()
	{
		try {
			$movies = MovieHandler::getAllMovies();
	
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

