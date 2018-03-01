<?php
/**
 * Project: feeio2   
 * File: Invitator.php
 *
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 13.07.15
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection\Invitator;


use AppBundle\DependencyInjection\Tool;
use AppBundle\Entity\Factory\UserFactory;
use AppBundle\Entity\Invitation;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\User;

class Invitator {

	/**
	 * @var Controller
	 */
	private $controller;

	/**
	 * @var Invitation
	 */
	private $invitation;

	/**
	 * @var EntityManager
	 */
	private $em;

	public function __construct( $controller, $invitation ) {
		$this->controller = $controller;
		$this->invitation = $invitation;
		$this->em = $this->controller->getDoctrine()->getEntityManager();
	}

	public function invite(){

		if($this->isAlreadyInvited()){

			$invitatorResult = new InvitatorResult("danger", "USER_INVITED");

		}else{

			$user = $this->getUserByEmailInvitation();

			if($user){

				$message = \Swift_Message::newInstance()
				                         ->setSubject('Byl/a jste přidán/a do společnosti '.$this->invitation->getCompany()->getName())
				                         ->setFrom('noreply@feeio.com', "Feeio")
				                         ->setTo($user->getEmail())
				                         ->setBody(
					                         $this->controller->renderView(
						                         '@App/email/invitationExistingUser.html.twig', array(
							                         "user" => $user,
							                         "company" => $this->invitation->getCompany(),
						                         )
					                         ),
					                         'text/html'
				                         )
				;
				$this->controller->get('mailer')->send($message);

				$invitatorResult = new InvitatorResult("success", "EMAIL_HAS_BEEN_SENT");

			}else{
				$password = Tool::getRandomPassword();
				$userFactory = new UserFactory($this->em);
				$user = $userFactory->createUser($this->invitation->getEmail(), null, null, $password);
				$this->em->persist($user);
				$this->em->flush();

				$message = \Swift_Message::newInstance()
				                         ->setSubject('Byl/a jste přidán/a do společnosti '.$this->invitation->getCompany()->getName())
				                         ->setFrom('noreply@feeio.com', "Feeio")
				                         ->setTo($user->getEmail())
				                         ->setBody(
					                         $this->controller->renderView(
						                         '@App/email/invitationNonExistingUser.html.twig', array(
							                         "user" => $user,
							                         "company" => $this->invitation->getCompany(),
							                         "password" => $password
						                         )
					                         ),
					                         'text/html'
				                         )
				;
				$this->controller->get('mailer')->send($message);

				$invitatorResult = new InvitatorResult("success", "USER_CREATED_EMAIL_HAS_BEEN_SENT");
			}

		}



		return $invitatorResult;

	}

	/**
	 * Vrátí uživatele podle definovaného e-mailu v objektu Invitator
	 *
	 * @return User|null
	 */
	public function getUserByEmailInvitation(){

		$criteria = array(
			"email" => $this->invitation->getEmail(),
		);

		$user = $this->em->getRepository("UserBundle:User")->findOneBy($criteria);

		return $user;

	}

	private function isAlreadyInvited(){

		$criteria = array(
			"email" => $this->invitation->getEmail(),
			"company" => $this->invitation->getCompany(),
			"enabled" => true
		);

		$user = $this->em->getRepository("AppBundle:Invitation")->findBy($criteria);

		return count($user) != 0;

	}

}