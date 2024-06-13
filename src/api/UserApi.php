<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\api;

use Olc\core\BaseApi;

class UserApi extends BaseApi {
	/**
	 * Get current user details
	 * <code>
	 * <?php
	 *   $response = $olc->user()->me();
	 *   var_dump($response);
	 * ?>
	 * </code>
	 *
	 * @return array{message: string, data: array} The response data.
	 * @throws \Olc\core\OlcRequestError If the request fails.
	 */
	public function me(): array {
		$request = $this->getInstance()->getRequest();
		$response = $request->post('/auth/me', [], [
			'authorize' => true,
		]);

		return $request->retrieveValueByPath($response);
	}
}
