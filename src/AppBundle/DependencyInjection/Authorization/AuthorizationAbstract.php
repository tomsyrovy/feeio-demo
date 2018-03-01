<?php
/**
 * Project: feeio2
 * File: AuthorizationAbstract.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 15.08.15
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection\Authorization;


use Doctrine\ORM\EntityManager;

abstract class AuthorizationAbstract {

	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * AuthorizationChecker constructor.
	 *
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

}