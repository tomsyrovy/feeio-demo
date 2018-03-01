<?php

namespace AppBundle\DependencyInjection\CommissionManager;
use AppBundle\DataObject\CommissionUserCompanyRelationDataObject;
use AppBundle\DependencyInjection\Authorization\AuthorizationCompany;
use AppBundle\Entity\AllocationUnit;
use AppBundle\Entity\CampaignManager;
use AppBundle\Entity\Commission;
use AppBundle\Entity\CommissionUserCompanyRelation;
use AppBundle\Entity\Company;
use AppBundle\Entity\CompanyGroupUserCompanyRelation;
use AppBundle\Entity\UserCompany;
use UserBundle\Entity\User;

/**
 * Project: feeio2
 * File: CommissionManager.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 05.12.15
 * Version: 1.0
 */
class CommissionManager {

	/**
	 * @var \Doctrine\Common\Persistence\ObjectManager
	 */
	private $em;

	/**
	 * CommissionManager constructor.
	 *
	 * @param \Doctrine\Common\Persistence\ObjectManager $em
	 */
	public function __construct( \Doctrine\Common\Persistence\ObjectManager $em ){
		$this->em = $em;
	}

	/**
	 * Vrátí seznam všech zakázek (DO objektů) napříč všemi společnostmi zadaného uživatele zadaného stavu vzhledem ke
	 * společnosti a definovaných rolí vzhledem zakázky
	 *
	 * @param \UserBundle\Entity\User $user Uživatel
	 * @param string                  $statusCode Stav uživatele ve společnosti
	 * @param array                   $roleTypeCodes Role uživatele k zakázce
	 *
	 * @return array
	 */
	public function getCommissionUserCompanyRelationDOsOfUser(User $user, $statusCode, array $roleTypeCodes){

		$commissionUserCompanyRelations_return = array();

		$criteria = array(
			'code' => $statusCode,
		);
		$status = $this->em->getRepository('AppBundle:UserCompanyTemporalityStatus')->findOneBy($criteria);

		$criteria = array(
			'user' => $user,
		);
		$userCompanyRelations = $this->em->getRepository('AppBundle:UserCompany')->findBy($criteria);

		foreach($userCompanyRelations as $ucr){

			if($ucr->getData()->getStatus() === $status){

				$criteria = array(
					'userCompany' => $ucr,
				);
				$commissionUserCompanyRelations = $this->em->getRepository('AppBundle:CommissionUserCompanyRelation')->findBy($criteria);

				foreach($commissionUserCompanyRelations as $cucr){

					$type = $cucr->getCommissionUserCompanyRelationType();

					if(in_array($type->getCode(), $roleTypeCodes)){
						$commissionUserCompanyRelations_return[] = $cucr;
					}

				}

			}

		}

		return $commissionUserCompanyRelations_return;

	}

	/**
	 * Vrátí seznam všech zakázek napříč všemi společnostmi zadaného uživatele zadaného stavu vzhledem ke společnosti a
	 * definovaných rolí vzhledem zakázky
	 *
	 * @param \UserBundle\Entity\User $user Uživatel
	 * @param string                  $statusCode Stav uživatele ve společnosti
	 * @param array                   $roleTypeCodes Role uživatele k zakázce
	 *
	 * @return array
	 */
	public function getCommissionsOfUser(User $user, $statusCode, array $roleTypeCodes){

		$commissions = array();

		$criteria = array(
			'code' => $statusCode,
		);
		$status = $this->em->getRepository('AppBundle:UserCompanyTemporalityStatus')->findOneBy($criteria);

		$criteria = array(
			'user' => $user,
		);
		$userCompanyRelations = $this->em->getRepository('AppBundle:UserCompany')->findBy($criteria);

		foreach($userCompanyRelations as $ucr){

			if($ucr->getData()->getStatus() === $status){

				$criteria = array(
					'userCompany' => $ucr,
				);
				$commissionUserCompanyRelations = $this->em->getRepository('AppBundle:CommissionUserCompanyRelation')->findBy($criteria);

				/** @var CommissionUserCompanyRelation $cucr */
				foreach($commissionUserCompanyRelations as $cucr){

					$type = $cucr->getCommissionUserCompanyRelationType();

					if(in_array($type->getCode(), $roleTypeCodes)){
						$commissions[] = $cucr->getCommission();
					}

				}

			}

		}

		return $commissions;

	}

