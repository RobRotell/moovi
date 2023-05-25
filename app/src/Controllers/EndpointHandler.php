<?php


namespace Moovi\Controllers;


use Moovi\Models\Response;
use Moovi\Endpoints\GetMovie;


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
		if( 'get_movie' === $endpoint ) {
			$endpoint = new GetMovie();
			$res = new Response();
			
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

