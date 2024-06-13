<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\core;

class Environment {
	/**
	 * Checks if the environment is production or not.
	 * @return bool
	 */
	public static function isProduction(): bool {
		return static::env() === 'production';
	}

	public static function sslVerify(): bool {
		return trim($_ENV['SSL_VERIFY'] ?? 'true') !== 'false';
	}

	/**
	 * Returns the current environment.
	 * @return string
	 */
	public static function env(): string {
		return $_ENV['ENV'] ?? 'production';
	}
}
