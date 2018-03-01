<?php
/**
 * Project: feeio2
 * File: EntityRepositoryExtendedInterface.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 11.02.16
 * Version: 1.0
 */

namespace AppBundle\DependencyInjection\ORM;


interface EntityRepositoryToolInterface {

	/**
	 * @return array
	 */
	public function getSumarizations();

}