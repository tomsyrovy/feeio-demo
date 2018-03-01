<?php
/**
 * Project: feeio2
 * File: ControllerRedirect.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 12.12.15
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection;


class ControllerRedirect {

	/**
	 * @var string
	 */
	private $flashType;

	/**
	 * @var string
	 */
	private $flashMessage;

	/**
	 * @var string
	 */
	private $redirectRoute;

	/**
	 * @var array
	 */
	private $params;

	/**
	 * ControllerRedirect constructor.
	 *
	 * @param       $flashType
	 * @param       $flashMessage
	 * @param       $redirectRoute
	 * @param array $params
	 */
	public function __construct( $flashType, $flashMessage, $redirectRoute, $params = array() ){
		$this->flashType     = $flashType;
		$this->flashMessage  = $flashMessage;
		$this->redirectRoute = $redirectRoute;
		$this->params        = $params;
	}

	/**
	 * @return string
	 */
	public function getFlashType(){
		return $this->flashType;
	}

	/**
	 * @return string
	 */
	public function getFlashMessage(){
		return $this->flashMessage;
	}

	/**
	 * @return string
	 */
	public function getRedirectRoute(){
		return $this->redirectRoute;
	}

	/**
	 * @return array
	 */
	public function getParams(){
		return $this->params;
	}

}