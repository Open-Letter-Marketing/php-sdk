<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Peridot\ObjectPath\ObjectPath;
use Psr\Http\Message\ResponseInterface;

class OlcRequest {
	protected ?Client $client = null;

	protected ?string $version = null;
	protected ?string $endpoint = null;

	protected string $apiKey;

	public function __construct(?string $version = null, ?string $endpoint = null) {
		$this->version = $version;
		$this->endpoint = $endpoint;
		$this->client = new Client();
	}

	/**
	 * Set the API key.
	 * @param string $apiKey
	 * @return void
	 */
	public function setApiKey(string $apiKey): void {
		$this->apiKey = $apiKey;
	}

	/**
	 * Get the API key.
	 * @return string
	 */
	public function getApiKey(): string {
		return $this->apiKey;
	}

	public function toBaseUrl(string ...$routes): string {
		$baseUrl = rtrim($this->endpoint ?? 'https://api.openletterconnect.com', '/');
		$endUrl = implode('/', ['api', $this->version ?? 'v1', ...$routes]);
		return sprintf("%s/%s", $baseUrl, str_replace('//', '/', $endUrl));
	}

	protected function initOptions(array $options = []): array {
		$headers = [];

		if ($options['jsonHeaders'] ?? true) {
			$headers['Content-Type'] = 'application/json; charset=utf-8';
			$headers['Accept'] = 'application/json; charset=utf-8';
		}

		if ($options['authorize'] ?? false) {
			$headers['Authorization'] = "Bearer {$this->getApiKey()}";
		}

		$options['headers'] = $headers;
		return $options;
	}

	/**
	 * Send a POST request to the API.
	 * @param string $route (required) The route to be called.
	 * @param array<string, mixed> $data (optional) Data to be sent with the request
	 * @param array<string, mixed> $options (optional) Additional options pass to the request
	 * @param string $options ['headers'] (optional) Key-value pairs of headers to be sent with the reque
	 * @param string $options ['jsonHeaders'] (optional) Add JSON (Content-Type, Accept) headers to the request
	 * @param boolean $options ['authorize'] (optional) If true, the request will initiate with the API keyst
	 * @param array $options ['client'] (optional) Client options pass to the Guzzle
	 * @return mixed The response from the API.
	 * @throws OlcRequestError if the request fails
	 */
	public function post(string $route, ?array $data = [], array $options = []): ResponseInterface {
		$options = $this->initOptions($options);

		$clientOptions = array_merge_recursive([
			'headers' => $options['headers'] ?? [],
			'verify' => Environment::sslVerify(),
		], $options['client'] ?? []);

		if ($data !== null) {
			$clientOptions['body'] = \count($data) ? \json_encode($data) : null;
		}

		try {
			return $this->getClient()->post($this->toBaseUrl($route), $clientOptions);
		} catch (ClientException $e) {
			throw new OlcRequestError($e);
		}
	}

	/**
	 * Send a PATCH request to the API.
	 * @param string $route (required) The route to be called.
	 * @param array<string, mixed> $data (optional) Data to be sent with the request
	 * @param array<string, mixed> $options (optional) Additional options pass to the request
	 * @param string $options ['headers'] (optional) Key-value pairs of headers to be sent with the reque
	 * @param string $options ['jsonHeaders'] (optional) Add JSON (Content-Type, Accept) headers to the request
	 * @param boolean $options ['authorize'] (optional) If true, the request will initiate with the API keyst
	 * @param array $options ['client'] (optional) Client options pass to the Guzzle
	 * @return mixed The response from the API.
	 * @throws OlcRequestError if the request fails
	 */
	public function patch(string $route, ?array $data = null, array $options = []): ResponseInterface {
		$options = $this->initOptions($options);

		$clientOptions = array_merge_recursive([
			'headers' => $options['headers'] ?? [],
			'verify' => Environment::sslVerify(),
		], $options['client'] ?? []);

		if ($data !== null) {
			$clientOptions['body'] = \count($data) ? \json_encode($data) : null;
		}

		try {
			return $this->getClient()->patch($this->toBaseUrl($route), $clientOptions);
		} catch (ClientException $e) {
			throw new OlcRequestError($e);
		}
	}

	/**
	 * Send a PUT request to the API.
	 * @param string $route (required) The route to be called.
	 * @param array<string, mixed> $data (optional) Data to be sent with the request
	 * @param array<string, mixed> $options (optional) Additional options pass to the request
	 * @param string $options ['headers'] (optional) Key-value pairs of headers to be sent with the reque
	 * @param string $options ['jsonHeaders'] (optional) Add JSON (Content-Type, Accept) headers to the request
	 * @param boolean $options ['authorize'] (optional) If true, the request will initiate with the API keyst
	 * @param array $options ['client'] (optional) Client options pass to the Guzzle
	 * @return mixed The response from the API.
	 * @throws OlcRequestError if the request fails
	 */
	public function put(string $route, ?array $data = null, array $options = []): ResponseInterface {
		$options = $this->initOptions($options);

		$clientOptions = array_merge_recursive([
			'headers' => $options['headers'] ?? [],
			'verify' => Environment::sslVerify(),
		], $options['client'] ?? []);

		if ($data !== null) {
			$clientOptions['body'] = \count($data) ? \json_encode($data) : null;
		}

		try {
			return $this->getClient()->put($this->toBaseUrl($route), $clientOptions);
		} catch (ClientException $e) {
			throw new OlcRequestError($e);
		}
	}

	/**
	 * Send a GET request to the API.
	 * @param string $route (required) The route to be called.
	 * @param array<string, mixed> $options (optional) Additional options pass to the request
	 * @param string $options ['headers'] (optional) Key-value pairs of headers to be sent with the request
	 * @param boolean $options ['authorize'] (optional) If true, the request will initiate with the API key
	 * @return mixed The response from the API.
	 * @throws OlcRequestError if the request fails
	 */
	public function get(string $route, array $options = []): ResponseInterface {
		$options = $this->initOptions($options);

		try {
			return $this->getClient()->get($this->toBaseUrl($route), [
				'headers' => $options['headers'] ?? [],
				'verify' => Environment::sslVerify(),
			]);
		} catch (ClientException $e) {
			throw new OlcRequestError($e);
		}
	}

	/**
	 * Send a DELETE request to the API.
	 * @param string $route (required) The route to be called.
	 * @param array<string, mixed> $options (optional) Additional options pass to the request
	 * @param string $options ['headers'] (optional) Key-value pairs of headers to be sent with the request
	 * @param boolean $options ['authorize'] (optional) If true, the request will initiate with the API key
	 * @return mixed The response from the API.
	 * @throws OlcRequestError if the request fails
	 */
	public function delete(string $route, array $options = []): ResponseInterface {
		$options = $this->initOptions($options);

		try {
			return $this->getClient()->delete($this->toBaseUrl($route), [
				'headers' => $options['headers'] ?? [],
				'verify' => Environment::sslVerify(),
			]);
		} catch (ClientException $e) {
			throw new OlcRequestError($e);
		}
	}

	public function retrieveValueByPath(ResponseInterface $response, ?string $key = null) {
		$body = json_decode($response->getBody()->getContents(), true);

		if (!$key) {
			return $body;
		}

		$accessor = (new ObjectPath ($body))->get($key);

		return is_object($accessor) && method_exists($accessor, 'getPropertyValue')
			? $accessor->getPropertyValue()
			: false;
	}

	public function getClient(): Client {
		return $this->client;
	}
}
