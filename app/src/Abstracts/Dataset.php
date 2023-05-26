<?php


namespace Moovi\Abstracts;


class Dataset
{
	public static $values;


	/**
	 * Get random value
	 *
	 * @since 0.0.1
	 *
	 * @return string 
	 */
	public static function getRandomValue(): string
	{
		return static::$values[ array_rand( static::$values ) ];
	}


	/**
	 * Get skewed random value
	 *
	 * @since 0.0.1
	 *
	 * @param string $skewValue Value to add skewed weight to increase likelihood of selection
	 * @param int $count Number of times skew value should be in values
	 * 
	 * @return string
	 */
	public static function getSkewedRandomValue( string $skewValue, int $count ): string
	{
		$values = array_filter( static::$values, fn( $value ) => $value !== $skewValue );
		$values = array_merge( $values, array_fill( 0, $count, $skewValue ) );

		return $values[ array_rand( $values ) ];
	}

}
