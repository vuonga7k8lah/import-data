<?php

namespace ImportData\Helper;

class Option
{
	private static string $optionKey = 'kma-import';

	private static array $aDataOptions = [];

	public static function saveAuthSettings(array $aValues)
	{
		update_option(self::$optionKey, $aValues);
	}

	public static function getNumberProduct()
	{
		return self::getAuthField('numberProduct', 200);
	}

	public static function getNumberReview()
	{
		return self::getAuthField('numberReview', 500);
	}

	public static function getNumberComment()
	{
		return self::getAuthField('numberComment', 1000);
	}

	public static function getNumberCustomer()
	{
		return self::getAuthField('numberCustomer', 500);
	}
	public static function getNumberOrder()
	{
		return self::getAuthField('numberOrder', 500);
	}

	public static function getAuthField($field, $default = '')
	{
		self::getAuthSettings();
		return self::$aDataOptions[$field] ?? $default;
	}

	public static function getAuthSettings()
	{
		self::$aDataOptions = get_option(self::$optionKey) ?: [];
		if (empty(self::$aDataOptions)) {
			self::$aDataOptions = [
				'numberProduct' => '',
				'numberComment' => '',
				'numberCustomer' => '',
				'numberOrder' => '',
				'numberReview' => ''
			];
		}
		return self::$aDataOptions;
	}
}