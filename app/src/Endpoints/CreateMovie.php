<?php


namespace Moovi\Endpoints;


use Exception;
use Moovi\Abstracts\Endpoint;
use Moovi\Controllers\MovieHandler;
use Moovi\Helpers;
use Moovi\Models\Movie;
use Moovi\Models\Response;
use Throwable;


class CreateMovie extends Endpoint
{
	public string $name = 'create-movie';
	

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
			// validate request
			$hash = Helpers::stripSpecialChars( $_POST['auth'] ?? '' );

			if( $hash !== $_ENV['ENDPOINT_CREATE_MOVIE_AUTH'] ) {
				throw new Exception( 'Invalid authorization code.' );
			}

			// OpenAI can take some time to create the image
			ini_set( 'max_execution_time', 120 );

			$movie = MovieHandler::createMovie();
			$movie = $movie->package();
	
			$this->response = new Response(
				200,
				[
					'movie' => $movie,
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
