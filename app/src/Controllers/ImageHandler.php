<?php


namespace Moovi\Controllers;


use finfo;
use GdImage;
use InvalidArgumentException;
use RuntimeException;


final class ImageHandler
{
	private static array $allowedHosted = [
		'oaidalleapiprodscus.blob.core.windows.net',
	];


	/**
	 * Save image from DALL-E
	 *
	 * @since 0.0.1
	 * 
	 * @throws InvalidArgumentException Invalid host
	 * @throws RunTimeException URL is not for PNG, can't convert from PNG to JPG
	 *
	 * @param string $url Image URL
	 * @param string $name File name to save image
	 * 
	 * @return string String for local media URL
	 */
	public static function save( string $url, string $name ): string
	{
		if( !in_array( parse_url( $url, PHP_URL_HOST ), self::$allowedHosted ) ) {
			throw new InvalidArgumentException( 'Cannot download an image from an invalid host.' );
		}

		$imageData = file_get_contents( $url );

		// try to guess mime type
		$fileInfo = new finfo( FILEINFO_MIME_TYPE );
		if( 'image/png' !== $fileInfo->buffer( file_get_contents( $url ) ) ) {
			throw new RuntimeException( 'Cannot confirm that remote URL is for a PNG.' );
		}
		unset( $fileInfo );
		
		$imgFilePath = self::saveAsPng( $imageData, $name );
		
		return self::saveAsJpg( $imgFilePath, $name );
	}


	/**
	 * Save image as PNG to app storage
	 *
	 * @since 0.0.1
	 *
	 * @param string $data Image data
	 * @param string $name File name
	 * 
	 * @return string File path to image
	 */
	private static function saveAsPng( string $data, string $name ): string
	{
		// save PNG in raw image directory
		$imgFilePath = sprintf( '%s/%s.png', APP_IMG_DIR, $name );
		
		file_put_contents( $imgFilePath, $data );
		chmod( $imgFilePath, 0400 );
		
		return $imgFilePath;
	}


	/**
	 * Save image as JPG for frontend
	 *
	 * @since	0.0.1
	 * 
	 * @throws RuntimeException Failed to create GdImage object or failed to save JPG
	 *
	 * @param string $rawImageFilePath File path for raw image
	 * @param string $name File name
	 * 
	 * @return string File path to image
	 */
	public static function saveAsJpg( string $rawImageFilePath, string $name ): string
	{
		// create GD image
		$imageObj = imagecreatefrompng( $rawImageFilePath );
		if( !$imageObj ) {
			throw new RuntimeException( 'Failed to create image object of PNG.' );
		}

		// file path for public-facing image 
		$publicFilePath = sprintf( '%s/media/%s.jpg', PUBLIC_SITE_DIR, $name );

		// save PNG image as JPG
		$saved = imagejpeg( $imageObj, $publicFilePath, 75 );
		if( !$saved ) {
			throw new RuntimeException( 'Failed to save JPG version of image.' );
		}
		chmod( $publicFilePath, 0644 );

		imagedestroy( $imageObj );
		
		return $publicFilePath;		
	}

}

