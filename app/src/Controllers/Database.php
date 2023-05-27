<?php


namespace Moovi\Controllers;


use PDO;


final class Database
{
	private static ?PDO $conn;


	/**
	 * Create instance
	 * 
	 * @todo Exception handler for failed connection
	 *
	 * @since 0.0.1
	 * 
	 * @return void
	 */
	private static function createPdo(): void
	{
		self::$conn = new PDO( 
			sprintf( 'mysql:dbname=%s;host=localhost', $_ENV['DB_NAME'] ),
			$_ENV['DB_USER'],
			$_ENV['DB_PASS'],
			[
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			]
		);
	}


	/**
	 * Fetch movie by date
	 *
	 * @since 0.0.1
	 *
	 * @return PDO Database connection
	 */
	public static function getPdo(): PDO
	{
		if( !isset( self::$conn ) || !( self::$conn instanceof PDO ) ) {
			self::createPdo();
		}

		return self::$conn;
	}

}

