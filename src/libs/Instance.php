<?php

namespace Olc\libs;

use Olc\core\OlcInstance as OlcInstanceAlias;
use Olc\Olc;

class Instance {
	private static OlcInstanceAlias $instance;

	public static function create(): OlcInstanceAlias {
		return Olc::create($_ENV['OLC_API_KEY'], $_ENV['OLC_API_VERSION'], $_ENV['OLC_API_ENDPOINT']);
	}

	public static function getInstance(): OlcInstanceAlias {
		return self::$instance ?? self::$instance = self::create();
	}
}
