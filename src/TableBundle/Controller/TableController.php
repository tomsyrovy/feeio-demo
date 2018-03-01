<?php

namespace TableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use TableBundle\Entity\UserColumn;
use TableBundle\Entity\UserDefaultColumn;
use TableBundle\Form\Type\UserColumnFormType;

class TableController extends Controller
{

	/**
	 * @Route("/settings/tables/")
	 * @Template()
	 *
	 * @return array
	 */
	public function listAction(){

		$em = $this->getDoctrine()->getManager();

		$tables = $em->getRepository("TableBundle:TableEntity")->findAll();

		$data = array(
			'tables' => $tables,
		);

		return $data;

	}

	/**
	 * @Route("/settings/table_code/{table_code}/edit/")
	 *
	 * @return array
	 */
	public function editcodeAction($table_code){

		$em = $this->getDoctrine()->getManager();

		$criteria = array(
			'code' => $table_code,
		);
		$table = $em->getRepository("TableBundle:TableEntity")->findOneBy($criteria);

		if(!$table){

			return $this->redirectToRoute('app_profile_edit');

		}

		$params = array(
			'table_id' => $table->getId(),
		);
		return $this->redirectToRoute('table_table_edit', $params);

	}

	/**
	 * @Route("/settings/table/{table_id}/edit/")
	 * @Template()
	 *
	 * @return array
	 */
	public function editAction($table_id, Request $request){

		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

		$table = $em->getRepository("TableBundle:TableEntity")->find($table_id);

		if(!$table){

			return $this->redirectToRoute('app_profile_edit');

		}

		$userColumn = new UserColumn();

		$form = $this->createForm(new UserColumnFormType(), $userColumn);

		$form->handleRequest($request);

		if($form->isValid()){

			$userColumn->setUser($user);
			$userColumn->setTableEntity($table);

			$em->persist($userColumn);

			$em->flush();

			$this->addFlash('success', 'Tabulka byla aktualizována.');

			$params = array(
				'table_id' => $table->getId(),
			);
			return $this->redirectToRoute('table_table_edit', $params);

		}

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

		$data = array(
			'table' => $table,
			'form' => $form->createView(),
			'userColumns' => $userColumns,
			'userDefaultColumns' => $udcs,
		);

		return $data;

	}

	/**
	 * @Route("/settings/table/{table_id}/column/{column_id}/delete/")
	 *
	 * @return array
	 */
	public function deleteAction($table_id, $column_id){

		$em = $this->getDoctrine()->getManager();

		$table = $em->getRepository("TableBundle:TableEntity")->find($table_id);

		if(!$table){

			return $this->redirectToRoute('app_profile_edit');

		}

		$criteria = array(
			'id' => $column_id,
		);
		$userColumn = $em->getRepository('TableBundle:UserColumn')->findOneBy($criteria);

		if(!$userColumn){

			return $this->redirectToRoute('app_profile_edit');

		}

		$em->remove($userColumn);
		$em->flush();

		$this->addFlash('success', 'Sloupeček byl odstraněn.');

		$params = array(
			'table_id' => $table->getId(),
		);
		return $this->redirectToRoute('table_table_edit', $params);

	}

	/**
	 * @Route("/settings/table/{table_id}/column/{column_id}/toggle/")
	 *
	 * @return array
	 */
	public function toggleAction($table_id, $column_id){

		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

		$table = $em->getRepository("TableBundle:TableEntity")->find($table_id);

		if(!$table){

			return $this->redirectToRoute('app_profile_edit');

		}

		$criteria = array(
			'id' => $column_id,
		);
		$defaultColumn = $em->getRepository('TableBundle:TableDefaultColumn')->findOneBy($criteria);

		if(!$defaultColumn){

			return $this->redirectToRoute('app_profile_edit');

		}

		$criteria = array(
			'user' => $user,
			'tableDefaultColumn' => $defaultColumn
		);

		$userDefaultColumn = $em->getRepository('TableBundle:UserDefaultColumn')->findOneBy($criteria);

		if(!$userDefaultColumn){

			$userDefaultColumn = new UserDefaultColumn();
			$userDefaultColumn->setUser($user);
			$userDefaultColumn->setTableDefaultColumn($defaultColumn);
			$userDefaultColumn->setHidden(true);

			$message = 'Sloupeček se nebude zobrazovat.';

		}else{

			if($userDefaultColumn->getHidden()){

				$userDefaultColumn->setHidden(false);

				$message = 'Sloupeček se bude zobrazovat.';

			}else{

				$userDefaultColumn->setHidden(true);

				$message = 'Sloupeček se nebude zobrazovat.';

			}

		}

		$em->persist($userDefaultColumn);
		$em->flush();

		$this->addFlash('success', $message);

		$params = array(
			'table_id' => $table->getId(),
		);
		return $this->redirectToRoute('table_table_edit', $params);

	}

}
