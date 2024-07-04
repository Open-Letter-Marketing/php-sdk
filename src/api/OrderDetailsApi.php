<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\api;

use Olc\core\BaseApi;
use Olc\core\OlcRequestError;
use Olc\errors\InvalidConfigException;

class OrderDetailsApi extends BaseApi {
	private const ADDRESS_STATUSES = [
		'Failed', 'Verified', 'Corrected'
	];

	private const MAILED_STATUSES = [
		'Mailed', 'Completed', 'Processing'
	];

	/**
	 * Get order contacts
	 * <code>
	 * <?php
	 *   $response = $olc->orderDetails()->allContacts(9759, [
	 *     //'search' => 'NY',
	 *     //'addressStatus' => 'Verified',
	 *     //'mailedStatus' => 'Completed',
	 *   ]);
	 * ?>
	 * </code>
	 *
	 * @param int $orderId The order ID
	 * @param array{startDate: string, endDate: string} $params ['deliveredDate'] - Filter results by delivered date range
	 * @return array{message: string, data: array} The response data.
	 * @throws InvalidConfigException
	 * @throws OlcRequestError If the request fails.
	 */
	public function allContacts(int $orderId, array $params = []): array {
		if (\array_key_exists('search', $params)
			|| !is_string($params['search'])) {
			throw new InvalidConfigException('The "search" parameter must be a string');
		}

		if (\array_key_exists('addressStatus', $params)
			|| !in_array($params['addressStatus'], static::ADDRESS_STATUSES)) {
			throw new InvalidConfigException('The "addressStatus" parameter must be one of: '. implode(', ', static::ADDRESS_STATUSES));
		}

		if (\array_key_exists('mailedStatus', $params)
			|| !in_array($params['mailedStatus'], static::MAILED_STATUSES)) {
			throw new InvalidConfigException('The "mailedStatus" parameter must be one of: '. implode(', ', static::MAILED_STATUSES));
		}

		if (\array_key_exists('deliveredDate', $params)
			|| !is_array($params['deliveredDate'])) {
			throw new InvalidConfigException('The "deliveredDate" parameter must be one of: '. implode(', ', static::MAILED_STATUSES));
		}

		$request = $this->getInstance()->getRequest();
		$response = $request->get("/orders/detail/contacts/$orderId", [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Get order details
	 * <code>
	 * <?php
	 *   $response = $olc->orderDetails()->get(9759);
	 * ?>
	 * </code>
	 *
	 * @param int $orderId The order ID
	 * @return array{message: string, data: array} The response data.
	 * @throws OlcRequestError If the request fails.
	 */
	public function get(int $orderId): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->get("/orders/detail/$orderId", [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}

	/**
	 * Get order analytics
	 * <code>
	 * <?php
	 *   $response = $olc->orderDetails()->analytics(9759);
	 * ?>
	 * </code>
	 *
	 * @param int $orderId The order ID
	 * @return array{message: string, data: array} The response data.
	 * @throws OlcRequestError If the request fails.
	 */
	public function analytics(int $orderId): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->get("/orders/detail/analytics/$orderId", [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}
}
