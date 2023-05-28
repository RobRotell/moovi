<?php


namespace Moovi\Connectors;

use Throwable;
use Tinify\Tinify;


final class TinifyApi
{
	/**
	 * Set API key for Tinify
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	private static function setApiKey(): void
	{
		\Tinify\setKey( $_ENV['TINIFY_API_KEY'] );
	}


	/**
	 * Get remaining monthly compression count
	 *
	 * @since 0.0.1
	 * 
	 * @deprecated Deprecated in favor of using (and ignoring) AccountException instead
	 *
	 * @return int|null Remaining compression count (if at least one compression used); otherwise null (no compressions used yet that month)
	 */
	private static function getCompressionCount(): int|null
	{
		self::setApiKey();

		return \Tinify\getCompressionCount();
	}


	/**
	 * Compress PNG file
	 *
	 * @since 0.0.1
	 *
	 * @param string $data Image data
	 * @return string Compressed image data
	 */
	public static function compressPng( string $data ): string
	{
		// if no more compressions left, just return original data
		// $count = self::getCompressionCount();
		// if( !empty( $count ) ) {
		// 	return $data;
		// }

		try {
			// initialize API key
			self::setApiKey();

			$compressedData = \Tinify\fromBuffer( $data )->toBuffer();
			if( !empty( $compressedData ) ) {
				$data = $compressedData;
			}

		} catch ( Throwable $err ) {
			// todo -- add logging?
		}

		return $data;
	}

}