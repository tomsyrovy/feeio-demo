<?php
/**
 * Project: feeio2
 * File: AuthorizationIndividual.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 15.08.15
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection\Authorization;


use AppBundle\Entity\Authorization;
use AppBundle\Entity\Company;
use AppBundle\Entity\Role;
use AppBundle\Entity\RoleAuthorization;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\ORMException;
use UserBundle\Entity\User;

class AuthorizationCompany extends AuthorizationAbstract {


	public function getUsersInCompanyWhereUsersHaveAuthorization(Authorization $authorization, Company $company){

		$userCompanyRelations = new ArrayCollection();

		$roles = $company->getRolesEnabled();

		foreach($roles as $role){

			$roleAuthorizations = $role->getRoleAuthorizationRelations();

			foreach($roleAuthorizations as $roleAuthorization){

				if($roleAuthorization->getEnabled() AND $roleAuthorization->getAuthorization() === $authorization){

					$ats = $role->getActualTemporalities();

					foreach($ats as $at){

						$userCompanyRelations->add($at->getUserCompany());

					}

				}

			}

		}

		return $userCompanyRelations;

	}

	public function getUsersInCompanyWhereUsersHaveAuthorizationCode($code, Company $company){

		$criteria = array(
			'code' => $code,
		);

		$authorization = $this->em->getRepository('AppBundle:Authorization')->findOneBy($criteria);

		if(!$authorization){
			throw new ORMException('Autorizace (oprávnění) '.$code.' neexistuje.');
		}

		return $this->getUsersInCompanyWhereUsersHaveAuthorization($authorization, $company);

	}

}