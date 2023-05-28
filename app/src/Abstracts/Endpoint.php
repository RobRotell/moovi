<?php


namespace Moovi\Abstracts;


use Moovi\Models\Response;


abstract class Endpoint
{
	public string $name;
	public Response $response;


	/**
	 * Get response for endpoint
	 *
	 * @since 0.0.1
	 *
	 * @return Response
	 */
	public function getResponse(): Response
	{
		return $this->response;
	}

}


