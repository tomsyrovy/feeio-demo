<?php
/**
 * Project: feeio2   
 * File: UserFactory.php
 *
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 24.07.15
 * Version: 1.0
 */

namespace AppBundle\Entity\Factory;


use AppBundle\DependencyInjection\ImageCreator\ImageCreator;
use AppBundle\Entity\CurrentSettings;
use Doctrine\ORM\EntityManager;
use UserBundle\Entity\User;

class UserFactory {

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @param $entityManager
	 */
	public function __construct( $entityManager ) {
		$this->entityManager = $entityManager;
	}


	/**
	 * Create new user based on input data and return it
	 *
	 * @param $email
	 * @param $firstname
	 * @param $lastname
	 * @param $password
	 *
	 * @return \UserBundle\Entity\User
	 */
	public function createUser($email, $firstname, $lastname, $password)
	{
		$user = new User();
		$user->setEmail($email);
		$user->setEmailCanonical($email);
		$user->setFirstname($firstname);
		$user->setLastname($lastname);
		$user->setEnabled(true);
		$user->setPlainPassword($password);
		$user->setCreatedAt(new \DateTime());

		$this->createProfileImage($user);

		$this->createCurrentSettings($user);

		return $user;
	}

	/**
	 * @param \UserBundle\Entity\User $user
	 */
	private function createProfileImage(User $user){
		$text = mb_substr($user->getFirstname(), 0, 1, 'UTF-8').mb_substr($user->getLastname(), 0, 1, 'UTF-8');
		$imageCreator = new ImageCreator();
		$image = $imageCreator->getImage($text);

		$user->setImage($image);
		$this->entityManager->persist($image);
	}

	/**
	 * @param \UserBundle\Entity\User $user
	 */
	private function createCurrentSettings(User $user){
		$currentSettings = new CurrentSettings();
		$currentSettings->setUser($user);
		$currentSettings->setCompany(null);

		$this->entityManager->persist($currentSettings);
	}

}