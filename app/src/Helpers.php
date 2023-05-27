<?php


namespace Moovi;


use DateTime;
use InvalidArgumentException;

class Helpers
{
	/**
	 * Quick-n-dirty date validator
	 *
	 * @since 0.0.1
	 *
	 * @param string $date Date to validate
	 * @param string $format Date format
	 * 
	 * @return bool True, if valid
	 */
	public static function validateDate( string $dateRaw, string $format = 'YYYY-MM-DD' ): bool
	{
		$datetime = DateTime::createFromFormat( $format, $dateRaw );

		return $datetime && $datetime->format( $format ) === $dateRaw;
	}


	/**
	 * Generate random release year
	 *
	 * @since 0.0.1
	 *
	 * @return int Year
	 */
	public static function generateRandomYear(): int
	{
		$yearRange = range( 1976, (int)date( 'Y' ) );

		return $yearRange[ array_rand( $yearRange ) ];
	}


	/**
	 * Convert image file path to URL
	 *
	 * @since 0.0.1
	 * 
	 * @throws InvalidArgumentException Invalid image file path 
	 *
	 * @param string $filePath Image file path
	 * @return string Image URL
	 */
	public static function getImageUrlFromFilePath( string $filePath ): string
	{
		if( !is_file( $filePath ) ) {
			throw new InvalidArgumentException( 'Argument is not a valid file.' );
		}

		return str_replace( PUBLIC_SITE_DIR, PUBLIC_SITE_URL, $filePath );
	}
}
