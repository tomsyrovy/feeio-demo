<?php
/**
 * Project: feeio2
 * File: TimeWindowManager.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 08.12.15
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection;


use AppBundle\Entity\TimeWindow;
use AppBundle\Entity\YearMonth;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\VarDumper\VarDumper;
use UserBundle\Entity\User;

class TimeWindowManager {

	/**
	 * @var ObjectManager
	 */
	private $em;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * TimeWindowManager constructor.
	 *
	 * @param \Doctrine\Common\Persistence\ObjectManager $em
	 * @param \UserBundle\Entity\User                    $user
	 */
	public function __construct( \Doctrine\Common\Persistence\ObjectManager $em, \UserBundle\Entity\User $user ){
		$this->em   = $em;
		$this->user = $user;
	}


	/**
	 * @return \AppBundle\Entity\TimeWindow|null
	 */
	public function getTimeWindow(){

		$timeWindow = $this->checkTimeWindow();

		if($timeWindow === null){

			return $this->setDefaultTimeWindow();

		}

		return $timeWindow;

	}

	/**
	 * @return \AppBundle\Entity\TimeWindow|null
	 */
	public function setDefaultTimeWindow(){

		$a = $this->getDefaultYearMonths();

		return $this->setTimeWindow($a['ym1'], $a['ym2']);

	}

	/**
	 * @param \AppBundle\Entity\YearMonth $ym1
	 * @param \AppBundle\Entity\YearMonth $ym2
	 *
	 * @return \AppBundle\Entity\TimeWindow|null
	 */
	private function setTimeWindow(YearMonth $ym1, YearMonth $ym2){

		if($ym1->getId() < $ym2->getId()){

			$timeWindow = $this->checkTimeWindow();

			if($timeWindow === null){

				$timeWindow = new TimeWindow();

			}

			$timeWindow->setUser($this->user);
			$timeWindow->setYearmonthFrom($ym1);
			$timeWindow->setYearmonthTo($ym2);

			return $timeWindow;

		}else{

			return $this->setDefaultTimeWindow();

		}

	}

	public function isDefaultTimeWindow(){

		$a = $this->getDefaultYearMonths();

		return $this->getTimeWindow()->getYearmonthFrom() === $a['ym1'] and $this->getTimeWindow()->getYearmonthTo() === $a['ym2'];

	}


	public function getDefaultYearMonths($a = 6, $s = 23){

		$date = new \DateTime();
		$date->add(new \DateInterval('P'.$a.'M'));

		$criteria = array(
			'month' => $date->format('m'),
			'year' => $date->format('Y'),
		);
		$ym2 = $this->em->getRepository('AppBundle:YearMonth')->findOneBy($criteria);

		$date = new \DateTime();
		$date->sub(new \DateInterval('P'.$s.'M'));
		$criteria = array(
			'month' => $date->format('m'),
			'year' => $date->format('Y'),
		);
		$ym1 = $this->em->getRepository('AppBundle:YearMonth')->findOneBy($criteria);

		return array(
			'ym1' => $ym1,
			'ym2' => $ym2
		);

	}

	private function checkTimeWindow(){

		$criteria = array(
			'user' => $this->user,
		);

		$timeWindow = $this->em->getRepository('AppBundle:TimeWindow')->findOneBy($criteria);

		if($timeWindow){

			return $timeWindow;

		}else{

			return null;

		}

	}

}