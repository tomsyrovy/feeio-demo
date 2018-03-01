<?php
/**
 * Project: feeio2   
 * File: Tool.php
 *
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 14.03.15
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection;


class Tool {

	public static function getRGBColorDark(){

		$r = str_pad( dechex( mt_rand( 0, 200 ) ), 2, '0', STR_PAD_LEFT);
		$g = str_pad( dechex( mt_rand( 0, 200 ) ), 2, '0', STR_PAD_LEFT);
		$b = str_pad( dechex( mt_rand( 0, 200 ) ), 2, '0', STR_PAD_LEFT);

		return $r.$g.$b;

	}

	public static function getRandomNumber($min, $max, $precision){
		return round(rand($min, $max), $precision);
	}

	public static function getRandomPassword(){
		$now = new \DateTime();
		$randomString = $now->format("j. n. Y H:i:s");
		return substr(md5($randomString), 0, 8);
	}

	public static function is_connected()
	{
		$connected = @fsockopen("www.google.com", 80);

		//website, port  (try 80 or 443)
		if ($connected){
			$is_conn = true; //action when connected
			fclose($connected);
		}else{
			$is_conn = false; //action in connection failure
		}
		return $is_conn;

	}

	/**
	 * @param \DateTime $start_one
	 * @param \DateTime $end_one
	 * @param \DateTime $start_two
	 * @param \DateTime $end_two
	 *
	 * @return int
	 */
	public static function datesOverlap(\DateTime $start_one, \DateTime $end_one, \DateTime $start_two, \DateTime $end_two) {

		if($start_one <= $end_two and $end_one >= $start_two) { //If the dates overlap
			return min($end_one,$end_two)->diff(max($start_two,$start_one))->days + 1; //return how many days overlap
		}

		return 0; //Return 0 if there is no overlap
	}

}