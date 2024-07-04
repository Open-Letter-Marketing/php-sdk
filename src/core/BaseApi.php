<?php declare(strict_types=1);

/**
 * @copyright 2024 Open Letter Connect Ltd.
 * @link https://www.openletterconnect.com/
 * @since 2024-06
 */

namespace Olc\core;

abstract class BaseApi {
	/**
	 * OlcInstance that this API is associated with
	 */
	private OlcInstance $_olcInstance;

	/**
	 * BaseApi constructor.
	 */
	public function __construct(OlcInstance $olcInstance) {
		$this->_olcInstance = $olcInstance;
	}

	/**
	 * Gets the OlcInstance that this API is associated with.
	 * @return OlcInstance
	 */
	protected function getInstance(): OlcInstance {
		return $this->_olcInstance;
	}
}
