<?php


namespace Moovi\Endpoints;


use DateTimeImmutable;
use Moovi\Controllers\Database;
use Moovi\Controllers\MovieHandler;
use Moovi\Models\Response;
use Moovi\Models\Movie;


class GetMovies
{
	private array $movies = [];
	

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
		$date = new DateTimeImmutable( 'America/New_York' );
		$date = $date->format( 'Y-m-d' );

		$movies = MovieHandler::getMoviesByDate( $date );

		if( empty( $movies ) ) {
			$movies = [ MovieHandler::createMovieByDate( $date ) ];
		}

		$this->movies = $movies;
	}

}

