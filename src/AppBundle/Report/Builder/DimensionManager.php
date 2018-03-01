<?php
	/**
	 * Project: feeio2
	 * File: DimensionManager.php
	 * Author: Tomas SYROVY <tomas@syrovy.pro>
	 * Date: 06.09.16
	 * Version: 1.0
	 */

	namespace AppBundle\Report\Builder;


	use AppBundle\DependencyInjection\Authorization\AuthorizationIndividual;
	use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
	use AppBundle\Entity\CommissionUserCompanyRelation;
	use AppBundle\Entity\Company;
	use AppBundle\Entity\ReportConfiguration;
	use AppBundle\Entity\UserCompany;
	use Doctrine\ORM\EntityManager;
	use UserBundle\Entity\User;

	class DimensionManager {

		/**
		 * @var EntityManager
		 */
		private $entityManager;

		/**
		 * @var AuthorizationIndividual
		 */
		private $authorizationIndividual;

		/**
		 * @var CommissionManager
		 */
		private $commissionManager;

		/**
		 * @var User
		 */
		private $user;

		/**
		 * @var array
		 */
		private $commissionsOfUser;

		/**
		 * DimensionManager constructor.
		 *
		 * @param \Doctrine\ORM\EntityManager $entityManager
		 * @param \UserBundle\Entity\User     $user
		 */
		public function __construct( EntityManager $entityManager, User $user ){
			$this->entityManager = $entityManager;
			$this->user          = $user;
			$this->init();
		}

		private function init(){
			$this->authorizationIndividual = new AuthorizationIndividual($this->entityManager);
			$this->commissionManager = new CommissionManager($this->entityManager);

			//Zakázky uživatele
			$this->commissionsOfUser = $this->commissionManager->getCommissionsOfUser($this->user, 'enabled', ['admin', 'observer']);
		}

		public function getCompanies(ReportConfiguration $reportConfiguration){
			$companies = $this->authorizationIndividual->getCompaniesWhereUserHasAuthorizationCode($reportConfiguration->getCode(), $this->user);

			return $companies;
		}

		public function getCommissions(ReportConfiguration $reportConfiguration){

			//Vyber všechny zakázky společnosti, kde přihlášený uživatel má dané právo
			$companies = $this->getCompanies($reportConfiguration);
			$commissions = [];
			/** @var Company $company */
			foreach($companies as $company){
				$commissions = array_merge($commissions, $company->getCommissions()->toArray());
			}

			//Uděláme průnik se zakázkami společnosti
			$commissions = array_intersect($commissions, $this->commissionsOfUser);

			return $commissions;

		}

		public function getUsers(ReportConfiguration $reportConfiguration){

			//Vyber všechny uživatele společnosti
			$companies = $this->getCompanies($reportConfiguration);
			$users = [];
			/** @var Company $company */
			foreach($companies as $company){
				$ucrs = $company->getUserCompanyRelations();
				/** @var UserCompany $ucr */
				foreach($ucrs as $ucr){
					$user = $ucr->getUser();
					$users[] = $user;
				}
			}

			return $users;

		}

	}