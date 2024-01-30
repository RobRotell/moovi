<?php


namespace Moovi\Controllers;


use Moovi\Endpoints\CreateMovie;
use Moovi\Models\Response;
use Moovi\Endpoints\GetMovie;
use Moovi\Endpoints\GetMovies;


class EndpointHandler
{
	/**
	 * Process incoming request
	 *
	 * @since 0.0.1
	 *
	 * @param string $endpoint Endpoint name
	 * @return void
	 */
	public static function process( string $endpoint ): void
	{
		if( 'get-movie' === $endpoint || 'get-movie' === $endpoint ) {
			$endpoint = new GetMovie();
			$res = $endpoint->getResponse();

		} elseif( 'get-movies' === $endpoint ) {
			$endpoint = new GetMovies();
			$res = $endpoint->getResponse();

		} elseif( 'create-movie' === $endpoint ) {
			$endpoint = new CreateMovie();
			$res = $endpoint->getResponse();			
			
		} else {
			$res = new Response( 
				400, 
				[
					'error' => 'Invalid endpoint name.'
				]
			);
		}

		$res->output();
	}

}

