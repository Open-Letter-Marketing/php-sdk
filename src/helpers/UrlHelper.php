<?php declare(strict_types=1);

namespace Olc\helpers;

abstract class UrlHelper {
	public static function assetUrlToPath(string $url): string {
		$matches = [];
		\preg_match('/\/(s3\/.+)$/i', $url, $matches);
		return $matches[1] ?? '';
	}
}
