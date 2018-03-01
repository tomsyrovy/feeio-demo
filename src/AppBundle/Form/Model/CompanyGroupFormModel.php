<?php
/**
 * Project: feeio2
 * File: CompanyGroupFormModel.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 20.01.16
 * Version: 1.0
 */

namespace AppBundle\Form\Model;


use AppBundle\Entity\CompanyGroup;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\VarDumper\VarDumper;

class CompanyGroupFormModel {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $members;

	/**
	 * @param \AppBundle\Entity\CompanyGroup $companyGroup
	 */
	public function setDataFromCompanyGroup(CompanyGroup $companyGroup, ObjectManager $em){

		$this->setName($companyGroup->getName());

		$cgucrtts = $em->getRepository("AppBundle:CompanyGroupUserCompanyRelationTemporalityType")->findAll();

		$string = "";

		foreach($cgucrtts as $cgucrtt){

			$cgucrs = $companyGroup->getCompanyGroupUserCompanyRelationsOfTemporalityStatus($cgucrtt->getCode());

			$string = $string.$cgucrtt->getCode().':';

			$i = 1;
			foreach($cgucrs as $cgucr){

				$string = $string.'userCompany[]='.$cgucr->getUserCompany()->getId();

				//if is not last
				if(count($cgucrs) != $i){

					$string = $string.'&';

				}

				$i++;
			}

			$string = $string.';';
		}

		$this->members = $string;

	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name ){
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getMembers(){
		return $this->members;
	}

	/**
	 * @param string $members
	 */
	public function setMembers( $members ){
		$this->members = $members;
	}



}