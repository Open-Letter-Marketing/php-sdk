<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\api;

use Olc\core\BaseApi;
use Olc\errors\InvalidConfigException;
use Olc\errors\NotImplemented;

class OrdersApi extends BaseApi {
	protected const SOURCE = [
		'OLC_CLIENT',
		'ZAPIER',
		'HUBSPOT',
		'SALESFORCE',
		'PODIO',
		'GOHIGHLEVEL',
	];

	protected const PAYMENT_STATUS = [
		'PENDING',
		'PAID',
		'PAYMENT_FAILED',
		'NOT_CHARGED',
	];

	protected const ORDER_STATUS = [
		'ON_HOLD',
		'SCHEDULED',
		'PROCESSING',
		'MAILED',
		'CANCELED',
	];

	/**
	 * Get orders history
	 * <code>
	 * <?php
	 *   $response = $olc->orders()->all([
	 *     //'page' => 1,
	 *     //'pageSize' => 10,
	 *   ]);
	 * ?>
	 * </code>
	 *
	 * @param array{page?: int, pageSize?: int} $params Parameters to send with the request.
	 * @return array{message: string, data: array} The response data.
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function all(array $params = []): array {
		$request = $this->getInstance()->getRequest();
		$query = \count($params) ? '?' . \http_build_query($params) : '';
		$response = $request->get('/orders' . $query, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Get order details against the given id
	 * <code>
	 * <?php
	 *   $response = $olc->orders()->get(1);
	 * ?>
	 * </code>
	 *
	 * @param int $id The order ID
	 * @return array The response data.
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function get(int $id): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->get("/orders/$id", [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Get order filters data
	 * <code>
	 * <?php
	 *   $response = $olc->orders()->filtersData([
	 *      //'source' => 'HUBSPOT',
	 *      //'paymentStatus' => 'PAID',
	 *      //'orderStatus' => 'MAILED',
	 *    ]);
	 * ?>
	 * </code>
	 *
	 * @param array{source?: string, paymentStatus?: string, orderStatus?: string} $params *(optional)* Additional options pass to the request
	 * @param $params ['source'] *(optional)* The source, should be one of them:
	 *   - `'OLC_CLIENT'`
	 *   - `'ZAPIER'`
	 *   - `'HUBSPOT'`
	 *   - `'SALESFORCE'`
	 *   - `'PODIO'`
	 *   - `'GOHIGHLEVEL'`
	 * @param $params ['paymentStatus'] *(optional)* The payment status, should be one of them:
	 *   - `'PENDING'`
	 *   - `'PAID'`
	 *   - `'PAYMENT_FAILED'`
	 *   - `'NOT_CHARGED'`
	 * @param $params ['orderStatus'] *(optional)* The order status, should be one of them:
	 *   - `'ON_HOLD'`
	 *   - `'SCHEDULED'`
	 *   - `'PROCESSING'`
	 *   - `'MAILED'`
	 *   - `'CANCELED'`
	 * @return array{message: string, data: array{
	 *     source: array<string>,
	 * 	   paymentStatus: array<string>,
	 *     orderStatus: array<string>}} The response data.
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function filtersData(array $params = []): array {
		$query = [];

		if (\array_key_exists('source', $params)) {
			if (!in_array($params['source'], static::SOURCE)) {
				throw new InvalidConfigException('The "source" is not valid.');
			}
			$query['source'] = $params['source'];
		}

		if (\array_key_exists('paymentStatus', $params)) {
			if (!in_array($params['paymentStatus'], static::PAYMENT_STATUS)) {
				throw new InvalidConfigException('The "paymentStatus" is not valid.');
			}
			$query['paymentStatus'] = $params['paymentStatus'];
		}

		if (\array_key_exists('orderStatus', $params)) {
			if (!in_array($params['orderStatus'], static::ORDER_STATUS)) {
				throw new InvalidConfigException('The "orderStatus" is not valid.');
			}
			$query['orderStatus'] = $params['orderStatus'];
		}

		$request = $this->getInstance()->getRequest();
		$query = \count($query) ? '?' . \http_build_query($query) : '';
		$response = $request->get('/orders/filters-data' . $query, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Calculate a cost<br>
	 * <code>
	 * <?php
	 *   $response = $olc->orders()->calculateCost([
	 *     'tag' => 69,
	 *     'productId' => 791926,
	 *   ]);
	 * ?>
	 * </code>'
	 *
	 * @param array{tag: int, productId: int} $params Required parameters pass to the request
	 * @param int $params ['tag'] - The tag ID
	 * @param int $params ['productId'] - The product ID
	 * @return array{message: string, data: array} The result of the request
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function calculateCost(array $params): array {
		if (!\array_key_exists('tag', $params)
			|| !is_int($params['tag'])) {
			throw new InvalidConfigException('The "tag" parameter is required and should be an integer');
		}

		if (!\array_key_exists('productId', $params)
			|| !is_int($params['productId'])) {
			throw new InvalidConfigException('The "productId" parameter is required and should be an integer');
		}

		$request = $this->getInstance()->getRequest();
		$response = $request->post('/orders/calculate-cost', $params, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Create a view proof<br>
	 * <code>
	 * <?php
	 *   $response = $olc->orders()->viewProof([
	 *     'templateId' => 69,
	 *     'returnContactId' => 791926,
	 *     'contactId' => 791927,
	 *   ]);
	 * ?>
	 * </code>'
	 *
	 * @param array{templateId: int, returnContactId: int, contactId: int} $params Required parameters pass to the request
	 * @param int $params ['templateId'] - The template ID
	 * @param int $params ['returnContactId'] - The return contact ID
	 * @param int $params ['contactId'] - The contact ID
	 * @return array{message: string, data: array} The result of the request
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function viewProof(array $params): array {
		if (!\array_key_exists('templateId', $params)
			|| !is_int($params['templateId'])) {
			throw new InvalidConfigException('The "templateId" parameter is required and should be an integer');
		}

		if (!\array_key_exists('returnContactId', $params)
			|| !is_int($params['returnContactId'])) {
			throw new InvalidConfigException('The "returnContactId" parameter is required and should be an integer');
		}

		if (!\array_key_exists('contactId', $params)
			|| !is_int($params['contactId'])) {
			throw new InvalidConfigException('The "contactId" parameter is required and should be an integer');
		}

		$request = $this->getInstance()->getRequest();
		$response = $request->post('/orders/view-proof', $params, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Get expected mailed date<br>
	 * <code>
	 * <?php
	 *   $response = $olc->orders()->getExpectedMailedDate([
	 *     'scheduledDate' => 'ASAP',
	 *   ]);
	 * ?>
	 * </code>'
	 *
	 * @param array{scheduledDate: string} $params Required parameters pass to the request
	 * @param string $params ['scheduledDate'] - The ISO date *(YYYY-MM-DD)* / 'ASAP'
	 * @return array The result of the request
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function getExpectedMailedDate(array $params): array {
		if (!\array_key_exists('scheduledDate', $params)
			|| !is_string($params['scheduledDate'])) {
			throw new InvalidConfigException('The "scheduledDate" parameter is required and should be a string');
		}

		$request = $this->getInstance()->getRequest();
		$response = $request->post('/orders/get-expected-mailed-date', $params, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Create an order<br>
	 * **Note:** Please note that one of the params `contactIds`, `reqId` or `tag` is required
	 * <code>
	 * <?php
	 *   $response = $olc->orders()->create([
	 *     'productId' => 59,
	 *     'templateId' => 38,
	 *     'returnAddress' => null,
	 *     'tag' => -1,
	 *   ]);
	 * ?>
	 * </code>'
	 *
	 * @param array{
	 *   productId: int,
	 *   templateId: int,
	 *   returnAddress: int,
	 *   contactIds?: int[],
	 *   reqId?: int,
	 *   tag?: int,
	 *   crmMode?: string} $params Required parameters pass to the request
	 * @param int $params ['productId'] - The product ID
	 * @param int $params ['templateId'] - The template ID
	 * @param int|null $params ['returnAddress'] - The return address
	 * @param int[] $params ['contactIds'] - *(optional)* List of contacts ID
	 * @param string $params ['reqId'] - *(optional)* Request ID (via CSV upload)
	 * @param int $params ['tag'] - *(optional)* The tag ID (-1 for all)
	 * @param string $params ['crmMode'] - *(optional)* CRM mode
	 * @return array The result of the request
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function create(array $params): array {
		if (!\array_key_exists('productId', $params)
			|| !is_int($params['productId'])) {
			throw new InvalidConfigException('The "productId" parameter is required and should be an integer');
		}

		if (!\array_key_exists('returnAddress', $params) ) {
			throw new InvalidConfigException('The "returnAddress" parameter is required');
		}

		if (!is_int($params['returnAddress']) && !is_null($params['returnAddress'])) {
			throw new InvalidConfigException('The "returnAddress" parameter either a null or an integer');
		}

		if (!\array_key_exists('templateId', $params)
			|| !is_int($params['templateId'])) {
			throw new InvalidConfigException('The "templateId" parameter is required and should be an integer');
		}

		if (\array_key_exists('tag', $params)
			&& \array_key_exists('reqId', $params)
			&& \array_key_exists('contactIds', $params)
		) {
			throw new InvalidConfigException('One of the parameters "tag", "reqId" or "contactIds" parameter is required');
		}

		if (\array_key_exists('tag', $params)
			|| !is_int($params['tag'])) {
			throw new InvalidConfigException('The "tag" parameter is required and should be a number');
		}

		if (\array_key_exists('reqId', $params)
			|| !is_int($params['reqId'])) {
			throw new InvalidConfigException('The "reqId" parameter is required and should be a string');
		}

		if (\array_key_exists('contactIds', $params)
			|| !is_array($params['contactIds'])) {
			throw new InvalidConfigException('The "contactIds" parameter is required and should be an array');
		}

		$request = $this->getInstance()->getRequest();
		$response = $request->post('/orders', $params, [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Execute a bulk action against an order<br>
	 * <code>
	 * <?php
	 *   $response = $olc->orders()->bulkActions([
	 *     'ids' => [30],
	 *     'action' => 'remove order items',
	 *     'orderId' => 20,
	 *   ]);
	 * ?>
	 * </code>'
	 *
	 * @param array{
	 *   ids: int[],
	 *   action: string,
	 *   orderId: int} $params Required parameters pass to the request
	 * @param int[] $params ['ids'] - List of IDs
	 * @param string $params ['action'] - Action to perform
	 * @param int $params ['orderId'] - The order ID
	 * @return array The result of the request
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 * @deprecated Internal method, Not available in this version
	 */
	public function bulkActions(array $params): array {
		throw new NotImplemented('Not available in this version');
	}
}
