<?php

/** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace Olc\tests\api;

use Olc\libs\Instance;
use PHPUnit\Framework\TestCase;

final class UserApiTest extends TestCase {
	public function testFetchingAccountDetails(): void {
		$response = Instance::getInstance()->user()->me();
		$this->assertIsArray($response, 'Response is not an array');
	}
}

