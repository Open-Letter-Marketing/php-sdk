<?php declare(strict_types=1);

namespace Olc\helpers;

abstract class CustomFieldsHelper {
	/**
	 * Checks whether the given array is valid custom field or not
	 * @param array $field The field to be validated.
	 * @return bool Whether the field is valid or not.
	 */
	public static function validate(array $field): bool {
		if (!\array_key_exists('key', $field)
			|| !\is_string($field['key'])
			|| !preg_match('/^\{\{[A-Z]+\.[A-Z]+(_[A-Z]+)*}}$/', $field['key'])) {
			return false;
		}

		if (!\array_key_exists('value', $field)
			|| !\is_string($field['value'])
			|| empty($field['value'])) {
			return false;
		}

		if (\array_key_exists('strict', $field) && !\is_bool($field['strict'])) {
			return false;
		}

		if (\array_key_exists('defaultValue', $field) && !\is_string($field['defaultValue'])) {
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the given list is valid custom fields array or not
	 * @param array $list The list to be validated.
	 * @return bool Whether the list is valid or not.
	 */
	public static function validateList(array $list = []): bool {
		if (!\is_countable($list) || !\count($list)) {
			return false;
		}

		foreach ($list as $field) {
			if (!static::validate($field)) {
				return false;
			}

		}

		return true;
	}
}
