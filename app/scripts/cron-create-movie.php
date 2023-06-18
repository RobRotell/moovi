<?php

$appDir = dirname( dirname( __FILE__ ) );

require_once( $appDir . '/vendor/autoload.php' );

// get endpoint authorization code
$dotenv = \Dotenv\Dotenv::createImmutable( $appDir . '/config' );
$dotenv->load();

$curl = curl_init( 'https://api.moovi.robr.app/create-movie' );

curl_setopt( 
   $curl, 
   CURLOPT_POSTFIELDS, 
   [
      'auth' => $_ENV['ENDPOINT_CREATE_MOVIE_AUTH']

   ]
);

curl_exec( $curl );
curl_close( $curl );

exit;
