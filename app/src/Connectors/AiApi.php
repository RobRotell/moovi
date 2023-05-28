<?php


namespace Moovi\Connectors;


use OpenAI;
use OpenAI\Client as OpenAiClient;


final class AiApi
{
	const MODEL = 'gpt-3.5-turbo';
	const ROLE = 'user';

	private static OpenAiClient $client;


	/**
	 * Create OpenAI client
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	private static function createClient(): void
	{
		self::$client = OpenAI::client( $_ENV['OPENAI_API_KEY'] );
	}


	/**
	 * Get OpenAI client
	 *
	 * @since 0.0.1
	 *
	 * @return OpenAiClient OpenAI client
	 */
	private static function getClient(): OpenAiClient
	{
		if( !isset( self::$client ) || !( self::$client instanceof OpenAiClient ) ) {
			self::createClient();
		}

		return self::$client;
	}


	/**
	 * Do chat completion
	 *
	 * @since 0.0.1
	 * 
	 * @todo Error handling (if we run out of tokens, etc)
	 *
	 * @param string $prompt Prompt to send to OpenAI's chat completion
	 * @return string Prompt content
	 */
	public static function completePrompt( string $prompt ): string
	{
		$client = self::getClient();

		$res = $client->chat()->create(
			[
				'model' 	=> self::MODEL,
				'messages' 	=> [
					[
						'role' 		=> self::ROLE,
						'content' 	=> $prompt, 
					],
				]
			]
		);

		$content = '';

		// todo -- *should* only one, but gotta check them docs
		foreach( $res->choices as $result ) {
			$content = $result->message->content;
		}

		return $content;
	}


	/**
	 * Get image
	 *
	 * @since 0.0.1
	 * 
	 * @todo Error handling (if we run out of tokens, etc)
	 *
	 * @param string $prompt Prompt to send to OpenAI's image generator
	 * @param string $size Image size in pixels
	 * @return string URL
	 */
	public static function generateImage( string $prompt, string $size = '1024x1024' ): string
	{
		$client = self::getClient();

		// default image size: 1024x1024
		$res = $client->images()->create(
			[
				'prompt' => $prompt,
				'size' => $size
			]
		);

		$url = '';

		// todo -- *should* only one, but gotta check them docs
		foreach( $res->data as $data ) {
			$url = $data->url;
		}

		return $url;
	}


}