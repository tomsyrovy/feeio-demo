<?php
	/**
	 * Project: feeio2
	 * File: FreeHoursManager.php
	 * Author: Tomas SYROVY <tomas@syrovy.pro>
	 * Date: 15.01.17
	 * Version: 1.0
	 */

	namespace AppBundle\DependencyInjection;


	use AppBundle\Entity\AllocationUnit;
	use AppBundle\Entity\Commission;
	use AppBundle\Entity\Company;
	use AppBundle\Entity\CompanyGroup;
	use AppBundle\Entity\UserCompany;
	use AppBundle\Entity\YearMonth;
	use Doctrine\ORM\EntityManagerInterface;
	use UserBundle\Entity\User;

	class FreeHoursManager {

		/**
		 * @var EntityManagerInterface
		 */
		private $em;

		/**
		 * FreeHoursManager constructor.
		 *
		 * @param \Doctrine\ORM\EntityManagerInterface $em
		 */
		public function __construct( EntityManagerInterface $em ){
			$this->em = $em;
		}

		public function getFreeHoursOfUserInCompany(User $user, YearMonth $yearMonth, Company $company){

			$freeHours = 0;
			$allocatedHours = 0;

			/** @var UserCompany $userCompanyRelation */
			foreach($user->getUserCompanyRelations() as $userCompanyRelation){
				if($userCompanyRelation->getData()->getStatus()->getCode() === 'enabled' and $userCompanyRelation->getCompany() === $company){
					$freeHours += $userCompanyRelation->getData()->getHours();
					/** @var AllocationUnit $au */
					foreach($userCompanyRelation->getAllocationUnits() as $au){
						if($au->getYearMonth() === $yearMonth){
							$allocatedHours += $au->getHoursPlan();
						}
					}
				}
			}

			$diff = $freeHours-$allocatedHours;

			return $diff;

		}

		public function getFreeHoursOfUserInCompanyGroup(User $user, YearMonth $yearMonth, CompanyGroup $companyGroup){

			$freeHours = 0;
			$allocatedHours = 0;

			/** @var UserCompany $userCompanyRelation */
			foreach($user->getUserCompanyRelations() as $userCompanyRelation){
				if($userCompanyRelation->getData()->getStatus()->getCode() === 'enabled'){
					$freeHours += $userCompanyRelation->getData()->getHours();
					/** @var AllocationUnit $au */
					foreach($userCompanyRelation->getAllocationUnits() as $au){
						if($au->getYearMonth() === $yearMonth and $au->getCommission()->getCampaign()->getCompanyGroup() === $companyGroup){
							$allocatedHours += $au->getHoursPlan();
						}
					}
				}
			}

			$diff = $freeHours-$allocatedHours;

			return $diff;

		}

		public function getFreeHoursOfUserInCompanies(User $user, YearMonth $yearMonth){

			$freeHours = 0;
			$allocatedHours = 0;

			/** @var UserCompany $userCompanyRelation */
			foreach($user->getUserCompanyRelations() as $userCompanyRelation){
				if($userCompanyRelation->getData()->getStatus()->getCode() === 'enabled'){
					$freeHours += $userCompanyRelation->getData()->getHours();
					/** @var AllocationUnit $au */
					foreach($userCompanyRelation->getAllocationUnits() as $au){
						if($au->getYearMonth() === $yearMonth){
							$allocatedHours += $au->getHoursPlan();
						}
					}
				}
			}

			$diff = $freeHours-$allocatedHours;

			return $diff;

		}

	}