<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\helpers;

abstract class FileHelper {
	/**
	 * Get a random name for a given file
	 * @param string $filePath The path to the file
	 * @return string The random name of the file
	 */
	public static function toRandomName(string $filePath): string {
		$file = pathinfo($filePath);
		return bin2hex(random_bytes(16)) . '.' . strtolower($file['extension']);
	}
}
