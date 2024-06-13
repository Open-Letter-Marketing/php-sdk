<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\core;

abstract class BaseApi {
	/**
	 * BaseApi constructor.
	 * @param OlcInstance $_olcInstance OlcInstance that this API is associated with
	 */
	public function __construct(private OlcInstance $_olcInstance) {
	}

	/**
	 * Gets the OlcInstance that this API is associated with.
	 * @return OlcInstance
	 */
	protected function getInstance(): OlcInstance {
		return $this->_olcInstance;
	}
}
