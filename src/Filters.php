<?php

declare(strict_types=1);

namespace Thunbolt\Templates;

use WebChemistry;

class Filters {

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
	public function month(int $month): string {
		return WebChemistry\Utils\DateTime::translateMonth($month);
	}

	/**
	 * @param int $day
	 * @return string
	 */
	public function day(int $day): string {
		return WebChemistry\Utils\DateTime::translateDay($day);
	}

	/**
	 * @param array|\Traversable $s
	 * @return int
	 */
	public function count($s): int {
		return $s instanceof \Traversable ? iterator_count($s) : count($s);
	}

	/**
	 * @param \DateTime|int $time
	 * @return string
	 */
	public function timeAgo($time): string {
		return WebChemistry\Utils\DateTime::timeAgo($time);
	}

	/**
	 * @param \DateTime|int $time
	 * @return string
	 */
	public function datetime($time): string {
		return WebChemistry\Utils\DateTime::toDateTime($time);
	}

	/**
	 * @param \DateTime|int $time
	 * @return string
	 */
	public function date($time, $format = NULL): string {
		return WebChemistry\Utils\DateTime::toDate($time, $format);
	}

	/**
	 * @param \DateTime|int $time
	 * @return string
	 */
	public function time($time): string {
		return WebChemistry\Utils\DateTime::toTime($time);
	}

	/**
	 * @param bool|mixed $boolean
	 * @return string
	 */
	public function bool($boolean): string {
		return self::$booleans[(int) $boolean];
	}

	/**
	 * @param float|int $num
	 * @param int $decimals
	 * @param string|null $decPoint
	 * @param string|null $sepThousands
	 * @return string
	 */
	public function number($num, int $decimals = 0, string $decPoint = NULL, string $sepThousands = NULL): string {
		return WebChemistry\Utils\Strings::number($num, $decimals, $decPoint, $sepThousands);
	}

}
