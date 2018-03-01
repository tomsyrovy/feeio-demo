<?php
	/**
	 * Project: feeio2
	 * File: MenuBuilder.php
	 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
	 * Date: 16.01.16
	 * Version: 1.0
	 */

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class MenuBuilder implements ContainerAwareInterface
{

	use ContainerAwareTrait;

	public function mainMenu(FactoryInterface $factory, array $options)
	{
		$menu = $factory->createItem('root');

		$em = $this->container->get('doctrine')->getManager();
		$router = $this->container->get('router');

		$itemCommissionList = new MenuItem('commission.list', $factory);
		$params = [];
		$itemCommissionList->setUri($router->generate('app_commission_list', $params));

// Commissions
		$criteria = [];
		$commissions = $em->getRepository('AppBundle:Commission')->findBy($criteria);
		foreach($commissions as $commission){
			// Commission item
			$itemCommission = new MenuItem($commission->getName(), $factory);
			$params = [
				'commission_id' => $commission->getId()
			];
			$uri = $router->generate('app_subcommission_list', $params);
			$itemCommission->setUri($uri);

			//Subcommission list
			$itemSubcommissionList = new MenuItem('subcommission.list', $factory);
			$params = [
				'commission_id' => $commission->getId()
			];
			$uri = $router->generate('app_subcommission_list', $params);
			$itemSubcommissionList->setUri($uri);

			foreach($commission->getSubcommissions() as $subcommission){
				//Subcommission item
				$itemSubcommission = new MenuItem($subcommission->getCode(), $factory);
				$params = [
					'commission_id' => $commission->getId(),
					'subcommission_id' => $subcommission->getId(),
				];
				$uri = $router->generate('app_subcommission_update', $params);
				$itemSubcommission->setUri($uri);

				$itemSubcommissionDuplicate = new MenuItem('subcommission.duplicate', $factory);
				$params = [
					'commission_id' => $commission->getId(),
					'subcommission_id' => $subcommission->getId(),
				];
				$uri = $router->generate('app_subcommission_update', $params);
				$itemSubcommissionDuplicate->setUri($uri);
				$itemSubcommission->addChild($itemSubcommissionDuplicate);

				$itemSubcommissionUpdate = new MenuItem('subcommission.update', $factory);
				$params = [
					'commission_id' => $commission->getId(),
					'subcommission_id' => $subcommission->getId(),
				];
				$uri = $router->generate('app_subcommission_update', $params);
				$itemSubcommissionUpdate->setUri($uri);
				$itemSubcommission->addChild($itemSubcommissionUpdate);

				$itemSubcommissionTeamupdate = new MenuItem('subcommission.teamupdate', $factory);
				$params = [
					'commission_id' => $commission->getId(),
					'subcommission_id' => $subcommission->getId(),
				];
				$uri = $router->generate('app_subcommission_teamupdate', $params);
				$itemSubcommissionTeamupdate->setUri($uri);
				$itemSubcommission->addChild($itemSubcommissionTeamupdate);

				$itemSubcommissionRemove = new MenuItem('subcommission.remove', $factory);
				$params = [
					'commission_id' => $commission->getId(),
					'subcommission_id' => $subcommission->getId(),
				];
				$uri = $router->generate('app_subcommission_delete', $params);
				$itemSubcommissionRemove->setUri($uri);
				$itemSubcommission->addChild($itemSubcommissionRemove);

				$itemSubcommissionList->addChild($itemSubcommission);
			}

			$itemCommission->addChild($itemSubcommissionList);

			//Budget list

			//Timesheet list

			$itemCommissionList->addChild( $itemCommission );
		}

		$menu->addChild($itemCommissionList);

//		$menu->addChild( 'timesheet.list', [ 'route' => 'app_timesheet_list' ] );
//		$menu->addChild( 'commission.list', [ 'route' => 'app_commission_list' ] );
//		$menu->addChild( 'contact.list', [ 'route' => 'app_contact_list' ] );
//		$menu->addChild( 'company.list', [ 'route' => 'app_company_list' ] );
//		$menu->addChild( 'profile.edit', [ 'route' => 'app_profile_edit' ] );

//		$menu['commission.list']->addChild( 'commission.create', [ 'route' => 'app_commission_create' ] );
//		$menu['contact.list']->addChild( 'contact.create', [ 'route' => 'app_contact_create' ] );

		return $menu;
	}
}