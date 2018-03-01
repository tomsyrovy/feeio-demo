<?php
	/**
	 * Project: feeio2
	 * File: PhpExcelGeneratorSubcommission.php
	 * Author: Tomas SYROVY <tomas@syrovy.pro>
	 * Date: 02.08.16
	 * Version: 1.0
	 */

	namespace PhpExcel;


	use AppBundle\DependencyInjection\CompanyManager;
	use AppBundle\Entity\CostRepository;
	use AppBundle\Entity\Subcommission;
	use AppBundle\Entity\SubcommissionTeamUserCompany;
	use AppBundle\Entity\SubcommissionTemporality;
	use AppBundle\Entity\Timesheet;
	use AppBundle\Entity\UserCompany;

	class PhpExcelGeneratorSubcommission extends PhpExcelGenerator{

		private $ucrs;

		/**
		 * @var CostRepository
		 */
		private $costRepository;

		/**
		 * @var array
		 */
		private $auxiliaryArray;

		/**
		 * @return Subcommission
		 */
		private function getData(){
			return $this->data;
		}

		public function generate(){

			$companyManager = new CompanyManager($this->em);
			$this->ucrs = $companyManager->getUserCompanyRelationsInYearmonthOfStatusCode($this->getData()->getCommission()->getCompany(), $this->getData()->getYearmonth(), 'enabled');

			$this->costRepository = $this->em->getRepository('AppBundle:Cost');

			$this->auxiliaryArray = [];

			$this->getPhpExcelObject()->setActiveSheetIndex(0);
			$this->setActiveSheet($this->getPhpExcelObject()->getActiveSheet());

			//PLAN
			$this->setSheetPlanHeader(0, 1);

			$this->setFinancialPlanLabels(0, 5);
			$this->setFinancialPlanData(1, 6);

			$this->setTeamPlanLabels(3, 5);
			$this->setTeamPlanData(4, 7);

			$this->setPerformancePlanLabels(0, 16);
			$this->setPerformancePlanData(1, 17);

			//REAL
			$rowOffset = 8+count($this->ucrs);
			$this->setSheetRealHeader(0, 1+$rowOffset);

			$this->setFinancialRealLabels(0, 5+$rowOffset);
			$this->setFinancialRealData(1, 6+$rowOffset);

			$this->setTeamRealLabels(3, 5+$rowOffset);
			$this->setTeamRealData(4, 7+$rowOffset);

			$this->setPerformanceRealLabels(0, 16+$rowOffset);
			$this->setPerformanceRealData(1, 17+$rowOffset);

			parent::finalize();

		}

		private function setSheetPlanHeader($iC = 0, $iR = 1){

			$this->setSheetGeneralHeader($iC, $iR, 'PLÁN');

		}

		private function setFinancialPlanLabels($iC = 0, $iR = 5){

			$c = $iC;
			$r = $iR;

			$this->setCellValue($c, $r++,  'Finance', 'header_2');
			$this->setCellValue($c, $r++,  'Výnosy', 'header_4');
			$this->setCellValue($c, $r++,  'Náklady', 'header_4');
			$this->setCellValue($c, $r++,  'VH', 'header_4');
			$this->setCellValue($c, $r++,  'Celkové fee', 'header_5');
			$this->setCellValue($c, $r++,  'Fixní fee', 'normal', 1);
			$this->setCellValue($c, $r++,  'Success fee', 'normal', 1);
			$this->setCellValue($c, $r++,  'Přefakturace', 'header_5');
			$this->setCellValue($c, $r++,  'Nákup', 'normal', 1);
			$this->setCellValue($c, $r++,  'Prodej', 'normal', 1);

		}

		private function setFinancialPlanData($iC = 1, $iR = 6){

			/** @var SubcommissionTemporality $fData */
			$fData = $this->getData()->getData();

			$fixFeePlan = $fData->getFeeFixPlan();
			$successFeePlan = $fData->getFeeSuccessPlan();

			$cData = $this->getCostPlanData();
			if(array_key_exists(0, $cData)){
				$cData = $cData[0];
				$costsBuyed = $cData['priceNonVatPlan'];
				$costsSold = $cData['rebillingPriceNonVatPlan'];
			}else{
				$costsBuyed = 0;
				$costsSold = 0;
			}

			$totalFeePlan = (int)$fixFeePlan + (int)$successFeePlan;
			$earnings = $totalFeePlan+$costsSold;
			$rebilling = $costsSold-$costsBuyed;
			$profit = $earnings-$costsBuyed;

			$c = $iC;
			$r = $iR;

			$this->setCellValue($c, $r++, $earnings, 'normal');
			$this->setCellValue($c, $r++, $costsBuyed, 'normal');
			$this->setCellValue($c, $r++, $profit, 'normal');
			$this->setCellValue($c, $r++, $totalFeePlan, 'normal');
			$this->setCellValue($c, $r++, $fixFeePlan, 'normal');
			$this->setCellValue($c, $r++, $successFeePlan, 'normal');
			$this->setCellValue($c, $r++, $rebilling, 'normal');
			$this->setCellValue($c, $r++, $costsBuyed, 'normal');
			$this->setCellValue($c, $r++, $costsSold, 'normal');

		}

		private function getCostPlanData(){
			$criteria = [
				'commission' => $this->getData()->getCommission(),
				'yearmonthPlan' => $this->getData()->getYearmonth(),
			];
			$result = $this->costRepository->findByGroupedBy($criteria, [], 'commission', 'id');
			return $result;
		}

		private function setTeamPlanLabels($iC = 3, $iR = 5){

			$c = $iC;
			$r = $iR;

			$this->setCellValue($c++, $r++, 'Tým', 'header_2');

			$this->setCellValue($c++, $r, 'Hodiny', 'header_4');
			$this->setCellValue($c++, $r, 'Externí sazba', 'header_4');
			$this->setCellValue($c++, $r, 'Interní sazba', 'header_4');
			$this->setCellValue($c++, $r, 'Externí sazba celkem', 'header_4');
			$this->setCellValue($c++, $r, 'Interní sazba celkem', 'header_4');

			$c = $iC;
			$r = $iR+2;
			/** @var UserCompany $ucr */
			foreach($this->ucrs as $ucr){
				$this->setCellValue($c, $r++, $ucr->getUser()->getFullName(), 'normal');
			}
			$this->setCellValue($c, $r++, 'Celkem', 'header_4');

		}

		private function setTeamPlanData($iC = 4, $iR = 7){

			$members = $this->getData()->getTeamData()->getMembers();

			$grandTotalHours = 0;
			$grandTotalRateExternal = 0;
			$grandTotalRateInternal = 0;

			/** @var SubcommissionTeamUserCompany $member */
			foreach($members as $member){

				$position = array_search($member->getUserCompany(), array_values($this->ucrs), true);

				$c = $iC;
				$r = $iR+$position;

				$hours = $member->getHours();
				$rateExternal = $member->getRateExternal();
				$rateInternal = $member->getRateInternal();
				$totalRateExternal = $hours*$rateExternal;
				$totalRateInternal = $hours*$rateInternal;

				$grandTotalHours += $hours;
				$grandTotalRateExternal += $totalRateExternal;
				$grandTotalRateInternal += $totalRateInternal;

				$this->setCellValue($c++, $r, $hours, 'normal');
				$this->setCellValue($c++, $r, $rateExternal, 'normal');
				$this->setCellValue($c++, $r, $rateInternal, 'normal');
				$this->setCellValue($c++, $r, $totalRateExternal, 'normal');
				$this->setCellValue($c++, $r, $totalRateInternal, 'normal');

			}

			$c = $iC;
			$r = $iR+count($this->ucrs);

			$this->setCellValue($c++, $r, $grandTotalHours, 'header_5');

			$c = $iC+3;
			$this->setCellValue($c++, $r, $grandTotalRateExternal, 'header_5');
			$this->setCellValue($c++, $r, $grandTotalRateInternal, 'header_5');

			$this->auxiliaryArray['grandTotalHours'] = $grandTotalHours;
			$this->auxiliaryArray['grandTotalRateExternal'] = $grandTotalRateExternal;
			$this->auxiliaryArray['grandTotalRateInternal'] = $grandTotalRateInternal;

		}

		private function setPerformancePlanLabels($iC = 0, $iR = 16){

			$c = $iC;
			$r = $iR;

			$this->setCellValue($c, $r++,  'Výkon', 'header_2');
			$this->setCellValue($c, $r++,  'Hodiny', 'header_4');
			$this->setCellValue($c, $r++,  'Externí sazba', 'header_4');
			$this->setCellValue($c, $r++,  'Interní sazba', 'header_4');
			$this->setCellValue($c, $r++,  'Hodiny týmu', 'header_4');

		}

		private function setPerformancePlanData($iC = 1, $iR = 17){

			$hours = $this->getData()->getData()->getHoursPlan();
			$exRate = $this->auxiliaryArray['grandTotalRateExternal'];
			$inRate = $this->auxiliaryArray['grandTotalRateInternal'];
			$teamHours = $this->auxiliaryArray['grandTotalHours'];

			$c = $iC;
			$r = $iR;

			$this->setCellValue($c, $r++,  $hours, 'normal');
			$this->setCellValue($c, $r++,  $exRate, 'normal');
			$this->setCellValue($c, $r++,  $inRate, 'normal');
			$this->setCellValue($c, $r++,  $teamHours, 'normal');

		}

		private function setSheetRealHeader($iC, $iR){

			$this->setSheetGeneralHeader($iC, $iR, 'REÁL');

		}

		private function setFinancialRealLabels($iC, $iR){

			$this->setFinancialPlanLabels($iC, $iR);

		}

		private function setFinancialRealData($iC, $iR){

			/** @var SubcommissionTemporality $fData */
			$fData = $this->getData()->getData();

			$fixFeeReal = $fData->getFeeFixReal();
			$successFeeReal = $fData->getFeeSuccessReal();

			$cData = $this->getCostRealData();
			if(array_key_exists(0, $cData)){
				$cData = $cData[0];
				$costsBuyed = $cData['priceNonVatReal'];
				$costsSold = $cData['rebillingPriceNonVatReal'];
			}else{
				$costsBuyed = 0;
				$costsSold = 0;
			}

			$totalFeeReal = (int)$fixFeeReal + (int)$successFeeReal;
			$earnings = $totalFeeReal+$costsSold;
			$rebilling = $costsSold-$costsBuyed;
			$profit = $earnings-$costsBuyed;

			$c = $iC;
			$r = $iR;

			$this->setCellValue($c, $r++, $earnings, 'normal');
			$this->setCellValue($c, $r++, $costsBuyed, 'normal');
			$this->setCellValue($c, $r++, $profit, 'normal');
			$this->setCellValue($c, $r++, $totalFeeReal, 'normal');
			$this->setCellValue($c, $r++, $fixFeeReal, 'normal');
			$this->setCellValue($c, $r++, $successFeeReal, 'normal');
			$this->setCellValue($c, $r++, $rebilling, 'normal');
			$this->setCellValue($c, $r++, $costsBuyed, 'normal');
			$this->setCellValue($c, $r++, $costsSold, 'normal');

		}

		private function getCostRealData(){
			$criteria = [
				'commission' => $this->getData()->getCommission(),
				'yearmonthReal' => $this->getData()->getYearmonth(),
			];
			$result = $this->costRepository->findByGroupedBy($criteria, [], 'commission', 'id');
			return $result;
		}

		private function setTeamRealLabels($iC, $iR){

			$this->setTeamPlanLabels($iC, $iR);

		}

		private function setTeamRealData($iC, $iR){

			$company = $this->getData()->getCommission()->getCompany();
			$subcommissionTeam = $this->getData()->getTeamData();

			$grandTotalHours = 0;
			$grandTotalRateExternal = 0;
			$grandTotalRateInternal = 0;

			$criteria = [
				'commission' => $this->getData()->getCommission(),
				'yearmonth' => $this->getData()->getYearmonth(),
			];
			$timesheets = $this->em->getRepository('AppBundle:Timesheet')->findByGroupedBy($criteria, [], 'author', 'id');

			foreach($timesheets as $timesheet){ //pro každý timesheet

				$user = $timesheet['entity']->getAuthor(); //získej autora

				$criteria = [
					'user' => $user,
					'company' => $company
				];
				$userCompanyRelation = $this->em->getRepository('AppBundle:UserCompany')->findOneBy($criteria); //získej vztah autora vůči společnosti (společnost pod kterou subzakázka spadá)

				if($userCompanyRelation){ //pokud vztah existuje

					$position = array_search($userCompanyRelation, array_values($this->ucrs), true);

					$c = $iC;
					$r = $iR+$position;

					$hours = round($timesheet['duration']/60*100)/100;
					$this->setCellValue($c++, $r, $hours, 'normal');

					$criteria = [
						'userCompany' => $userCompanyRelation,
						'subcommissionTeam' => $subcommissionTeam
					];
					$member = $this->em->getRepository('AppBundle:SubcommissionTeamUserCompany')->findOneBy($criteria); //získej členství vztahu vůči týmu subzakázky

					if($member){ //pokud členství existuje

						$rateExternal = $member->getRateExternal();
						$rateInternal = $member->getRateInternal();
						$totalRateExternal = $hours*$rateExternal;
						$totalRateInternal = $hours*$rateInternal;

						$grandTotalHours += $hours;
						$grandTotalRateExternal += $totalRateExternal;
						$grandTotalRateInternal += $totalRateInternal;

						$this->setCellValue($c++, $r, $rateExternal, 'normal');
						$this->setCellValue($c++, $r, $rateInternal, 'normal');
						$this->setCellValue($c++, $r, $totalRateExternal, 'normal');
						$this->setCellValue($c++, $r, $totalRateInternal, 'normal');

					}

				}

			}

			$c = $iC;
			$r = $iR+count($this->ucrs);

			$this->setCellValue($c++, $r, $grandTotalHours, 'header_5');

			$c = $iC+3;
			$this->setCellValue($c++, $r, $grandTotalRateExternal, 'header_5');
			$this->setCellValue($c++, $r, $grandTotalRateInternal, 'header_5');

			$this->auxiliaryArray['grandTotalHours'] = $grandTotalHours;
			$this->auxiliaryArray['grandTotalRateExternal'] = $grandTotalRateExternal;
			$this->auxiliaryArray['grandTotalRateInternal'] = $grandTotalRateInternal;

		}

		private function setPerformanceRealLabels($iC, $iR){

			$this->setPerformancePlanLabels($iC, $iR);

		}

		private function setPerformanceRealData($iC, $iR){

			$hours = '';
			$exRate = $this->auxiliaryArray['grandTotalRateExternal'];
			$inRate = $this->auxiliaryArray['grandTotalRateInternal'];
			$teamHours = $this->auxiliaryArray['grandTotalHours'];

			$c = $iC;
			$r = $iR;

			$this->setCellValue($c, $r++,  $hours, 'normal');
			$this->setCellValue($c, $r++,  $exRate, 'normal');
			$this->setCellValue($c, $r++,  $inRate, 'normal');
			$this->setCellValue($c, $r++,  $teamHours, 'normal');

		}

		private function setSheetGeneralHeader($iC, $iR, $label){

			$c = $iC;
			$r = $iR;

			$this->setCellValue($c, $r++, $this->getData()->getCode(), 'header_1');
			$this->setCellValue($c, $r++, $label, 'header_2');

			$c = $iC+1;
			$r = $iR;

			$this->setCellValue($c, $r++, $this->getData()->getCommission()->getClient()->getTitle(), 'header_3');
			$this->setCellValue($c, $r++, $this->getData()->getCommission()->getCompany()->getName(), 'header_4');
			if($this->getData()->getCommission()->getCompanyGroup()){
				$this->setCellValue($c, $r++, $this->getData()->getCommission()->getCompanyGroup()->getName(), 'header_4');
			}

		}

	}