<?php
/**
 * Project: feeio2
 * File: CompanyFactory.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 16.12.15
 * Version: 1.0
 */

namespace AppBundle\Entity\Factory;


use AppBundle\Entity\Company;
use UserBundle\Entity\User;

class CompanyFactory {

	public function createCompany($name, User $owner){
		$company = new Company();
		$company->setCreated(new \DateTime());
		$company->setEnabled(true);
		$owner->addOwnedCompany($company);
		$company->setOwner($owner);
		$company->setName($name);

		return $company;
	}

}