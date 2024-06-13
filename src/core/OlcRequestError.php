<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\core;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

class OlcRequestError extends \Exception {
	private ClientException $exception;

	public function __construct(ClientException $response) {
		parent::__construct($response->getMessage(), $response->getCode());
		$this->exception = $response;
	}

	/**
	 * Returns response body as an array.
	 * @return mixed
	 */
	public function toArray() {
		return json_decode($this->toString(), true);
	}

	/**
	 * @return array
	 */
	public function getErrorList(): array {
		return $this->toArray()['data']['errors'] ?? [];
	}

	/**
	 * @return mixed
	 */
	public function getErrorMessage(): string {
		return $this->toArray()['message'] ?? '';
	}

	/**
	 * @return mixed
	 */
	public function toString() {
		return $this->exception->getResponse()->getBody()->getContents();
	}

	/**
	 * @return mixed
	 */
	public function getResponse(): ResponseInterface {
		return $this->exception->getResponse();
	}

	/**
	 * @return mixed
	 */
	public function getRequest(): RequestInterface {
		return $this->exception->getRequest();
	}
}
