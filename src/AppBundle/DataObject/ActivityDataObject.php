<?php
/**
 * Project: feeio2
 * File: ActivityDataObject.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 01.01.16
 * Version: 1.0
 */

namespace AppBundle\DataObject;


use AppBundle\Entity\Activity;

class ActivityDataObject {

	/**
	 * @var Activity
	 */
	private $activity;

	/**
	 * ActivityDataObject constructor.
	 *
	 * @param \AppBundle\Entity\Activity $activity
	 */
	public function __construct( \AppBundle\Entity\Activity $activity ){
		$this->activity = $activity;
	}

	/**
	 * @return Activity
	 */
	public function getActivity(){
		return $this->activity;
	}

	/**
	 * @return boolean
	 */
	public function getCanDelete(){
		return $this->activity->getTimesheets()->count() === 0;
	}
}