	/**
	 * Vrátí vazební entitu zadaného uživatele u zadané zakázky a status ve společnosti
	 *
	 * @param \UserBundle\Entity\User      $user
	 * @param \AppBundle\Entity\Commission $commission
	 * @param                              $statusCode
	 *
	 * @return \AppBundle\Entity\CommissionUserCompanyRelation|null
	 */
	public function getCommissionUserCompany(User $user, Commission $commission, $statusCode){

		$company = $commission->getCompany();

		$criteria = array(
			'user' => $user,
			'company' => $company,
		);
		$ucr = $this->em->getRepository('AppBundle:UserCompany')->findOneBy($criteria);

		if($ucr){

			$criteria = array(
				'code' => $statusCode,
			);
			$status = $this->em->getRepository('AppBundle:UserCompanyTemporalityStatus')->findOneBy($criteria);

			if($ucr->getData()->getStatus() === $status){

				$criteria = array(
					'userCompany' => $ucr,
					'commission' => $commission,
				);
				$cucr = $this->em->getRepository('AppBundle:CommissionUserCompanyRelation')->findOneBy($criteria);

				return $cucr;

			}

		}

		return null;

	}

	/**
	 * Ověří zda-li zadaný uživatel má zadané oprávnění vůči zadané zakázce
	 *
	 * @param \UserBundle\Entity\User      $user
	 * @param \AppBundle\Entity\Commission $commission
	 * @param                              $statusCode
	 * @param array                        $roleTypeCodes
	 *
	 * @return bool
	 */
	public function checkCommissionUserCompanyManagedByRoleTypeCodes(User $user, Commission $commission, $statusCode, array $roleTypeCodes){

		$cucr = $this->getCommissionUserCompany($user, $commission, $statusCode);

		$type = $cucr->getCommissionUserCompanyRelationType();

		if(in_array($type->getCode(), $roleTypeCodes, true)){

			return true;

		}

		return false;

	}

	/**
	 * Vrátí kolekci zakázek daného uživatele všech společností, kde tento uživatel je aktuálně v povoleném stavu
	 *
	 * @param \UserBundle\Entity\User $user
	 *
	 * @return array
	 */
	public function getCommissionsOfUserInAllCompaniesWhereIsEnabled(User $user){

		$aus = [];

		$now = new \DateTime();
		$criteria = [
			'year' => $now->format('Y'),
			'month' => $now->format('n'),
		];
		$yearmonth = $this->em->getRepository('AppBundle:YearMonth')->findOneBy($criteria);
//		dump($yearmonth);

		/** @var UserCompany $userCompanyRelation */
		foreach($user->getUserCompanyRelations() as $userCompanyRelation){
			if($userCompanyRelation->getData()->getStatus()->getCode() === 'enabled'){
//				dump($userCompanyRelation->getAllocationUnits()->count());
//				dump($userCompanyRelation->getAllocationUnits());
				$result = $userCompanyRelation->getAllocationUnits()->filter(function($entry) use ($yearmonth){
//					dump($entry->getYearMonth());
					return (($entry->getYearMonth()->getId() === $yearmonth->getId() or $entry->getYearMonth()->getId() === $yearmonth->getId()-1) and $entry->getHoursPlan() > 0);
				});
//				dump($result);
				$aus = array_merge($aus, $result->toArray());
//				dump($aus);
			}
		}

		$commissions = [];

		/** @var AllocationUnit $au */
		foreach($aus as $au){

			$commission = $au->getCommission();

			if(!in_array($commission, $commissions, true)){
				$commissions[] = $commission;
			}

		}

		uasort($commissions, function($a, $b){
			return $a->getName() > $b->getName();
		});


//		$criteria = array(
//			'code' => 'enabled',
//		);
//		$ucts = $this->em->getRepository('AppBundle:UserCompanyTemporalityStatus')->findOneBy($criteria);
//		$commissions = $this->em->getRepository('AppBundle:Commission')->getCommissionOfUserInAllCompaniesOfUserCompanyRelationType($user, $ucts);
//
//		$commissions = array_filter($commissions, function($entry){
//			return ($entry->getStartDate()->getYear() == 2017 and $entry->getStartDate()->getMonth() == 1);
//		});

		return $commissions;

	}

