<?php
	/**
	 * Project: feeio2
	 * File: CommissionInfo.php
	 * Author: Tomas SYROVY <tomas@syrovy.pro>
	 * Date: 08.08.16
	 * Version: 1.0
	 */

	namespace AppBundle\Report\Query\Builder;

	use AppBundle\Report\Query\QueryManager;

	class QueryBuilder{

		/**
		 * @var QueryManager
		 */
		private $queryManager;

		/**
		 * @var array
		 */
		private $selects;

		/**
		 * @var array
		 */
		private $table;

		/**
		 * @var array
		 */
		private $joins;

		/**
		 * @var array
		 */
		private $criterias;

		/**
		 * @var mixed
		 */
		private $aggregateByColumns;

		/**
		 * QueryBuilder constructor.
		 *
		 * @param \AppBundle\Report\Query\QueryManager $queryManager
		 */
		public function __construct(QueryManager $queryManager){
			$this->queryManager = $queryManager;
			$this->selects = [];
			$this->table = [];
			$this->joins = [];
			$this->criterias = [];
			$this->aggregateByColumns = false;
		}

		private function addSelect($table, $tableAlias){

			$sql = 'SHOW COLUMNS IN '.$table;
			$result = $this->queryManager->execute($sql);

			foreach($result as $row){
//				$this->selects[] = $tableAlias.'.'.$row['Field'].' AS '.$table.'_'.$row['Field'];
				$this->selects[] = $tableAlias.'.'.$row['Field'].' AS '.$tableAlias.'_'.$row['Field'];
			}

		}

		public function select(array $columns){
			foreach($columns as $key => $column){
				$this->selects[] = $column.' AS '.$key;
			}
		}

		public function table($table, $alias){
			$this->table = [
				$table,
				$alias,
			];
			$this->addSelect($table, $alias);
			return $this;
		}

		public function join($table, $alias, $on){
			$this->joins[] = [
				$table,
				$on,
				$alias,
			];
			$this->addSelect($table, $alias);
			return $this;
		}

		public function criteria($criteria){
			$this->criterias[] = $criteria;
			return $this;
		}

		public function aggregate(array $columns, $aggregatedByColumns){
			$this->select($columns);
			$this->aggregateByColumns = $aggregatedByColumns;
		}

		public function get(){
			list($table, $alias) = $this->table;
			$query = 'SELECT '.implode(', ', $this->selects).' FROM '.$table.' '.$alias;
			if(count($this->joins) !== 0){
				foreach($this->joins as $join){
					list($table, $on, $alias) = $join;
					$query .= ' LEFT JOIN '.$table.' '.$alias.' ON ('.$on.')';
				}
			}
			if(count($this->criterias) !== 0){
				$query .= ' WHERE';
				$query .= $this->prepareCriteria($this->criterias);
			}
			if($this->aggregateByColumns){
				$query .= ' GROUP BY '.$this->aggregateByColumns;
			}
			return $query;
		}

		private function prepareCriteria($criteria){

			$operator = 'AND';
			if(array_key_exists(0, $criteria) AND ($criteria[0] === 'AND' OR $criteria[0] === 'OR')){
				$operator = array_shift($criteria);
			}
			$query = ' (';
			$i = 0;
			foreach($criteria as $criterium){
				if(is_array($criterium)){
					$query .= $this->prepareCriteria($criterium);
				}else{
					$query .= ' '.$criterium;
				}
				$i++;
				if($i !== count($criteria)){
					$query .= ' '.$operator;
				}
			}
			$query .= ' )';

			return $query;
		}

	}