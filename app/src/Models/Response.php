<?php


namespace Moovi\Models;


class Response
{
	private int $code;
	private mixed $data = null;


	/**
	 * Create response object
	 *
	 * @since 0.0.1
	 *
	 * @param int $code Response code
	 * @param mixed $data Response data. Should be array, object, or scalar
	 */
	public function __construct( int $code = 200, mixed $data = '' )
	{
		$this->code = $code;

		if( is_scalar( $data ) || is_array( $data ) || is_object( $data ) ) {
			$this->data = $data;
		}
	}


	/**
	 * Output response to client
	 *
	 * @since 0.0.1
	 *
	 * @param bool $exit True, to terminate script
	 * @return void
	 */
	public function output( bool $exit = true ): void
	{
		header( 'Content-Type: application/json' );
		header( 'X-App: Moovi' );
		
		http_response_code( $this->code );

		echo json_encode( $this->data );

		if( $exit ) {
			exit;
		}
	}

}

