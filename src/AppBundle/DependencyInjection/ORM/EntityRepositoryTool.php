<?php
/**
 * Project: feeio2
 * File: EntityRepositoryTool.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 11.02.16
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class EntityRepositoryTool {

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var string
	 */
	private $entityName;

	/**
	 * @var EntityRepository
	 */
	private $entityRepository;

	/**
	 * EntityRepositoryTool constructor.
	 *
	 * @param \Doctrine\ORM\EntityManager                                      $em
	 * @param                                                                  $entityName
	 * @param \AppBundle\DependencyInjection\ORM\EntityRepositoryToolInterface $entityRepository
	 */
	public function __construct( EntityManager $em, $entityName, EntityRepositoryToolInterface $entityRepository ){
		$this->em               = $em;
		$this->entityName       = $entityName;
		$this->entityRepository = $entityRepository;
	}

	/**
	 * Vrátí seskupené hodnoty z množiny výsledků, které odpovídají zadaným kritériím. Hodnoty jsou seskupeny dle zadané tabulky a sloupce.
	 *
	 * @param array  $criteria Omezující kritéria pro seskupení
	 * @param array  $orderBy Seřazení výsledků po seskupení
	 * @param string $groupByReference Tabulka, podle které se seskupuje
	 * @param string $groupByColumnName Sloupec, podle kterého se seskupuje
	 *
	 * @return array
	 */
	public function findByGroupedBy(array $criteria, array $orderBy, $groupByReference, $groupByColumnName){

		$alias = 't0';
		$groupByAlias = 't1';

		$qb = new QueryBuilder($this->em);

//		$qb->select($alias.'.'.$groupByReference.', '.implode(', ', $this->getSelectStatement($alias)).', COUNT('.$groupByAlias.'.'.$groupByColumnName.') AS count');
		$qb->select($alias.' AS entity, '.implode(', ', $this->getSelectStatement($alias)));
		$qb->from($this->entityName, $alias);
		$qb->join($alias.'.'.$groupByReference, $groupByAlias);


		$i = 0;
		foreach($criteria as $column => $value){

			if($i !== 0){

				$qb->andWhere($alias.'.'.$column.' = :c_'.$i);
				$qb->setParameter('c_'.$i, $value);

			}else{

				$qb->where($alias.'.'.$column.' = :c_'.$i);
				$qb->setParameter('c_'.$i, $value);

			}


			$i++;

		}

		$qb->groupBy($groupByAlias.'.'.$groupByColumnName);


		$i = 0;
		foreach($orderBy as $column => $value){

			$qb->orderBy($alias.'.'.$column.' = :o_'.$i);
			$qb->setParameter('o_'.$i, $value);

			$i++;

		}

		return $qb->getQuery()->getResult();

	}

	private function getSelectStatement($alias){

		$array = array_map(function($entry) use ($alias) {
			return 'SUM('.$alias.'.'.$entry.') AS '.$entry;
		}, $this->entityRepository->getSumarizations());

		return $array;

	}


}