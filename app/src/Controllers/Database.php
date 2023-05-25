<?php


namespace Moovi\Controllers;


use PDO;


final class Database
{
	private static $instance = null;

	private PDO $conn;


	/**
	 * Get instance of class
	 *
	 * @since 0.0.1
	 *
	 * @return \Moovi\Controllers\Database
	 */
	public static function getInstance(): self
	{
		if( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * Create instance
	 *
	 * @since 0.0.1
	 */
	private function __construct()
	{
		$this->conn = new PDO( 
			sprintf( 'mysql:dbname=%s;host=localhost', $_ENV['DB_NAME'] ),
			$_ENV['DB_USER'],
			$_ENV['DB_PASS']
		);
	}
}

