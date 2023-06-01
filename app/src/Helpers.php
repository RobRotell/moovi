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


	/**
	 * Strip leading and trailing quotation marks
	 *
	 * @since 0.0.1
	 *
	 * @param string $input String to remove leading/trailing quotation marks
	 * @return string
	 */
	public static function stripLeadingTrailingQuotationMarks( string $input ): string
	{
		if( str_starts_with( $input, '"' ) ) {
			$input = substr( $input, 1 );
		}

		if( str_ends_with( $input, '"' ) ) {
			$input = substr( $input, 0, -1 );
		}

		return $input;
	}

	
	/**
	 * Strip special characters from string
	 *
	 * @since 0.0.1
	 *
	 * @param string $input String to clean
	 * @return string
	 */
	public static function stripSpecialChars( string $input ): string
	{
		return preg_replace( '/[^A-Za-z0-9]/', '', $input );
	}


	/**
	 * Convert a value to an array of non-zero integers
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $input
	 * @return array
	 */
	public static function convertToIntArray( mixed $input = '' ): array
	{
		if( empty( $input ) ) {
			return [];
		}

		if( is_string( $input ) ) {
			$input = explode( ',', $input );
		}

		$input = (array)$input;

		return array_filter( array_map( function( $value ) {
			return is_numeric( $value ) ? abs( $value ) : false;
		}, $input ) );
	}
}
