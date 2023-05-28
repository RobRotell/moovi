<?php

require_once( __DIR__ . '/vendor/autoload.php' );

// get endpoint authorization code
$dotenv = \Dotenv\Dotenv::createImmutable( __DIR__ . '/config' );
$dotenv->load();

$curl = curl_init( 'https://moovi.robr.app/endpoints/create-movie' );

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
