<?php
/**
 * Project: feeio2
 * File: TableData.php
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 12.12.15
 * Version: 1.0
 */

namespace TableBundle\DependencyInjection;


use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;

class TableData {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $em
	 * @param \UserBundle\Entity\User                    $user
	 * @param                                            $tableCode
	 *
	 * @return array
	 */
	public static function getData(ObjectManager $em, User $user, $tableCode){

		$criteria = array(
			'code' => $tableCode,
		);
		$table = $em->getRepository('TableBundle:TableEntity')->findOneBy($criteria);

		$criteria = array(
			'user' => $user,
			'tableEntity' => $table,
		);
		$userColumns = $em->getRepository('TableBundle:UserColumn')->findBy($criteria);

		$criteria = array(
			'user' => $user,
		);
		$userDefaultColumns = $em->getRepository('TableBundle:UserDefaultColumn')->findBy($criteria);

		$udcs = array();
		foreach($userDefaultColumns as $udc){

			$udcs[$udc->getTableDefaultColumn()->getId()] = $udc;

		}

		return array(
			'table' => $table,
			'userColumns' => $userColumns,
			'userDefaultColumns' => $udcs,
		);

	}

}