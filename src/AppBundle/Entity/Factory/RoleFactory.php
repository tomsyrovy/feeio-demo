<?php
/**
 * Project: feeio2   
 * File: RoleFactory.php
 *
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 10.07.15
 * Version: 1.0
 */

namespace AppBundle\Entity\Factory;


use AppBundle\Entity\Authorization;
use AppBundle\Entity\Company;
use AppBundle\Entity\Role;
use AppBundle\Entity\RoleAuthorization;

class RoleFactory {

	public function createRole($em, $name, Company $company, $noneditable, array $defaultAuthorizations){

		$role = new Role();
		$role->setName($name);
		$role->setCreated(new \DateTime());
		$role->setCompany($company);
		$role->setNoneditable($noneditable);
		$role->setEnabled(true);

		foreach($em->getRepository("AppBundle:Authorization")->findAll() as $authorization){
			$enabled = in_array($authorization, $defaultAuthorizations) ? true : false;
			$roleAuthorization = $this->createRoleAuthorization($role, $authorization, $enabled);
			$role->addRoleAuthorizationRelation($roleAuthorization);
		}

		return $role;
	}

	public function createRoleAuthorization(Role $role, Authorization $authorization, $enabled){

		$roleAuthorization = new RoleAuthorization();
		$roleAuthorization->setRole($role);
		$roleAuthorization->setAuthorization($authorization);
		$roleAuthorization->setEnabled($enabled);

		return $roleAuthorization;

	}

}