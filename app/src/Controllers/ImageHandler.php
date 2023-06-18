<?php


namespace Moovi\Controllers;


use finfo;
use GdImage;
use InvalidArgumentException;
use Moovi\Connectors\TinifyApi;
use OpenAI\Resources\Files;
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
	 * @return array Array of file paths for JPG image sizes
	 */
	public static function save( string $url, string $name ): array
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
		
		return self::savePngToJpg( $imgFilePath, $name, true );
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
		// compress PNG (original images are ~3MB) through Tinify
		// $data = TinifyApi::compressPng( $data );

		// save PNG in raw image directory
		$imgFilePath = sprintf( '%s/%s.png', APP_IMG_DIR, $name );
		file_put_contents( $imgFilePath, $data );
		chmod( $imgFilePath, 0644 );
		
		return $imgFilePath;
	}


	/**
	 * Save image as JPG to public
	 *
	 * @since 0.0.1
	 * 
	 * @throws RuntimeException Failed to create GdImage object or failed to save JPG
	 *
	 * @param string|GdImage $image If string, file path; otherwise, GdImage
	 * @param string $name File name
	 * @param int $targetWidth Desired width of image file
	 * @param int $targetHeight Desired height of image file
	 * 
	 * @return string File path to image
	 */
	private static function saveAsJpg( 
		string|GdImage $image, 
		string $name, 
		int $targetWidth = 1024, 
		int $targetHeight = 1024 
	): string
	{
		// todo -- error handler for passing a non-JPG file
		if( is_string( $image ) ) {
			$image = imagecreatefromjpeg( $image );
		}

		$imageWidth = imagesx( $image );
		$imageHeight = imagesy( $image );

		$filePath = sprintf( '%s/media/%s-%sx%s.jpg', PUBLIC_SITE_DIR, $name, $targetWidth, $targetHeight );

		// do we need to resize?
		if( $imageWidth === $targetWidth && $imageHeight === $targetHeight ) {
			$saved = imagejpeg( $image, $filePath, 75 );

		} else {
			$copy = imagecreatetruecolor( $targetWidth, $targetHeight );
			imagecopyresampled( $copy, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $imageWidth, $imageHeight );

			$saved = imagejpeg( $copy, $filePath, 75 );
			imagedestroy( $copy );
		}

		if( !$saved ) {
			throw new RuntimeException( 
				sprintf( 'Failed to save JPG version of image (size: %dx%d', $targetWidth, $targetHeight )
			);
		}
		chmod( $filePath, 0644 );

		// create WebP variant
		self::createWebpVariant( $copy ?? $image, $name, $targetWidth, $targetHeight, filesize( $filePath ) );

		// create AVIF variant
		self::createAvifVariant( $copy ?? $image, $name, $targetWidth, $targetHeight, filesize( $filePath ) );
		
		return $filePath;
	}


	/**
	 * Save image as JPG for frontend
	 *
	 * @since	0.0.1
	 * 
	 * @param string $rawImageFilePath File path for raw image
	 * @param string $name File name
	 * 
	 * @return array Array of file paths
	 */
	public static function savePngToJpg( string $rawImageFilePath, string $name ): array
	{
		// create GD image
		$imageObj = imagecreatefrompng( $rawImageFilePath );
		if( !$imageObj ) {
			throw new RuntimeException( 'Failed to create image object of PNG.' );
		}

		$filePaths = [
			'1024x1024' => self::saveAsJpg( $imageObj, $name, 1024, 1024 ),
			'768x768' => self::saveAsJpg( $imageObj, $name, 768, 768 ),
			'480x480' => self::saveAsJpg( $imageObj, $name, 480, 480 ),
		];

		imagedestroy( $imageObj );

		return $filePaths;
	}


	/**
	 * Create WebP variant of image. 
	 * 
	 * If WebP version is larger than original, then delete WebP variant.
	 *
	 * @since 0.0.2
	 *
	 * @param GdImage $image Original image
	 * @param string $name Image name
	 * @param int $width Image width (for image filename)
	 * @param int $height Image height (for image filename)
	 * @param int $origFileSize File size of original image
	 * 
	 * @return string Variant file path
	 */
	private static function createWebpVariant( 
		GdImage $image, 
		string $name, 
		int $width, 
		int $height, 
		int $origFileSize 
	): string
	{
		$variantFilePath = sprintf( '%s/media/%s-%sx%s.webp', PUBLIC_SITE_DIR, $name, $width, $height );

		imagewebp( $image, $variantFilePath, 75 );

		$variantFileSize = filesize( $variantFilePath );

		if( $variantFileSize >= $origFileSize ) {
			unlink( $variantFilePath );
		}

		chmod( $variantFilePath, 0644 );

		return $variantFilePath;
	}


	/**
	 * Create AVIF variant of image. 
	 * 
	 * If AVIF version is larger than original, then delete AVIF variant.
	 *
	 * @since 0.0.2
	 *
	 * @param GdImage $image Original image
	 * @param string $name Image name
	 * @param int $width Image width (for image filename)
	 * @param int $height Image height (for image filename)
	 * @param int $origFileSize File size of original image
	 * 
	 * @return string Variant file path
	 */
	private static function createAvifVariant( 
		GdImage $image, 
		string $name, 
		int $width, 
		int $height, 
		int $origFileSize 
	): string
	{
		$variantFilePath = sprintf( '%s/media/%s-%sx%s.avif', PUBLIC_SITE_DIR, $name, $width, $height );

		imageavif( $image, $variantFilePath, 75, 0 );

		$variantFileSize = filesize( $variantFilePath );

		if( $variantFileSize >= $origFileSize ) {
			unlink( $variantFilePath );
		}

		chmod( $variantFilePath, 0644 );

		return $variantFilePath;
	}	

}

