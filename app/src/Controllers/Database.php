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
	private static function createConn(): void
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
	private static function getConn(): PDO
	{
		if( !isset( self::$conn ) || !( self::$conn instanceof PDO ) ) {
			self::createConn();
		}

		return self::$conn;
	}


	/**
	 * Perform SQL query
	 *
	 * @since	0.0.1
	 *
	 * @param string $sql Base SQL query
	 * @param array $vars Variables to prepare
	 * 
	 * @return mixed Query results
	 */
	public static function query( string $sql, array $vars = [] ): mixed
	{
		$conn = self::getConn();

		$statement = $conn->prepare( $sql );
		
		if( !empty( $vars ) ) {
			$statement->execute( $vars );
		}

		return $statement->fetchAll();
	}
}

