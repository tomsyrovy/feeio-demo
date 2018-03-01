<?php
	/**
	 * Project: feeio2
	 * File: QueryManager.php
	 * Author: Tomas SYROVY <tomas@syrovy.pro>
	 * Date: 08.08.16
	 * Version: 1.0
	 */

	namespace AppBundle\Report\Query;


	use AppBundle\Entity\TimeWindow;
	use AppBundle\Report\Query\Builder\QueryBuilder;
	use Doctrine\ORM\EntityManager;

	class QueryManager {

		/**
		 * @var EntityManager
		 */
		private $em;

		/**
		 * @var QueryBuilder
		 */
		private $queryBuilder;

		/**
		 * QueryManager constructor.
		 *
		 * @param \Doctrine\ORM\EntityManager $em
		 */
		public function __construct( EntityManager $em ){
			$this->em = $em;
		}

		/**
		 * @param string $sql
		 * @param array  $columns
		 *
		 * @return array
		 * @throws \Doctrine\DBAL\DBALException
		 */
		public function execute(string $sql, array $columns = []){

			$stmt = $this->em->getConnection()->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll();

			if(count($columns) !== 0){
				return $this->cleanResult($result, $columns);
			}

			return $result;

		}

		/**
		 * Čistí výsledek dotazu podle toho, jaké mají být zachovány sloupce.
		 *
		 * @param array $result Výsledek dotazu
		 * @param array $columns Sloupce (jejich názvy), které mají být zachovány
		 *
		 * @return array
		 */
		public function cleanResult(array $result, array $columns){
			$result = array_map(
				function($entry) use ($columns){
					return array_intersect_key($entry, array_flip($columns));
				},
				$result
			);
			return $result;
		}

		/**
		 * Konvertuje zadané sloupce zadaného výsledku na datový typ integer
		 *
		 * @param array $result
		 * @param array $fields
		 *
		 * @return array
		 */
		public function convertToInt(array $result, array $fields){
			foreach($result as $index => $row){
				foreach($row as $field => $value){
					if(in_array($field, $fields, true)){
						$result[$index][$field] = (int)$value;
					}
				}
			}
			return $result;
		}

		public function getSubcommissions(){
			$this->init();
			$this->queryBuilder->table('SubcommissionTemporality', 'sct');
			$this->queryBuilder->criteria([
				'AND',
				'sct.date_until IS NULL',
			]);
			$this->joinSubcommissions('sct.subcommission_id');
			$this->joinCommissions('sc.commission_id');
			$this->joinCompanyGroups('c.companygroup_id');
			$this->joinClients('c.client_id');
			$this->joinCompanies('cl.company_id');
			return $this->queryBuilder->get();
		}

		public function getTimesheets(){
			$this->init();
			$this->queryBuilder->table('Timesheet', 't');
			$this->joinCommissions('t.commission_id');
			$this->joinCampaigns('c.campaign_id');
			$this->joinCompanyGroups('ca.companygroup_id');
			$this->joinClients('ca.client_id');
			$this->joinCompanies('cl.company_id');
			$this->joinUsers('t.author_id');
			return $this->queryBuilder->get();
		}

		public function getAllocations(){
			$this->init();
			$this->queryBuilder->table('AllocationContainerListItem', 'acli');
			$this->joinAllocationContainerLists('acli.allocationContainerList_id');
			$this->joinAllocationContainers('acl.allocationContainer_id', [
				'ac.yearmonth_id IS NOT NULL'
			]);
			$this->joinCommissions('ac.commission_id');
			$this->joinCampaigns('c.campaign_id');
			$this->joinCompanyGroups('ca.companygroup_id');
			$this->joinClients('ca.client_id');
			$this->joinCompanies('cl.company_id');
			$this->joinYearMonths('ac.yearmonth_id');
			$this->queryBuilder->select([
				'acli_totalBuyingPricePlan' => '(acli.buyingPricePlan*acli.quantityPlan)',
				'acli_totalSellingPricePlan' => '(acli.sellingPricePlan*acli.quantityPlan)',
				'acli_profitPlan' => '(acli.sellingPricePlan-acli.buyingPricePlan)',
				'acli_totalProfitPlan' => '((acli.sellingPricePlan-acli.buyingPricePlan)*acli.quantityPlan)',
				'acli_totalBuyingPriceReal' => '(acli.buyingPriceReal*acli.quantityReal)',
				'acli_totalSellingPriceReal' => '(acli.sellingPriceReal*acli.quantityReal)',
				'acli_profitReal' => '(acli.sellingPriceReal-acli.buyingPriceReal)',
				'acli_totalProfitReal' => '((acli.sellingPriceReal-acli.buyingPriceReal)*acli.quantityReal)',
			]);
			return $this->queryBuilder->get();
		}

		public function getTimesheetsInTimeWindow(TimeWindow $timeWindow){
			$this->getTimesheets();
			$this->joinYearMonths('t.yearmonth_id', $this->prepareYearMonthCriteria($timeWindow));
			return $this->queryBuilder->get();
		}
//
//		public function getTimesheetsInYearsAndMonthsAggregatedDurationViaYearsAndMonths(array $years = [], array $months = []){
//			$this->getTimesheetsInYearsAndMonths($years, $months);
//			$this->queryBuilder->aggregate(
//				[
//					't_sum_duration' => 'SUM(t.duration)',
//				],
//				'ym_id'
//			);
//			return $this->queryBuilder->get();
//		}
//
//		public function getTimesheetsInYearsAndMonthsAggregatedDurationViaYearsAndMonthsAndUsers(array $years = [], array $months = []){
//			$this->getTimesheetsInYearsAndMonths($years, $months);
//			$this->queryBuilder->aggregate(
//				[
//					't_sum_duration' => 'SUM(t.duration)',
//					't_count_duration' => 'COUNT(t.id)',
//				],
//				'ym_id, u_id'
//			);
//			return $this->queryBuilder->get();
//		}

		public function getSubcommissionsInTimeWindow(TimeWindow $timeWindow){
			$this->getSubcommissions();
			$this->joinYearMonths('sc.yearmonth_id', $this->prepareYearMonthCriteria($timeWindow));
			return $this->queryBuilder->get();
		}

		private function init(){
			$this->queryBuilder = new QueryBuilder($this);
		}

		private function joinSubcommissions($referenceColumn, $criteria = null){
			$this->queryBuilder->join('Subcommission', 'sc', $referenceColumn.' = sc.id');
			if($criteria !== null){
				$this->queryBuilder->criteria($criteria);
			}
		}

		private function joinAllocationContainerListItems($referenceColumn, $criteria = null){
			$this->queryBuilder->join('AllocationContainerListItem', 'acli', $referenceColumn.' = acli.id');
			if($criteria !== null){
				$this->queryBuilder->criteria($criteria);
			}
		}

		private function joinAllocationContainerLists($referenceColumn, $criteria = null){
			$this->queryBuilder->join('AllocationContainerList', 'acl', $referenceColumn.' = acl.id');
			if($criteria !== null){
				$this->queryBuilder->criteria($criteria);
			}
		}

		private function joinAllocationContainers($referenceColumn, $criteria = null){
			$this->queryBuilder->join('AllocationContainer', 'ac', $referenceColumn.' = ac.id');
			if($criteria !== null){
				$this->queryBuilder->criteria($criteria);
			}
		}

		private function joinCommissions($referenceColumn, $criteria = null){
			$this->queryBuilder->join('Commission', 'c', $referenceColumn.' = c.id');
			if($criteria !== null){
				$this->queryBuilder->criteria($criteria);
			}
		}

		private function joinCampaigns($referenceColumn, $criteria = null){
			$this->queryBuilder->join('Campaign', 'ca', $referenceColumn.' = ca.id');
			if($criteria !== null){
				$this->queryBuilder->criteria($criteria);
			}
		}

		private function joinClients($referenceColumn, $criteria = null){
			$this->queryBuilder->join('Client', 'cl', $referenceColumn.' = cl.id');
			if($criteria !== null){
				$this->queryBuilder->criteria($criteria);
			}
		}

		private function joinCompanies($referenceColumn, $criteria = null){
			$this->queryBuilder->join('Company', 'co', $referenceColumn.' = co.id');
			if($criteria !== null){
				$this->queryBuilder->criteria($criteria);
			}
		}

		private function joinCompanyGroups($referenceColumn, $criteria = null){
			$this->queryBuilder->join('CompanyGroup', 'cg', $referenceColumn.' = cg.id');
			if($criteria !== null){
				$this->queryBuilder->criteria($criteria);
			}
		}

		private function joinUsers($referenceColumn, $criteria = null){
			$this->queryBuilder->join('User', 'u', $referenceColumn.' = u.id');
			$this->queryBuilder->select(
				[
					'u_fullname' => 'CONCAT(u.lastname, \' \', u.firstname)',
				]
			);
			if($criteria !== null){
				$this->queryBuilder->criteria($criteria);
			}
		}

		private function joinYearMonths($referenceColumn, $criteria = null){
			$this->queryBuilder->join('YearMonth', 'ym', $referenceColumn.' = ym.id');
			if($criteria !== null){
				$this->queryBuilder->criteria($criteria);
			}
		}

		private function prepareYearMonthCriteria(TimeWindow $timeWindow){
			$criteria = ['AND'];
			$criteria[] = 'ym.id >= '.$timeWindow->getYearmonthFrom()->getId();
			$criteria[] = 'ym.id <= '.$timeWindow->getYearmonthTo()->getId();

			return $criteria;
		}

	}