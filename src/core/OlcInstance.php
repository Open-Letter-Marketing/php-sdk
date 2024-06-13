<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\core;

use Olc\api\{OrderDetailsApi, OrdersApi, ProductApi, UserApi, CustomFieldsApi, TemplatesApi};

class OlcInstance {
	/**
	 * @var array<string, object>
	 */
	protected array $registry = [];

	private OlcRequest $request;

	public function __construct(string $apiKey, string|null $version = null, string|null $endpoint = null) {
		$this->request = new OlcRequest($version, $endpoint);
		$this->request->setApiKey($apiKey);
	}

	/**
	 * Retrieve an API instance by class (FQN) from the registry. If not found, create a new instance.
	 * @param class-string $className API FQN class name
	 * @return mixed API instance
	 */
	protected function retrieveApiInstance(string $className): mixed {
		if (!array_key_exists($className, $this->registry)) {
			$instance = new $className($this);
			$this->registry[$className] = $instance;
		}

		return $this->registry[$className];
	}

	/**
	 * Get the request instance.
	 * @return OlcRequest
	 */
	public function getRequest(): OlcRequest {
		return $this->request;
	}

	/**
	 * Get the User instance.
	 * @return UserApi
	 */
	public function user(): UserApi {
		return $this->retrieveApiInstance(UserApi::class);
	}

	/**
	 * Get the CustomFields instance.
	 * @return CustomFieldsApi
	 */
	public function customFields(): CustomFieldsApi {
		return $this->retrieveApiInstance(CustomFieldsApi::class);
	}

	/**
	 * Get the Templates instance.
	 * @return TemplatesApi
	 */
	public function templates(): TemplatesApi {
		return $this->retrieveApiInstance(TemplatesApi::class);
	}

	/**
	 * Get the ProductApi instance.
	 * @return ProductApi
	 */
	public function products(): ProductApi {
		return $this->retrieveApiInstance(ProductApi::class);
	}

	/**
	 * Get the OrdersApi instance.
	 * @return OrdersApi
	 */
	public function orders(): OrdersApi {
		return $this->retrieveApiInstance(OrdersApi::class);
	}

	/**
	 * Get the OrderDetailsApi instance.
	 * @return OrderDetailsApi
	 */
	public function orderDetails(): OrderDetailsApi {
		return $this->retrieveApiInstance(OrderDetailsApi::class);
	}
}
