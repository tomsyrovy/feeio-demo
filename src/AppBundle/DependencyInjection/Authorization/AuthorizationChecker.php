<?php
	/**
	 * Project: feeio2
	 * File: AuthorizationChecker.php
	 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
	 * Date: 09.08.15
	 * Version: 1.0
	 */

namespace AppBundle\DependencyInjection\Authorization;


use AppBundle\Entity\Authorization;
use AppBundle\Entity\Company;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use UserBundle\Entity\User;

class AuthorizationChecker extends AuthorizationAbstract {

	public function checkAuthorizationOfUserInCompany(Authorization $authorization, User $user, Company $company){

		$criteria = array(
			'user' => $user,
			'company' => $company,
		);

		$userCompany = $this->em->getRepository('AppBundle:UserCompany')->findOneBy($criteria);

		if($userCompany){

			$userCompanyTemporality = $userCompany->getData();

			if($userCompanyTemporality->getStatus()->getCode() === 'enabled'){

				$role = $userCompanyTemporality->getRole();

				$criteria = array(
					'role' => $role,
					'authorization' => $authorization,
				);

				$roleAuthorization = $this->em->getRepository('AppBundle:RoleAuthorization')->findOneBy($criteria);

				return $roleAuthorization->getEnabled();
			}

		}

		return false;

	}

	public function checkAuthorizationCodeOfUserInCompany($code, User $user, Company $company){

		$criteria = array(
			'code' => $code,
		);

		$authorization = $this->em->getRepository('AppBundle:Authorization')->findOneBy($criteria);

		if(!$authorization){
			return true; //TODO - only for development
			throw new ORMException('Autorizace (oprávnění) '.$code.' neexistuje.');
		}

		return $this->checkAuthorizationOfUserInCompany($authorization, $user, $company);

	}

}