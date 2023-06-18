<?php



final class MooviApp
{
	/**
	 * Create class instance
	 *
	 * @since	0.0.1
	 */
	public function __construct()
	{
		$this->defineConstants();
		$this->loadIncludes();
		$this->loadEnv();
	}


	/**
	 * Define constants
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	private function defineConstants(): void
	{
		define( 'APP_VERSION', '0.0.1' );

		define( 'APP_DIR', __DIR__ );
		define( 'APP_FILE', __FILE__ );

		// for storing raw DALL-E images
		define( 'APP_IMG_DIR', APP_DIR . '/img' );

		define( 'PUBLIC_SITE_DIR', dirname( APP_DIR ) . '/public' );
		define( 'PUBLIC_SITE_URL', 'https://api.moovi.robr.app' );
	}


	/**
	 * Load dependencies and includes
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	private function loadIncludes(): void
	{
		require_once( APP_DIR . '/vendor/autoload.php' );
	}


	/**
	 * Load environment variables
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	private function loadEnv(): void
	{
		$dotenv = \Dotenv\Dotenv::createImmutable( APP_DIR . '/config' );
		$dotenv->load();
	}


	/**
	 * Execute app
	 * 
	 * Right now, we're only processing simple endpoint requests (e.g. "get_movie")
	 *
	 * @since	0.0.1
	 *
	 * @param string $endpoint Endpoint name
	 * @return void
	 */
	public function run( string $endpoint ): void
	{
		\Moovi\Controllers\EndpointHandler::process( $endpoint );
	}

}


return new MooviApp();
