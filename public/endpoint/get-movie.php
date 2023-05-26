<?php


define( 'APP_DEBUG', true );


if( defined( 'APP_DEBUG' ) && APP_DEBUG ) {
	ini_set( 'display_errors', 1 );
	ini_set( 'display_startup_errors', 1 );
	error_reporting( E_ALL );
}


// load Moovi app, including endpoints
$app = require_once( __DIR__ . '/../../app/app.php' );

// execute app
$app->run( 'get_movie' );

exit;