<?php


namespace Moovi\Controllers;


use Moovi\Models\Response;
use Moovi\Endpoints\GetMovie;
use Moovi\Models\Movie;


class MovieHandler
{
	/**
	 * Get movie by date
	 *
	 * @since 0.0.1
	 *
	 * @param string $date Date in Y-m-d format
	 * @return Movie|null Movie, if found for date. Otherwise, null
	 */
	public static function getMovieByDate( string $date ): Movie|null
	{
		return null;
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
		$movie = new Movie;

		return $movie;
	}

}

