<?php
	/**
	 * Project: feeio2
	 * File: TimesheetListDataObject.php
	 * Author: Tomas SYROVY <tomas@syrovy.pro>
	 * Date: 29.07.16
	 * Version: 1.0
	 */

	namespace AppBundle\DataObject;


	use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
	use AppBundle\Entity\Commission;
	use AppBundle\Entity\TimeWindow;
	use Doctrine\ORM\EntityManager;
	use TableBundle\DependencyInjection\TableData;
	use UserBundle\Entity\User;

	class TimesheetListDataObject {

		/**
		 * @var EntityManager
		 */
		private $em;

		/**
		 * @var CommissionManager
		 */
		private $cm;

		/**
		 * @var User
		 */
		private $user;

		/**
		 * @var Commission
		 */
		private $commission;

		private $timesheets;

		private $tableData;

		/**
		 * TimesheetListDataObject constructor.
		 *
		 * @param EntityManager               $em
		 * @param CommissionManager $cm
		 * @param User              $user
		 * @param Commission        $commission
		 */
		public function __construct( EntityManager $em, CommissionManager $cm, User $user, Commission $commission ){
			$this->em         = $em;
			$this->cm         = $cm;
			$this->user       = $user;
			$this->commission = $commission;
		}

		/**
		 * @param \AppBundle\Entity\TimeWindow|null $timeWindow
		 *
		 * @return array
		 */
		public function getData(TimeWindow $timeWindow = null){

			$repository = $this->em->getRepository('AppBundle:Timesheet');

			if($timeWindow !== null){
				$this->timesheets = $repository->findByCommissionAndTimeWindow($this->commission, $timeWindow);
			}else{
				$this->timesheets = $repository->findByCommission($this->commission);
			}

			$this->tableData = TableData::getData($this->em, $this->user, 'table-timesheetlist');

			$data = [
				'timesheets' => $this->timesheets,
				'tableData' => $this->tableData,
			];

			return $data;

		}

	}