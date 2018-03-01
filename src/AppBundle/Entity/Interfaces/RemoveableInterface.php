<?php
/**
 * Project: feeio2
 * File: RemoveableInterface.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 13.01.16
 * Version: 1.0
 */

namespace AppBundle\Entity\Interfaces;

interface RemoveableInterface {

	public function canRemove();

}