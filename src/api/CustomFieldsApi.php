<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\api;

use Olc\core\BaseApi;
use Olc\errors\InvalidConfigException;

class CustomFieldsApi extends BaseApi {
	/**
	 * Fetch all custom fields
	 * <code>
	 * <?php
	 *   $response = $olc->customFields()->all();
	 * ?>
	 * </code>
	 *
	 * @param array<string, mixed> $params Parameters to send with the request.
	 * @return array{
	 *     message: string,
	 *     data: array{
	 *       key: string,
	 *       value: string,
	 *       defaultValue: string,
	 *       strict: bool}
	 * } The response data.
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function all(array $params = []): array {
		$request = $this->getInstance()->getRequest();
		$query = \count($params) ? '?' . \http_build_query($params) : '';
		$response = $request->get('/custom-fields' . $query, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Fetch all paginated custom fields
	 * <code>
	 * <?php
	 *   $response = $olc->customFields()->allPaginated([
	 *     //'page' => 1,
	 *     //'perPage' => 10,
	 *   ]);
	 * ?>
	 * </code>
	 *
	 * @param array{page?: int, perPage?: int} $params Parameters to send with the request.
	 * @return array The response data.
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function allPaginated(array $params = []): array {
		$request = $this->getInstance()->getRequest();
		$query = \count($params) ? '?' . \http_build_query($params) : '';
		$response = $request->get('/custom-fields/get' . $query, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Get custom field by the given id
	 * <code>
	 * <?php
	 *   $response = $olc->customFields()->get(1);
	 * ?>
	 * </code>
	 *
	 * @param int $id The custom field id
	 * @return array The response data.
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function get(int $id): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->get("/custom-fields/$id", [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Removes custom field by the given id
	 * <code>
	 * <?php
	 *   $response = $olc->customFields()->delete(1);
	 * ?>
	 * </code>
	 *
	 * @param int $id The custom field id
	 * @return array The response data.
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function delete(int $id): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->delete("/custom-fields/$id", [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Create a custom field
	 * <code>
	 * <?php
	 *   $response = $olc->customFields()->create('Wife Name');
	 * ?>
	 * </code>
	 * @param string $name The custom field name
	 * @return array{
	 *   message: string,
	 *   data: array{
	 *     id: int,
	 *     key: string,
	 *     value: string,
	 *     defaultValue: string,
	 *     isLiveMode: bool,
	 *     creator: null|array,
	 *     createdAt: string }
	 * } The result of the request
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function create(string $name): array {
		//<editor-fold desc="Params basic validation">
		if (empty($name)) {
			throw new InvalidConfigException('The "name" parameter is required and should not be empty');
		}
		//</editor-fold>

		$request = $this->getInstance()->getRequest();
		$response = $request->post('/custom-fields', [
			'customFieldName' => $name,
		], [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Updates a custom field
	 * <code>
	 * <?php
	 *   $response = $olc->customFields()->update(1, 'Wife Name');
	 * ?>
	 * </code>
	 * @param int $id The custom field ID
	 * @param string $name The name to update
	 * @return array{
	 *   message: string,
	 *   data: array{
	 *     id: int,
	 *     key: string,
	 *     value: string,
	 *     defaultValue: string,
	 *     isLiveMode: bool,
	 *     creator: null|array,
	 *     createdAt: string }
	 * } The result of the request
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function update(int $id, string $name): array {
		//<editor-fold desc="Params basic validation">
		if (empty($name)) {
			throw new InvalidConfigException('The "name" parameter is required and should not be empty');
		}
		//</editor-fold>

		$request = $this->getInstance()->getRequest();
		$response = $request->put("/custom-fields/$id", [
			'customFieldName' => $name,
		], [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}
}
