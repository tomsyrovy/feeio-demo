<?php
/**
 * Project: feeio2   
 * File: InvitatorResult.php
 *
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 22.07.15
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection\Invitator;


class InvitatorResult {

	private $type;

	private $text;

	function __construct( $type, $text ) {
		$this->type = $type;
		$this->text = $text;
	}


	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType( $type ) {
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * @param mixed $text
	 */
	public function setText( $text ) {
		$this->text = $text;
	}

}