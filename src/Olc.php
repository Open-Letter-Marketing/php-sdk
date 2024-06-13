<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc;

use Olc\core\OlcInstance;

abstract class Olc {
	/**
	 * Creates a new OLC instance.
	 * @param string $apiKey The API key to use.
	 * @param string|null $version The version to use. If not set, the default version will be used.
	 * e.g. "v1" or "v2"
	 * @param string|null $endpoint The endpoint to use. If not set, the default endpoint will be used.
	 * e.g. "https://api.openletterconnect.com"
	 * @return OlcInstance The OLC instance.
	 */
	final public static function create(string $apiKey, string|null $version = null, string|null $endpoint = null): OlcInstance {
		return new OlcInstance($apiKey, $version, $endpoint);
	}
}
