<?php
/**
 * Project: feeio2
 * File: ActivityManager.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 16.01.16
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection;


use AppBundle\Entity\Company;
use UserBundle\Entity\User;

class ActivityManager {

	/**
	 * @var User
	 */
	private $user;

	/**
	 * ActivityManager constructor.
	 *
	 * @param \UserBundle\Entity\User $user
	 */
	public function __construct( User $user){
		$this->user    = $user;
	}

	/**
	 * @param \AppBundle\Entity\Company $company
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCompanyActivities(Company $company){

		return $company->getActivities();

	}

	/**
	 * @param \AppBundle\Entity\Company $company
	 *
	 * @return array
	 */
	public function getCompanyActivitiesWithFavourites(Company $company){

		$companyActivities = $this->getCompanyActivities($company);
		$favouriteActivities = $this->user->getFavouriteActivities();
		$fas = array();
		foreach($favouriteActivities as $fa){
			$activity = $fa->getActivity();
			if($companyActivities->contains($activity)){
				$fas[] = $activity;
			}
		}

		$data = [
			'Oblíbené' => $fas,
			'Všechny' => $companyActivities->toArray(),
		];

		return $data;

	}


}