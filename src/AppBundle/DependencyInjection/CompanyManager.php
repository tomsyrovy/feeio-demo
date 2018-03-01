<?php
	/**
	 * Project: feeio2
	 * File: CompanyManager.php
	 * Author: Tomas SYROVY <tomas@syrovy.pro>
	 * Date: 02.08.16
	 * Version: 1.0
	 */

	namespace AppBundle\DependencyInjection;


	use AppBundle\Entity\Company;
	use AppBundle\Entity\UserCompany;
	use AppBundle\Entity\UserCompanyTemporality;
	use AppBundle\Entity\YearMonth;
	use Doctrine\ORM\EntityManager;

	class CompanyManager {

		/**
		 * @var \Doctrine\Common\Persistence\ObjectManager
		 */
		private $em;

		/**
		 * CompanyManager constructor.
		 *
		 * @param \Doctrine\ORM\EntityManager $em
		 */
		public function __construct( EntityManager $em ){
			$this->em = $em;
		}

		/**
		 * Vrátí seznam vazeb uživatel - společnost zadané společnosti, kteří v zadaném YearMonth byli v zadaném stavu
		 *
		 * @param \AppBundle\Entity\Company   $company
		 * @param \AppBundle\Entity\YearMonth $yearMonth
		 * @param string                      $statusCode
		 *
		 * @return array
		 */
		public function getUserCompanyRelationsInYearmonthOfStatusCode(Company $company, YearMonth $yearMonth, $statusCode = 'enabled'){

			$result = [];

			$ucrs = $company->getUserCompanyRelations();

			//definuj periodu pro výběr
			$month = $yearMonth->getMonth();
			$year = $yearMonth->getYear();
			$ymFrom = new \DateTime($year.'-'.$month.'-01');
			$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			$ymUntil = new \DateTime($year.'-'.$month.'-'.$days);

			/** @var UserCompany $ucr */
			foreach($ucrs as $ucr){ //pro každou vazbu uživatel - společnost
				$temporalities = $ucr->getTemporalities(); //získej všechny temporality
				/** @var UserCompanyTemporality $temporality */
				foreach($temporalities as $temporality){ //pro každou temporalitu
					if($temporality->getStatus()->getCode() === $statusCode){ //ověř, zda-li má status dle zadání a pokud ano, pak
						$tFrom = $temporality->getFrom(); // získej počáteční datum temporality
						$tUntil = $temporality->getUntil(); // získej konečné datum temporality
						if($tUntil === null){ //není-li temporalita ukončená, tak simuluj ukončovací datum jako ukončovací datum periody výběru
							$tUntil = clone $ymUntil;
						}
						if(Tool::datesOverlap($ymFrom, $ymUntil, $tFrom, $tUntil)){ //překrývá se temporalita a perioda výběru
							$result[] = $ucr; //pokud ano, tak ulož uživatele
						}
					}
				}
			}

			//seřaď dle příjmení
			uasort($result, function($a, $b){
				return $a->getUser()->getLastname() > $b->getUser()->getLastname();
			});

			return $result;

		}

	}