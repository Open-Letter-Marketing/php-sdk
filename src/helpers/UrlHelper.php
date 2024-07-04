<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\helpers;

abstract class UrlHelper {
	public static function assetUrlToPath(string $url): string {
		$matches = [];
		\preg_match('/\/(s3\/.+)$/i', $url, $matches);
		return $matches[1] ?? '';
	}
}
