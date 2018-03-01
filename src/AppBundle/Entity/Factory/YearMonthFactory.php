<?php
/**
 * Project: feeio2
 * File: YearMonthFactory.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 05.12.15
 * Version: 1.0
 */

namespace AppBundle\Entity\Factory;


use AppBundle\Entity\YearMonth;
use Doctrine\Common\Persistence\ObjectManager;

class YearMonthFactory {

	public function generateYears($year_from, $year_to){

		$collection = array();

		for($i = $year_from; $i <= $year_to; $i++){

			for($j = 1; $j <= 12; $j++){

				$yearmonth = $this->create($i, $j);

				$collection[] = $yearmonth;

			}

		}

		return $collection;

	}

	public function persistCollection(ObjectManager $em, array $collection){

		foreach($collection as $yearmonth){

			$em->persist($yearmonth);

		}

	}

	private function create($year, $month){

		$yearmonth = new YearMonth();
		$yearmonth->setYear($year);
		$yearmonth->setMonth($month);

		return $yearmonth;

	}

}