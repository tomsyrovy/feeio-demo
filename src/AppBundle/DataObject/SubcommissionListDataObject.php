<?php
	/**
	 * Project: feeio2
	 * File: SubcommissionListDataObject.php
	 * Author: Tomas SYROVY <tomas@syrovy.pro>
	 * Date: 29.07.16
	 * Version: 1.0
	 */

	namespace AppBundle\DataObject;


	use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
	use AppBundle\Entity\Commission;
	use AppBundle\Entity\TimeWindow;
	use Doctrine\ORM\EntityManager;
	use UserBundle\Entity\User;

	class SubcommissionListDataObject {

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

		public function getData(TimeWindow $timeWindow = null){

			$subcommissions = $this->commission->getSubcommissionsInTimeWindow($timeWindow);

			//Práce s tabulkou
			$criteria = array(
				'code' => 'table-subcommissions',
			);
			$table = $this->em->getRepository('TableBundle:TableEntity')->findOneBy($criteria);

			$criteria = array(
				'user' => $this->user,
				'tableEntity' => $table,
			);
			$userColumns = $this->em->getRepository('TableBundle:UserColumn')->findBy($criteria);

			$criteria = array(
				'user' => $this->user,
			);
			$userDefaultColumns = $this->em->getRepository('TableBundle:UserDefaultColumn')->findBy($criteria);

			$udcs = array();
			foreach($userDefaultColumns as $udc){

				$udcs[$udc->getTableDefaultColumn()->getId()] = $udc;

			}

			$data = array(
				'subcommissions' => $subcommissions,
				'table' => $table,
				'userColumns' => $userColumns,
				'userDefaultColumns' => $udcs,
			);

			return $data;

		}

	}