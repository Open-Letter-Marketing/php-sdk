<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\tests\api;

use Olc\libs\Instance;
use PHPUnit\Framework\TestCase;

final class UserApiTest extends TestCase {
	public function testFetchingAccountDetails(): void {
		$response = Instance::getInstance()->user()->me();
		/** @noinspection PhpParamsInspection */
		$this->assertIsArray($response, 'Response is not an array');
	}
}

