<?php

namespace Thunbolt\Templates;

use Nette, WebChemistry;

class Filters extends Nette\Object {

	/** @var array */
	public static $booleans = ['no', 'yes'];

	public function load() {
		$name = strtolower(func_get_arg(0));
		if (method_exists($this, $name)) {
			return call_user_func_array([$this, $name], array_slice(func_get_args(), 1));
		}
	}

	/**
	 * @param int $month
	 * @return string
	 */
	public function month($month) {
		return WebChemistry\Utils\DateTime::translateMonth($month);
	}

	/**
	 * @param int $day
	 * @return string
	 */
	public function day($day) {
		return WebChemistry\Utils\DateTime::translateDay($day);
	}

	/**
	 * @param array|\Traversable $s
	 * @return int
	 */
	public function count($s) {
		return $s instanceof \Traversable ? iterator_count($s) : count($s);
	}

	/**
	 * @param \DateTime|int $time
	 * @return string
	 */
	public function timeAgo($time) {
		return WebChemistry\Utils\DateTime::timeAgo($time);
	}

	/**
	 * @param \DateTime|int $time
	 * @return string
	 */
	public function datetime($time) {
		return WebChemistry\Utils\DateTime::toDateTime($time);
	}

	/**
	 * @param \DateTime|int $time
	 * @return string
	 */
	public function date($time, $format = NULL) {
		return WebChemistry\Utils\DateTime::toDate($time, $format);
	}

	/**
	 * @param \DateTime|int $time
	 * @return string
	 */
	public function time($time) {
		return WebChemistry\Utils\DateTime::toTime($time);
	}

	/**
	 * @param bool $boolean
	 * @return string
	 */
	public function bool($boolean) {
		return self::$booleans[(int)((bool) $boolean)];
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public function entity_de($string) {
		return html_entity_decode($string);
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public function entity_en($string) {
		return htmlentities($string);
	}

	/**
	 * @param float|int $num
	 * @param int $decimals
	 * @param null $decPoint
	 * @param null $sepThousands
	 * @return string
	 */
	public function number($num, $decimals = 0, $decPoint = NULL, $sepThousands = NULL) {
		return WebChemistry\Utils\Strings::number($num, $decimals, $decPoint, $sepThousands);
	}

	/**
	 * @param int $s
	 * @param string $first
	 * @param string $second
	 * @param string $third
	 * @return string
	 */
	public function plural($s, $first, $second, $third) {
		return $s == 1 ? $first : ($s < 5 && $s > 1 ? $second : $third);
	}

	/**
	 * @param string $s
	 * @param int $maxLen
	 * @param string $append
	 * @param bool $exact
	 * @return string
	 */
	public function htmlTruncate($s, $maxLen, $append = "\xE2\x80\xA6", $exact = TRUE) {
		return WebChemistry\Utils\Strings::htmlTruncate($s, $maxLen, $append, $exact);
	}

}