	/**
	 * Vrátí kolekci zakázek daného uživatele všech společností, kde tento uživatel je aktuálně v povoleném stavu
	 * společně s oblíbenými zakázkami
	 *
	 * @param \UserBundle\Entity\User $user
	 *
	 * @return array
	 */
	public function getCommissionsOfUserInAllCompaniesWhereIsEnabledWithFavourites(User $user){

		$commissions = $this->getCommissionsOfUserInAllCompaniesWhereIsEnabled($user);
		$favouriteCommisions = $user->getFavouriteCommissions();
		$fcs = array();
		foreach($favouriteCommisions as $fc){
			$commission = $fc->getCommission();
			if(in_array($commission, $commissions)){
				$fcs[] = $commission;
			}
		}

		$data = [
			'Oblíbené' => $fcs,
			'Všechny' => $commissions,
		];

		return $data;

	}

	private function filterCommissionsWhereParticipate(User $user, $commissions){

		$cs = [];

		/** @var Commission $commission */
		foreach($commissions as $commission){
			$campaign = $commission->getCampaign();
			$client = $campaign->getClient();
			$company = $client->getCompany();
			$companyGroup = $campaign->getCompanyGroup();

			$criteria = [
				'user' => $user,
				'company' => $company,
			];
			$userCompany = $this->em->getRepository('AppBundle:UserCompany')->findOneBy($criteria);
			if(!$userCompany){
				continue;
			}
			$uct = $userCompany->getData();

			//Jsi-li člověkem ve společnosti v ne-editovatelný roli
			if($uct->getRole()->getNoneditable()){
				$cs[] = $commission;
			}else{

				//Nebo jsi-li adminem v pracovní skupině kampaně pod který spadá job
				$isCgAdmin = false;
				$cgAdmins = $companyGroup->getCompanyGroupUserCompanyRelationsOfTemporalityStatus('admin');
				/** @var CompanyGroupUserCompanyRelation $cgAdmin */
				foreach($cgAdmins as $cgAdmin){
					if($cgAdmin->getUserCompany()->getUser() === $user){
						$isCgAdmin = true;
						break 1;
					}
				}
				if($isCgAdmin){
					$cs[] = $commission;
					continue;
				}

				//Nebo jsi-li ownerem, či jobmanagerem kampaně pod kterou spadá job
				/** @var CampaignManager $campaignManager */
				foreach($campaign->getCampaignManagers() as $campaignManager){
					if($campaignManager->getUserCompany()->getUser() === $user){
						if($campaignManager->getJobManager() or $campaignManager->getOwner()){
							$cs[] = $commission;
						}
					}
				}

			}
		}

		return $cs;

	}

	public function getCommissionsWhereParticipate(User $user){

		//Všechny zakázky ze všech společností, kde uživatel je povolen
		$commissions = $this->em->getRepository('AppBundle:Commission')->getCommissionOfUser($user);

		return $this->filterCommissionsWhereParticipate($user, $commissions);

	}

	public function getCommissionsWhereParticipateInTimeWindow(User $user){

		//Všechny zakázky ze všech společností, kde uživatel je povolen
		$commissions = $this->em->getRepository('AppBundle:Commission')->getCommissionOfUserInAllCompaniesInTimeWindow($user);

		return $this->filterCommissionsWhereParticipate($user, $commissions);
//


	}


}