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
use AppBundle\Entity\Role;
use AppBundle\Entity\RoleAuthorization;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\ORMException;
use UserBundle\Entity\User;

class AuthorizationIndividual extends AuthorizationAbstract {


	/**
	 * @param \UserBundle\Entity\User $user
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	private function getRolesEnabledOfUser(User $user){

		$roles = new ArrayCollection();

		$criteria = array(
			'user' => $user,
		);
		$userCompanies = $this->em->getRepository('AppBundle:UserCompany')->findBy($criteria);

		foreach($userCompanies as $uc){

			$ucTemporality = $uc->getData();
			$role = $ucTemporality->getRole();

			if($ucTemporality->getStatus()->getCode() === 'enabled' AND $role->getEnabled()){

				$roles->add($role);

			}

		}

		return $roles;

	}

	/**
	 * @param \AppBundle\Entity\Authorization $authorization
	 * @param \UserBundle\Entity\User         $user
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getCompaniesWhereUserHasAuthorization(Authorization $authorization, User $user){

		$companies = new ArrayCollection();

		$roles = $this->getRolesEnabledOfUser($user);

		foreach($roles as $role){

			$roleAuthorizations = $role->getRoleAuthorizationRelations();

			foreach($roleAuthorizations as $roleAuthorization){

				if($roleAuthorization->getEnabled() AND $roleAuthorization->getAuthorization() === $authorization){

					$companies->add($role->getCompany());

				}

			}

		}

		return $companies;

	}

	/**
	 * @param                         $code
	 * @param \UserBundle\Entity\User $user
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 * @throws \Doctrine\ORM\ORMException
	 */
	public function getCompaniesWhereUserHasAuthorizationCode($code, User $user){

		$criteria = array(
			'code' => $code,
		);

		$authorization = $this->em->getRepository('AppBundle:Authorization')->findOneBy($criteria);

		//TODO - only for development
		return $user->getCompaniesEnabled();

		if(!$authorization){
			throw new ORMException('Autorizace (oprávnění) '.$code.' neexistuje.');
		}

		return $this->getCompaniesWhereUserHasAuthorization($authorization, $user);

	}

}