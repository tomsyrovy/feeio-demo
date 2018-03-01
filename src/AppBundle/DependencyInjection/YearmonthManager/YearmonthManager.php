<?php
/**
 * Project: feeio2
 * File: YearmonthManager.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 05.12.15
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection\YearmonthManager;


use AppBundle\Entity\YearMonth;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;

class YearmonthManager {

	/**
	 * @var ObjectManager
	 */
	private $em;

	/**
	 * YearmonthManager constructor.
	 *
	 * @param \Doctrine\Common\Persistence\ObjectManager $em
	 */
	public function __construct( \Doctrine\Common\Persistence\ObjectManager $em ){
		$this->em = $em;
	}

	/**
	 * @param     $year
	 * @param int $nextYears
	 *
	 * @return array
	 */
	public function getCollectionOfThisAndNextYears($year, $nextYears = 1){

		$qb = new QueryBuilder($this->em);
		$qb
			->select('ym')
			->from('AppBundle:YearMonth', 'ym')
			->where('ym.year >= :year1')
			->andWhere('ym.year <= :year2')
			->setParameter('year1', $year)
			->setParameter('year2', $year+$nextYears);

		return $qb->getQuery()->getResult();
	}

	/**
	 * @param \AppBundle\Entity\YearMonth $yearMonth
	 * @param int                         $limit
	 *
	 * @return array
	 */
	public function getCollectionOfYearMonthAndPreviousByLimit(YearMonth $yearMonth, $limit = 2){

		$qb = new QueryBuilder($this->em);
		$qb
			->select('ym')
			->from('AppBundle:YearMonth', 'ym')
			->where('ym.id <= :ym_id')
			->orderBy('ym.id', 'DESC')
			->setMaxResults($limit)
			->setParameter('ym_id', $yearMonth->getId());

		return $qb->getQuery()->getResult();
	}

}