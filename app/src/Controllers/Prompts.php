<?php


namespace Moovi\Controllers;


use Moovi\Datasets\Characters;
use Moovi\Datasets\Genres;
use Moovi\Datasets\Nationalities;
use Moovi\Datasets\Plots;
use Moovi\Datasets\Styles;


final class Prompts
{
	/**
	 * Create a title and tagline prompt for OpenAI
	 *
	 * @since 0.0.1
	 *
	 * @return array Contains prompts for title. tagline, and poster
	 */
	public static function createPrompts(): array
	{
		$genre = strtolower( Genres::getRandomValue() );
		$scenario = strtolower( self::createScenario() );
		$style = strtolower( Styles::getSkewedRandomValue( 'realistic', 5 ) );

		return [
			'title' => sprintf( 'Create a %s movie title between three and seven words for a %s movie about %s', $style, $genre, $scenario ),
			'tagline' => sprintf( 'Create a %s movie tagline for an %s movie about %s', $style, $genre, $scenario ),
			'poster' => sprintf( '%s image for a %s movie about %s', $style, $genre, $scenario ),
			'director' => sprintf( 'Create a %s name between two and four words.', Nationalities::getSkewedRandomValue( 'American', 200 ) ),
			'__meta' => [
				'genre' => $genre,
				'scenario' => $scenario,
				'style' => $style,
			]
		];
	}


	/**
	 * Create scenario from datasets
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public static function createScenario(): string
	{
		return sprintf(
			'%s %s',
			Characters::getRandomValue(),
			Plots::getRandomValue(),
		);
	}

}
