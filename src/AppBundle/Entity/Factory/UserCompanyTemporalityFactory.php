<?php
/**
 * Project: feeio2   
 * File: UserCompanyTemporality.php
 *
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 11.07.15
 * Version: 1.0
 */

namespace AppBundle\Entity\Factory;


use AppBundle\DependencyInjection\Tool;
use AppBundle\Entity\Role;
use AppBundle\Entity\UserCompany;
use AppBundle\Entity\UserCompanyTemporality;
use AppBundle\Entity\UserCompanyTemporalityStatus;

class UserCompanyTemporalityFactory {

	public function createUserCompanyTemporality(UserCompany $userCompany, Role $role, UserCompanyTemporalityStatus $status, $hours, $rateInternal, $until = null){

		if($role->getCompany() != $userCompany->getCompany()){
			throw new \Exception("UserCompanyTemporality cannot be created. UserCompany company attribute is not same as Role company attribute.");
		}

		$userCompanyTemporality = new UserCompanyTemporality();
		$userCompanyTemporality->setUserCompany($userCompany);
		$userCompanyTemporality->setFrom(new \DateTime());
		$userCompanyTemporality->setRole($role);
		$userCompanyTemporality->setStatus($status);
		$userCompanyTemporality->setHours($hours);
		$userCompanyTemporality->setRateInternal($rateInternal);

		if($until != null){
			$userCompanyTemporality->setUntil($until);
		}

		return $userCompanyTemporality;

	}

	public function createUserCompanyTemporalityRandom(UserCompany $userCompany, Role $role, $status){

		$h = Tool::getRandomNumber(60, 160, -1);
		$ri = Tool::getRandomNumber(100, 500, -1);
		$re = $ri + Tool::getRandomNumber(100, 700, -1);

		return $this->createUserCompanyTemporality($userCompany, $role, $status, $h, $re, $ri);

	}

}