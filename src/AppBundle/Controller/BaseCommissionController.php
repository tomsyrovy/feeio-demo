<?php

/**
 * Project: feeio2
 * File: BaseCommissionController.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 11.02.16
 * Version: 1.0
 */

namespace AppBundle\Controller;

use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
use AppBundle\DependencyInjection\ControllerRedirect;
use AppBundle\Entity\Commission;
use AppBundle\Entity\CommissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\User;

abstract class BaseCommissionController extends Controller{

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @var EntityManagerInterface
	 */
	protected $em;

	/**
	 * @var CommissionRepository
	 */
	protected $commissionRepository;

	/**
	 * @var Commission
	 */
	protected $commission;

	protected function init(){

		$this->user = $this->getUser();
		$this->em = $this->getDoctrine()->getManager();
		$this->commissionRepository = $this->em->getRepository('AppBundle:Commission');

	}

	protected function initCommission($commission_id){

		$this->commission = $this->commissionRepository->find($commission_id);

	}

	/**
	 * @return \AppBundle\DependencyInjection\ControllerRedirect|bool
	 */
	protected function checkCommission(){

		$cr = false;

		if(!$this->commission){

			$cr = new ControllerRedirect('danger', 'Tato zakázka neexistuje.', 'app_commission_list'); //Pozor - tato hláška musí být univerzální

		}

		return $cr;

	}

	/**
	 * @return \AppBundle\DependencyInjection\ControllerRedirect|bool
	 */
	protected function checkCommissionMgm(array $m){

		$cr = false;

		$cm = new CommissionManager($this->em);

		$check = $cm->checkCommissionUserCompanyManagedByRoleTypeCodes($this->user, $this->commission, 'enabled', $m);

		if(!$check){

			$params = array(
				'commission_id' => $this->commission->getId(),
			);
			$cr = new ControllerRedirect('danger', 'Nemáte oprávnění spravovat tuto zakázku.', 'app_commission_list', $params);//Pozor - tato hláška musí být univerzální

		}

		return $cr;

	}

	protected function redirectNow(ControllerRedirect $cr){

		$this->addFlash($cr->getFlashType(), $cr->getFlashMessage());
		if($cr->getParams()){
			return $this->redirectToRoute($cr->getRedirectRoute(), $cr->getParams());
		}else{
			return $this->redirectToRoute($cr->getRedirectRoute());
		}

	}
	
}