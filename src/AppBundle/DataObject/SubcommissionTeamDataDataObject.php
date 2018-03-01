<?php
/**
 * Project: feeio2
 * File: SubcommissionTeamDataDataObject.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 06.12.15
 * Version: 1.0
 */

namespace AppBundle\DataObject;


use AppBundle\Entity\SubcommissionTeam;
use AppBundle\Entity\SubcommissionTeamUserCompany;
use AppBundle\Entity\Timesheet;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\VarDumper\VarDumper;

class SubcommissionTeamDataDataObject {
	
	private $hoursPlan = 0;

	private $hoursReal = 0;

	private $rateInternal = 0;

	private $rateExternal = 0;

	private $totalRateInternalPlan = 0;

	private $totalRateInternalReal = 0;

	private $totalRateExternalPlan = 0;

	private $totalRateExternalReal = 0;

	public function processSumOfSubcommissionTeam(SubcommissionTeam $subcommissionTeam){

		foreach($subcommissionTeam->getMembers() as $member){

			$hoursRealOfMember = $this->calculateHoursRealOfMember($member);

			$this->hoursPlan += $member->getHours();
			$this->hoursReal += $hoursRealOfMember;
			$this->rateInternal += $member->getRateInternal();
			$this->rateExternal += $member->getRateExternal();
			$this->totalRateInternalPlan += ($member->getRateInternal()*$member->getHours());
			$this->totalRateInternalReal += ($member->getRateInternal()*$hoursRealOfMember);
			$this->totalRateExternalPlan += ($member->getRateExternal()*$member->getHours());
			$this->totalRateExternalReal += ($member->getRateExternal()*$hoursRealOfMember);

		}

	}

	/**
	 * @param \AppBundle\Entity\SubcommissionTeamUserCompany $member
	 *
	 * @return float|int
	 */
	private function calculateHoursRealOfMember(SubcommissionTeamUserCompany $member){

		$hr = 0; //default value

		$subcommission = $member->getSubcommissionTeam()->getSubcommission();
		$commission = $subcommission->getCommission();
		$yearmonth = $subcommission->getYearmonth();
		$user = $member->getUserCompany()->getUser();

		$timesheets = $yearmonth->getTimesheets()->filter(function($entry) use ($commission, $user){
			return ($entry->getCommission() === $commission and $entry->getAuthor() === $user) ? true : false;
		});

		foreach($timesheets as $timesheet){
			$hr += $timesheet->getDuration();
		}

		$hr = round($hr/60, 2);

		return $hr;

	}

	/**
	 * @return mixed
	 */
	public function getHoursPlan(){
		return $this->hoursPlan;
	}

	/**
	 * @return mixed
	 */
	public function getHoursReal(){
		return $this->hoursReal;
	}

	/**
	 * @return mixed
	 */
	public function getRateInternal(){
		return $this->rateInternal;
	}

	/**
	 * @return mixed
	 */
	public function getRateExternal(){
		return $this->rateExternal;
	}

	/**
	 * @return mixed
	 */
	public function getTotalRateInternalPlan(){
		return $this->totalRateInternalPlan;
	}

	/**
	 * @return mixed
	 */
	public function getTotalRateInternalReal(){
		return $this->totalRateInternalReal;
	}

	/**
	 * @return mixed
	 */
	public function getTotalRateExternalPlan(){
		return $this->totalRateExternalPlan;
	}

	/**
	 * @return mixed
	 */
	public function getTotalRateExternalReal(){
		return $this->totalRateExternalReal;
	}

}