<?php
/**
 * Project: feeio2
 * File: WidgetManager.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 05.03.16
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection\Widget;


use AppBundle\Entity\Widget;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\VarDumper\VarDumper;
use UserBundle\Entity\User;

class WidgetManager {

	/**
	 * @var ObjectManager
	 */
	private $em;

	/**
	 * WidgetManager constructor.
	 *
	 * @param \Doctrine\Common\Persistence\ObjectManager $em
	 */
	public function __construct( \Doctrine\Common\Persistence\ObjectManager $em){
		$this->em     = $em;
	}

	public function getWidgetDOs(User $user, array $params){

		$userWidgets = $user->getUserWidgets();

		$widgetDOs = [];

		foreach($userWidgets as $userWidget){

			$widget = $userWidget->getWidget();

			if($this->checkParams($widget, $params)){

				$widgetDOs[] = $this->getWidgetDO($widget, $params);

            }

		}

		return $widgetDOs;

	}

	private function checkParams(Widget $widget, array $params){

		$dql = $widget->getDQL();

		preg_match_all("/\s:([a-z]*)\s/", $dql, $output_array);

		$dqlParams = $output_array[1];

		foreach($dqlParams as $dqlParam){
			if(!key_exists($dqlParam, $params)){
				return false;
			}
		}

		$dql2 = $widget->getDQL2();

		preg_match_all("/\s:([a-z]*)\s/", $dql2, $output_array);

		$dqlParams = $output_array[1];

		foreach($dqlParams as $dqlParam){
			if(!key_exists($dqlParam, $params)){
				return false;
			}
		}

		return true;

	}

	private function getWidgetDO(Widget $widget, array $params){

		$result = $this->getWidgetResult($widget, $params);

		$widgetDO = new WidgetDO($widget, $result);

		return $widgetDO;

	}


	private function getWidgetResult(Widget $widget, array $params){

		$result = [];

		$dql = $widget->getDQL();

		$query = $this->em->createQuery($dql);
		$query->setParameters($params);

		if(count($query->getResult()) == 0){

			$result[] = [
				[
					'result' => 0
				],
			];

		}else{

			$result[] = $query->getResult();

		}

		$dql2 = $widget->getDQL2();

		if($dql2){

			$query = $this->em->createQuery($dql2);
			$query->setParameters($params);

			$result[] = $query->getResult();

		}

		return $result;

	}

}