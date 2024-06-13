<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\api;

use Olc\core\BaseApi;


class ProductApi extends BaseApi {
	/**
	 * Get products history
	 * <code>
	 * <?php
	 *   $response = $olc->products()->all();
	 * ?>
	 * </code>
	 *
	 * @param array $params Parameters to send with the request.
	 * @return array{message: string, data: array} The response data.
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function all(array $params = []): array {
		$request = $this->getInstance()->getRequest();
		$query = \count($params) ? '?' . \http_build_query($params) : '';
		$response = $request->get('/products' . $query, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Get product types
	 * <code>
	 * <?php
	 *   $response = $olc->products()->productTypes();
	 * ?>
	 * </code>
	 *
	 * @param array $params Parameters to send with the request.
	 * @return array{message: string, data: array} The response data.
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function productTypes(array $params = []): array {
		$request = $this->getInstance()->getRequest();
		$query = \count($params) ? '?' . \http_build_query($params) : '';
		$response = $request->get('/products/types' . $query, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Get product details by product type details
	 * <code>
	 * <?php
	 *   $response = $olc->products()->getDetailsByType('Personal Letters');
	 * ?>
	 * </code>'
	 *
	 * @param string $productType Product type
	 * @return array{message: string, data: array} The response data.
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function getDetailsByType(string $productType): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->post('/products/details', [
			'productType' => $productType,
		], [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Get product template details
	 * <code>
	 * <?php
	 *   $response = $olc->products()->getTemplateById(87);
	 * ?>
	 * </code>'
	 *
	 * @param int $templateId The template ID
	 * @return array{message: string, data: array} The response data.
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function getTemplateById(int $templateId): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->post('/products/template/details', [
			'templateId' => $templateId,
		], [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}
}
