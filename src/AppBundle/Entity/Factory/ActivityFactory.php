<?php
/**
 * Project: feeio2
 * File: ActivityFactory.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 29.12.15
 * Version: 1.0
 */

namespace AppBundle\Entity\Factory;


use AppBundle\Entity\Activity;
use AppBundle\Entity\Company;

class ActivityFactory {

	public function createActivity($name, Company $company){

		$activity = new Activity();
		$activity->setName($name);
		$activity->setCompany($company);

		return $activity;

	}

}