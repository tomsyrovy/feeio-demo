<?php
/**
 * Project: feeio2
 * File: Slug.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 16.01.16
 * Version: 1.0
 */

namespace AppBundle\Library;


class Slug {

	/**
	 * @param $str
	 *
	 * @return mixed|string
	 */
	public static function getSlug($str) {
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_| -]+/", '-', $clean);

		return $clean;
	}

	public static function getAlphanumericalSlug($str) {
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_| -]+/", '', $clean);

		return $clean;
	}

}