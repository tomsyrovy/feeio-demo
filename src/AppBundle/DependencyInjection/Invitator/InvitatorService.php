<?php
/**
 * Project: feeio2   
 * File: InvitatorService.php
 *
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 22.07.15
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection\Invitator;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\User;

class InvitatorService {

	/**
	 * @var Controller
	 */
	private $controller;

	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	function __construct( Controller $controller ) {
		$this->controller = $controller;
		$this->em = $this->controller->getDoctrine()->getManager();
	}


	public function getInvitationsByUser(User $user){

		$criteria = array(
			"email" => $user->getEmail(),
			"enabled" => true,
		);
		$invitations = $this->em->getRepository("AppBundle:Invitation")->findBy($criteria);

		return $invitations;

	}

}