<?php


namespace Moovi\Endpoints;


use DateTimeImmutable;
use Moovi\Controllers\MovieHandler;
use Moovi\Models\Response;
use Moovi\Models\Movie;


class GetMovie
{
	private Movie $movie;
	

	/**
	 * Create endpoint instance
	 * 
	 * @todo Support for passing specific date
	 *
	 * @since 0.0.1
	 */
	public function __construct()
	{
		$date = new DateTimeImmutable( 'America/New_York' );
		$date = $date->format( 'Y-m-d' );

		$movie = MovieHandler::getMovieByDate( $date );

		if( empty( $movie ) ) {
			$movie = MovieHandler::createMovieByDate( $date );
		}

		$this->movie = $movie;
	}

}

