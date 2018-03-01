<?php

namespace AppBundle\Controller;

use AppBundle\DataObject\CRUDLDataObject;
use AppBundle\DataObject\TimesheetListDataObject;
use AppBundle\DependencyInjection\Authorization\AuthorizationChecker;
use AppBundle\DependencyInjection\Authorization\AuthorizationCompany;
use AppBundle\DependencyInjection\Authorization\AuthorizationIndividual;
use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
use AppBundle\DependencyInjection\ControllerRedirect;
use AppBundle\DependencyInjection\ImageCreator\ImageCreator;
use AppBundle\Entity\AllocationContainer;
use AppBundle\Entity\AllocationContainerList;
use AppBundle\Entity\AllocationContainerListItem;
use AppBundle\Entity\AllocationUnit;
use AppBundle\Entity\Campaign;
use AppBundle\Entity\CommissionUserCompanyRelation;
use AppBundle\Entity\Company;
use AppBundle\Entity\UserCompany;
use AppBundle\Entity\YearMonth;
use AppBundle\Form\Type\AllocationContainerFormType;
use AppBundle\Library\Slug;
use AppBundle\Report\Query\QueryManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Commission;
use AppBundle\Form\Type\CommissionFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\VarDumper\VarDumper;
use TableBundle\DependencyInjection\TableData;
use UserBundle\Entity\User;

/**
 * Commission controller.
 */
class CommissionController extends BaseController
{

    /**
     * @Route("/campaign/{campaign_id}/commission/create/")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return Response
     */
    public function createAction($campaign_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $campaign = $em->getRepository('AppBundle:Campaign')->find($campaign_id);

        if(!$campaign){
            $this->addFlash('danger', 'Tato kampaň neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $user = $this->getUser();
        $companies = $user->getCompaniesEnabled();
        $company = $campaign->getClient()->getCompany();

        //Kontakt je ve společnosti, do které uživatel nemá přístup
        if(!$companies->contains($company)){
            $this->addFlash('danger', 'K této kampani nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

        $authorizationChecker = new AuthorizationChecker($em);
        $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('commission-create', $user, $company);

        if(!$check){
            $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění vytvářet zakázky.');

            return $this->redirectToRoute('app_client_list');
        }

        $commission = new Commission();

        $params = [
            'campaign_id' => $campaign_id,
        ];
        $form = $this->getCommissionForm($commission, $campaign, array(
            'method' => 'post',
            'action' => $this->generateUrl('app_commission_create', $params),
        ));

        $form->handleRequest($request);

        if($form->isValid()){

            $commission->setCreated(new \DateTime());
            $commission->setCampaign($campaign);
            $commission->setName($commission->generateName());

            $ac = new AllocationContainer();
            $ac->setVersion(1);
            $ac->setEnabled(true);
            $ac->setClientApproved(false);

            if($commission->getRepeatable()){
                $commission->setEndDate($commission->getStartDate());
            }

            $ac->setCommission($commission);
            $commission->addAllocationContainer($ac);

            $em->persist($commission);
            $em->persist($ac);

            $em->flush();

            $this->addFlash('success', 'Zakázka '.$commission->getName().' byla úspěšně vytvořena.');

            $params = [
                'campaign_id' => $campaign_id,
            ];
            return $this->redirectToRoute('app_campaign_detail', $params);

        }

        $data = array(
            'campaign' => $campaign,
            'form' => $form->createView(),
        );

        return $data;

    }

//    /**
//     * @route("/commission/create/_dc")
//     * @route("/commission/{commission_id}/update/_dc")
//     *
//     * @param \Symfony\Component\HttpFoundation\Request $request
//     *
//     */
//    public function dynamicchangeAction(Request $request, $commission_id = 0){
//
//        $em = $this->getDoctrine()->getManager();
//
//        $company_id = $request->request->get('appbundle_commission')['company'];
//
//        $company = $em->getRepository('AppBundle:Company')->find($company_id);
//
//        if(!$company){
//
//            throw new \Exception('Společnost neexistuje.');
//
//        }
//
//        $ucrsR = $company->getUserCompanyRelationsOfTemporalityStatus('enabled');
//
//        $ucrs = array();
//        foreach($ucrsR as $ucr){
//            $ucrData = array(
//                'id' => $ucr->getId(),
//                'fullname' => $ucr->getUser()->getFullname(),
//            );
//            $ucrs[] = $ucrData;
//        }
//
//        $admins = array();
//
//        //ID of userCompanyRelation of logged user in company
//        $criteria = array(
//            'user' => $this->getUser(),
//            'company' => $company,
//        );
//        $userCompanyRelation = $em->getRepository('AppBundle:UserCompany')->findOneBy($criteria);
//        $admins[] = $userCompanyRelation->getId();
//
//        $ac = new AuthorizationCompany($em);
//
//        //UserCompanyRelations of users which have 'commission-admin-always' authorization in company
//        $usrs = $ac->getUsersInCompanyWhereUsersHaveAuthorizationCode('commission-admin-always', $company);
//        foreach($usrs as $usr){
//            $id = $usr->getId();
//            if(!in_array($id, $admins)){
//                $admins[] = $id;
//            }
//        }
//
//        $observers = array();
//        //UserCompanyRelations of users which have 'commission-observer-always' authorization in company
//        $usrs = $ac->getUsersInCompanyWhereUsersHaveAuthorizationCode('commission-observer-always', $company);
//        foreach($usrs as $usr){
//            $id = $usr->getId();
//            if(!in_array($id, $observers) and !in_array($id, $admins)){
//                $observers[] = $id;
//            }
//        }
//
//        $customManagers = array();
//        $customManager_ids = array();
//
//        if($commission_id !== 0){
//
//            $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);
//
//            if($commission){
//
//                $criteria = array(
//                    'commission' => $commission,
//                );
//                $cucrs = $em->getRepository('AppBundle:CommissionUserCompanyRelation')->findBy($criteria);
//
//                foreach($cucrs as $cucr){
//
//                    if($cucr->getUserCompany()->getData()->getStatus()->getCode() == 'enabled'){
//
//                        $userCompany_id = $cucr->getUserCompany()->getId();
//
//                        $customManager = array(
//                            'userCompany_id' => $userCompany_id,
//                            'type_id' => $cucr->getCommissionUserCompanyRelationType()->getId(),
//                        );
//
//                        if(!in_array($userCompany_id, $observers) and !in_array($userCompany_id, $admins) and !in_array($userCompany_id, $customManager_ids)){
//                            $customManagers[] = $customManager;
//                            $customManager_ids[] = $userCompany_id;
//                        }
//
//                    }
//
//                }
//
//            }
//
//        }
//
//        //Role pro správu zakázky přenášíme v JSON odpovědi
//        $roles = array();
//        foreach($em->getRepository('AppBundle:CommissionUserCompanyRelationType')->findAll() as $cucrt){
//            $roles[$cucrt->getCode()] = $cucrt->getId();
//        }
//
//        $data = array(
//            'ucrs' => $ucrs,
//            'admins' => $admins,
//            'observers' => $observers,
//            'roles' => $roles,
//            'customManagers' => $customManagers,
//        );
//
//        $response = new JsonResponse($data);
//
//        return $response;
//
//    }

    /**
     * @Route("/commission/{commission_id}/duplicate/")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return Response
     */
    public function duplicateAction($commission_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        if(!$commission){
            $this->addFlash('danger', 'Tato zakázka neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $user = $this->getUser();
        $companies = $user->getCompaniesEnabled();
        $company = $commission->getCampaign()->getClient()->getCompany();

        //Kontakt je ve společnosti, do které uživatel nemá přístup
        if(!$companies->contains($company)){
            $this->addFlash('danger', 'K této zakázce nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

        $authorizationChecker = new AuthorizationChecker($em);
        $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('commission-read', $user, $company);

        if(!$check){
            $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění zobrazit detail zakázky.');

            return $this->redirectToRoute('app_client_list');
        }

        $commission2 = new Commission();
        $commission2->setCampaign($commission->getCampaign());
        $commission2->setCreated(new \DateTime());
        $commission2->setBillable($commission->getBillable());
        $commission2->setRepeatable(true);
        $commission2->setName($commission->generateName());
        $commission2->setClientApproved($commission->getClientApproved());

        if($commission->getRepeatable()){

            /** @var Campaign $campaign */
            $campaign = $commission->getCampaign();
            $criteria = [
                'enabled' => true,
                'closed' => false,
                'campaign' => $campaign,
                'repeatable' => true,
            ];
            $orderBy = [
                'id' => 'DESC',
            ];
            $cs = $em->getRepository('AppBundle:Commission')->findBy($criteria, $orderBy);
            $c = $cs[0];

            $yearmonth = $em->getRepository('AppBundle:YearMonth')->find($c->getEndDate()->getId()+1);
            $commission2->setStartDate($yearmonth);

            $yearmonth = $em->getRepository('AppBundle:YearMonth')->find($c->getEndDate()->getId()+1+$commission->getEndDate()->getId()-$commission->getStartDate()->getId());
            $commission2->setEndDate($yearmonth);

//            dump($commission2);exit;

        }

        $allocationContainers = $commission->getAllocationContainers();
        /** @var AllocationContainer $allocationContainer */
        foreach($allocationContainers as $allocationContainer){
            $allocationContainerCopy = clone $allocationContainer;
            $allocationContainerCopy->setCommission($commission2);
            $commission2->addAllocationContainer($allocationContainerCopy);

            $em->persist($allocationContainerCopy);

        }

        $ymDiff = $commission2->getStartDate()->getId() - $commission->getStartDate()->getId();

        $allocationUnits = $commission->getAllocationUnits();
        /** @var AllocationUnit $allocationUnit */
        foreach($allocationUnits as $allocationUnit){
            $allocationUnitCopy = clone $allocationUnit;
            $allocationUnitCopy->setHoursReal(0); //TODO - toto je sporné...

            $id = $allocationUnit->getYearMonth()->getId() + $ymDiff;
            $yearmonth = $em->getRepository('AppBundle:YearMonth')->find($id);
            $allocationUnitCopy->setYearMonth($yearmonth);

            $allocationUnitCopy->setCommission($commission2);
            $commission2->addAllocationUnit($allocationUnitCopy);
        }

        $em->persist($commission2);

        $em->flush();

        $this->addFlash('success', 'Zakázka byla zduplikována.');

        $params = [
            'campaign_id' => $commission2->getCampaign()->getId(),
        ];
        return $this->redirectToRoute('app_campaign_detail', $params);

    }

	/**
	 * @Route("/commission/{commission_id}/delete/")
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 * @return Response
	 */
	public function deleteAction($commission_id, Request $request){

		$em = $this->getDoctrine()->getManager();

		$commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

		if(!$commission){
			$this->addFlash('danger', 'Tato zakázka neexistuje.');
			return $this->redirectToRoute('app_client_list');
		}

		$user = $this->getUser();
		$companies = $user->getCompaniesEnabled();
		$company = $commission->getCampaign()->getClient()->getCompany();

		//Kontakt je ve společnosti, do které uživatel nemá přístup
		if(!$companies->contains($company)){
			$this->addFlash('danger', 'K této zakázce nemáte přístup.');
			return $this->redirectToRoute('app_client_list');
		}

		$authorizationChecker = new AuthorizationChecker($em);
		$check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('commission-read', $user, $company);

		if(!$check){
			$this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění zobrazit detail zakázky.');

			return $this->redirectToRoute('app_client_list');
		}

		if($commission->getTimesheets()->count() > 0){
			$this->addFlash('danger', 'Tuto zakázku nelze odstranit.');

			$params = [
				'campaign_id' => $commission->getCampaign()->getId(),
			];
			return $this->redirectToRoute('app_campaign_detail', $params);
		}

		$em->remove($commission);

		$em->flush();

		$this->addFlash('success', 'Zakázka byla odstraněna.');

		$params = [
			'campaign_id' => $commission->getCampaign()->getId(),
		];
		return $this->redirectToRoute('app_campaign_detail', $params);

	}

    /**
     * @Route("/commission/{commission_id}/update/")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return Response
     */
    public function updateAction($commission_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        if(!$commission){
            $this->addFlash('danger', 'Tato zakázka neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $user = $this->getUser();
        $companies = $user->getCompaniesEnabled();
        $company = $commission->getCampaign()->getClient()->getCompany();

        //Kontakt je ve společnosti, do které uživatel nemá přístup
        if(!$companies->contains($company)){
            $this->addFlash('danger', 'K této zakázce nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

        $authorizationChecker = new AuthorizationChecker($em);
        $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('commission-read', $user, $company);

        if(!$check){
            $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění zobrazit detail zakázky.');

            return $this->redirectToRoute('app_client_list');
        }

        $form = $this->getCommissionForm($commission, $commission->getCampaign(), array(
            'method' => 'post',
            'action' => $this->generateUrl('app_commission_update', array(
                'commission_id' => $commission_id,
            )
        )));

        $form->handleRequest($request);

        if($form->isValid()){


//            //nejdříve odstraníme staré vazby
//            foreach($cucrs_old as $cucr){
//                $em->remove($cucr);
//                $commission->removeCommissionUserCompanyRelation($cucr);
//            }

            $em->persist($commission);
            $em->flush();

            $this->addFlash('success', 'Zakázka '.$commission->getName().' byla úspěšně upravena.');

            $params = array(
                'commission_id' => $commission->getId(),
            );
            return $this->redirectToRoute('app_commission_detail', $params);

        }

        $data = array(
            'form' => $form->createView(),
            'commission' => $commission,
        );

        return $data;

    }

//    /**
//     * @route("/commissions/managed/")
//     * @template()
//     *
//     * @return Response
//     */
//    public function listAction(){
//
//        $user = $this->getUser();
//
//        $em = $this->getDoctrine()->getManager();
//
//        $cm = new CommissionManager($em);
//
//        $m = array(
//            'admin',
//            'observer'
//        );
//        $commissionUserCompanyRelations = $cm->getCommissionUserCompanyRelationDOsOfUser($user, 'enabled', $m);
//
//        //Can create
//        $authorizationIndividual = new AuthorizationIndividual($em);
//        $companies = $authorizationIndividual->getCompaniesWhereUserHasAuthorizationCode('commission-create', $user);
//
//        $canCreate = true;
//        if($companies->count() == 0){
//            $canCreate = false;
//        }
//
//        $data = array(
//            'commissionUserCompanyRelations' => $commissionUserCompanyRelations,
//            'canCreate' => $canCreate,
//        );
//
//        return $data;
//
//    }

    /**
     * @Route("/commission/{commission_id}/detail/", name="app_commission_detail")
     * @Route("/commission/{commission_id}/detail/{allocation_id}", name="app_commission_detail_allocation")
     * @Template()
     *
     * @return Response
     */
    public function detailAction($commission_id, Request $request, $allocation_id = null){

        $em = $this->getDoctrine()->getManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        if(!$commission){
            $this->addFlash('danger', 'Tato zakázka neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $user = $this->getUser();
        $companies = $user->getCompaniesEnabled();
        $company = $commission->getCampaign()->getClient()->getCompany();

        //Kontakt je ve společnosti, do které uživatel nemá přístup
        if(!$companies->contains($company)){
            $this->addFlash('danger', 'K této zakázce nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

        $authorizationChecker = new AuthorizationChecker($em);
        $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('commission-read', $user, $company);

        if(!$check){
            $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění zobrazit detail zakázky.');

            return $this->redirectToRoute('app_client_list');
        }

        $commissionDO = new CRUDLDataObject();
        $commissionDO->setData($commission);

        $criteria = [
            'commission' => $commission,
            'enabled' => true,
        ];
        $orderBy = [
            'version' => 'ASC',
        ];
        $allocations = $em->getRepository('AppBundle:AllocationContainer')->findBy($criteria, $orderBy);

        $criteria = [
            'commission' => $commission,
            'enabled' => true,
            'clientApproved' => true,
        ];
        $choosedAllocation = $em->getRepository('AppBundle:AllocationContainer')->findOneBy($criteria);

        //Formulář na úpravu alokace

        if($allocation_id == null){
            $criteria = [
                'commission' => $commission,
                'clientApproved' => true,
                'enabled' => true,
            ];
            /** @var AllocationContainer $allocationContainer */
            $allocationContainer = $em->getRepository('AppBundle:AllocationContainer')->findOneBy($criteria);

            if(!$allocationContainer){
                $criteria = [
                    'commission' => $commission,
                    'enabled' => true,
                ];
                /** @var AllocationContainer $allocationContainer */
                $allocationContainer = $em->getRepository('AppBundle:AllocationContainer')->findOneBy($criteria);
            };
        }else{
            /** @var AllocationContainer $allocationContainer */
            $allocationContainer = $em->getRepository('AppBundle:AllocationContainer')->find($allocation_id);
        }

        if(!$allocationContainer){
            $this->addFlash('danger', 'Tento rozpočet neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $user = $this->getUser();
        $companies = $user->getCompaniesEnabled();
        $company = $commission->getCampaign()->getClient()->getCompany();

        //Kontakt je ve společnosti, do které uživatel nemá přístup
        if(!$companies->contains($company)){
            $this->addFlash('danger', 'K tomuto rozpočtu nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

        $authorizationChecker = new AuthorizationChecker($em);
        $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('allocation-update', $user, $company); //TODO - jiné oprávnění

        if(!$check){
            $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění měnit plánování zakázky.');

            return $this->redirectToRoute('app_client_list');
        }

        $params = [
            'commission_id' => $commission_id,
	        'allocation_id' => $allocationContainer->getId(),
        ];
        $form = $this->getForm($allocationContainer, $commission, array(
            'method' => 'post',
            'action' => $this->generateUrl('app_commission_detail_allocation', $params),
        ));

        $form->handleRequest($request);

        if($form->isValid()){

            //Původní entita se vypíná
            $allocationContainer->setEnabled(false);
            $em->persist($allocationContainer);

            /** @var AllocationContainer $allocationContainerData */
            $allocationContainerData = $form->getData();

            //Vytváří se nová entita, přejímací data z původní a z odeslaného formuláře
            $allocationContainer = new AllocationContainer();
            $allocationContainer->setCommission($allocationContainerData->getCommission());
            $allocationContainer->setEnabled(true);
            $allocationContainer->setClientApproved($allocationContainerData->getClientApproved());
            $allocationContainer->setVersion($allocationContainerData->getVersion());

            /** @var AllocationContainerList $allocationContainerListData */
            foreach($allocationContainerData->getAllocationContainerLists() as $allocationContainerListData){

                $allocationContainerList = new AllocationContainerList();
                $allocationContainerList->setName($allocationContainerListData->getName());

                /** @var AllocationContainerListItem $allocationContainerListItemData */
                foreach($allocationContainerListData->getAllocationContainerListItems() as $allocationContainerListItemData){

                    $allocationContainerListItem = clone $allocationContainerListItemData;

                    $allocationContainerListItem->setAllocationContainerList($allocationContainerList);
                    $allocationContainerList->addAllocationContainerListItem($allocationContainerListItem);

                }

                $allocationContainer->addAllocationContainerList($allocationContainerList);
                $allocationContainerList->setAllocationContainer($allocationContainer);

            }

            $em->persist($allocationContainer);

            $em->flush();

            $l = 'Rozpočet';

            $this->addFlash('success', $l.' pro '.$commission->getName().' byl upraven.');

            $params = [
                'commission_id' => $commission_id,
                'allocation_id' => $allocationContainer->getId(),
            ];
            return $this->redirectToRoute('app_commission_detail_allocation', $params);

        }

        $data = array(
            'commission' => $commissionDO,
            'allocations' => $allocations,
            'choosedAllocation' => $choosedAllocation,
            'allocationContainer' => $allocationContainer,
            'form' => $form->createView(),
        );

        return $data;

    }

    /**
     * @route("/commission/{commission_id}/timesheets/")
     * @template()
     *
     * @return Response
     */
    public function timesheetlistAction($commission_id){

        $user = $this->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        if(!$commission){

            $this->addFlash('danger', 'Tato zakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        $cm = new CommissionManager($em);

        $m = array(
            'observer',
            'admin',
        );
        $check = $cm->checkCommissionUserCompanyManagedByRoleTypeCodes($user, $commission, 'enabled', $m);

        if(!$check){

            $this->addFlash('danger', 'Nemáte oprávnění zobrazovat tuto zakázku.');

            return $this->redirectToRoute('app_commission_list');

        }

        $commissionUserCompany = $cm->getCommissionUserCompany($user, $commission, 'enabled');

        $data = array(
            'commissionUserCompany' => $commissionUserCompany,
            'commission' => $commission,
        );

        $tldo = new TimesheetListDataObject($em, $cm, $user, $commission);

        $data = array_merge($data, $tldo->getData());

        return $data;

    }

    /**
     * @route("/commission/{commission_id}/timesheets/{year}/{month}/")
     * @template()
     *
     * @return Response
     */
    public function timesheetlistyearmonthAction($commission_id, $year, $month){

        $return = $this->initTimesheetListYearmonth($commission_id, $year, $month);

        if($return['controllerRedirect'] instanceof ControllerRedirect){

            $this->addFlash($return['controllerRedirect']->getFlashType(), $return['controllerRedirect']->getFlashMessage());

            return $this->redirectToRoute($return['controllerRedirect']->getRedirectRoute(), $return['controllerRedirect']->getRedirectRoute()->getParams());

        }

        return $return['data'];

    }

    /**
     * @route("/commission/{commission_id}/timesheets/{year}/{month}/export/xls/")
     * @template()
     *
     * @return Response
     */
    public function timesheetlistyearmonthexportxlsAction($commission_id, $year, $month){

        $return = $this->initTimesheetListYearmonth($commission_id, $year, $month);

        if($return['controllerRedirect'] instanceof ControllerRedirect){

            $this->addFlash($return['controllerRedirect']->getFlashType(), $return['controllerRedirect']->getFlashMessage());

            return $this->redirectToRoute($return['controllerRedirect']->getRedirectRoute(), $return['controllerRedirect']->getRedirectRoute()->getParams());

        }

        $user = $return['variables']['user'];
        $commission = $return['variables']['commission'];
        $yearmonth = $return['variables']['yearmonth'];

        //TODO - vyřešit problém s kódováním
//        $author = $user->getFullname();
//        $author = mb_convert_encoding($author, 'ISO-8859-1', 'UTF-8');
        $author = "Feeio";

        //TODO - CompanyName se neukládá korektně
        $companyName = $commission->getCompany()->getName();

        $title = Slug::getSlug($commission->getName().$yearmonth->getShortCode());

        //TODO - generování XLS zjednodušit a převést do jiné třídy
        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator($author)
                       ->setLastModifiedBy($author)
                       ->setTitle($title)
                       ->setSubject($title)
                       ->setDescription("Document ".$title." generated by Feeio (www.feeio.com). ")
                       ->setKeywords($title." feeio")
                       ->setCompany($companyName);

        $styleArrayTotal = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
            ));

        $styleArrayGrandTotal = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
            ));

        $styleArrayHeader = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
            ));

        //TODO - zajistit generování podle nastavení sloupců uživatele
        // == začátek plnění dat
        $phpExcelObject->setActiveSheetIndex(0);
        $phpExcelObject->getActiveSheet()->setTitle('Timesheety');

        $r = 1;
        $c = 0;
        $grandTotal = 0;

        foreach($return['data']['tableData']['table']->getTableDefaultColumns() as $column){

//            if(!isset($return['data']['tableData']['userDefaultColumns'][$column->getId()]) or !$return['data']['tableData']['userDefaultColumns'][$column->getId()]->getHidden()){
                $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $column->getTitle());
                $c++;
//            }

        }
        foreach($return['data']['tableData']['userColumns'] as $column){

            //TODO - zobrazení sloupce pro formulu
//            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $column->getTitle());
            $c++;

        }

        //Ukládáme počet sloupců do proměnné ve formě písmena
        $cCount = chr(64 + $c);

        //Stylování prvního řádku
        $phpExcelObject->getActiveSheet()->getStyle('A1:'.$cCount.'1')->applyFromArray($styleArrayHeader);

        $r++;

        foreach($return['data']['timesheets'] as $row){

            $c = 0;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getAuthor()->getFullname());
            $c++;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getDate()->format('j. n. Y'));
            $c++;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getActivity()->getName());
            $c++;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getDescription());
            $c++;

            $duration = $row['timesheet']->getDuration();
            $duration = round($duration/60, 2);
            $grandTotal = $grandTotal + $duration;
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $duration);
            $c++;

            foreach($return['data']['tableData']['userColumns'] as $column){

                //TODO - Výpočet z formuly
                $c++;

            }

            $r++;

        }

        //Sumace
        $c = 0;

        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, 'Celkem');
        $c++;
        $c++;
        $c++;
        $c++;

        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $grandTotal);
        $c++;

        foreach($return['data']['tableData']['userColumns'] as $column){

            //TODO - Výpočet z formuly
            $c++;

        }

        //Stylování grandtotal řádku
        $phpExcelObject->getActiveSheet()->getStyle('A'.$r.':'.$cCount.$r)->applyFromArray($styleArrayGrandTotal);

        //Druhý list
        $timesheets = $return['data']['timesheets'];
        usort($timesheets, array($this, "cmpByActivity"));

        //TODO - zajistit generování podle nastavení sloupců uživatele
        // == začátek plnění dat
        $phpExcelObject->createSheet();
        $phpExcelObject->setActiveSheetIndex(1);
        $phpExcelObject->getActiveSheet()->setTitle('Timesheety podle aktivit');

        $r = 1;
        $c = 0;
        $grandTotal = 0;

        foreach($return['data']['tableData']['table']->getTableDefaultColumns() as $column){

//            if(!isset($return['data']['tableData']['userDefaultColumns'][$column->getId()]) or !$return['data']['tableData']['userDefaultColumns'][$column->getId()]->getHidden()){
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $column->getTitle());
            $c++;
//            }

        }
        foreach($return['data']['tableData']['userColumns'] as $column){

            //TODO - zobrazení sloupce pro formulu
//            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $column->getTitle());
            $c++;

        }

        //Ukládáme počet sloupců do proměnné ve formě písmena
        $cCount = chr(64 + $c);

        //Stylování prvního řádku
        $phpExcelObject->getActiveSheet()->getStyle('A1:'.$cCount.'1')->applyFromArray($styleArrayHeader);

        $r++;

        $total = 0;

        $i = 0;
        foreach($timesheets as $row){

            $c = 0;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getAuthor()->getFullname());
            $c++;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getDate()->format('j. n. Y'));
            $c++;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getActivity()->getName());
            $c++;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getDescription());
            $c++;

            $duration = $row['timesheet']->getDuration();
            $duration = round($duration/60, 2);
            $total = $total + $duration;
            $grandTotal = $grandTotal + $duration;
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $duration);
            $c++;

            foreach($return['data']['tableData']['userColumns'] as $column){

                //TODO - Výpočet z formuly
                $c++;

            }

            $i++;

            $r++;

            $activity = $row['timesheet']->getActivity();

            if(count($timesheets) === $i or (isset($timesheets[$i]) and $timesheets[$i]['timesheet']->getActivity() !== $activity)){

                $c = 0;

                $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, 'Celkem '.$activity->getName());
                $c++;
                $c++;
                $c++;
                $c++;

                $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $total);
                $c++;

                foreach($return['data']['tableData']['userColumns'] as $column){

                    //TODO - Výpočet z formuly
                    $c++;

                }

                //Stylování total řádku
                $phpExcelObject->getActiveSheet()->getStyle('A'.$r.':'.$cCount.$r)->applyFromArray($styleArrayTotal);

                $total = 0;

                $r++;

            }

        }

        //Sumace
        $c = 0;

        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, 'Celkem');
        $c++;
        $c++;
        $c++;
        $c++;

        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $grandTotal);
        $c++;

        foreach($return['data']['tableData']['userColumns'] as $column){

            //TODO - Výpočet z formuly
            $c++;

        }

        //Stylování grandtotal řádku
        $phpExcelObject->getActiveSheet()->getStyle('A'.$r.':'.$cCount.$r)->applyFromArray($styleArrayGrandTotal);

        //Třetí list
        $timesheets = $return['data']['timesheets'];
        usort($timesheets, array($this, "cmpByAuthor"));

        //TODO - zajistit generování podle nastavení sloupců uživatele
        // == začátek plnění dat
        $phpExcelObject->createSheet();
        $phpExcelObject->setActiveSheetIndex(2);
        $phpExcelObject->getActiveSheet()->setTitle('Timesheety podle lidí');

        $r = 1;
        $c = 0;
        $grandTotal = 0;

        foreach($return['data']['tableData']['table']->getTableDefaultColumns() as $column){

//            if(!isset($return['data']['tableData']['userDefaultColumns'][$column->getId()]) or !$return['data']['tableData']['userDefaultColumns'][$column->getId()]->getHidden()){
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $column->getTitle());
            $c++;
//            }

        }
        foreach($return['data']['tableData']['userColumns'] as $column){

            //TODO - zobrazení sloupce pro formulu
//            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $column->getTitle());
            $c++;

        }

        //Ukládáme počet sloupců do proměnné ve formě písmena
        $cCount = chr(64 + $c);

        //Stylování prvního řádku
        $phpExcelObject->getActiveSheet()->getStyle('A1:'.$cCount.'1')->applyFromArray($styleArrayHeader);

        $r++;

        $total = 0;

        $i = 0;
        foreach($timesheets as $row){

            $c = 0;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getAuthor()->getFullname());
            $c++;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getDate()->format('j. n. Y'));
            $c++;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getActivity()->getName());
            $c++;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getDescription());
            $c++;

            $duration = $row['timesheet']->getDuration();
            $duration = round($duration/60, 2);
            $total = $total + $duration;
            $grandTotal = $grandTotal + $duration;
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $duration);
            $c++;

            foreach($return['data']['tableData']['userColumns'] as $column){

                //TODO - Výpočet z formuly
                $c++;

            }

            $i++;

            $r++;

            $user = $row['timesheet']->getAuthor();

            if(count($timesheets) === $i or (isset($timesheets[$i]) and $timesheets[$i]['timesheet']->getAuthor() !== $user)){

                $c = 0;

                $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, 'Celkem '.$user->getFullname());
                $c++;
                $c++;
                $c++;
                $c++;

                $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $total);
                $c++;

                foreach($return['data']['tableData']['userColumns'] as $column){

                    //TODO - Výpočet z formuly
                    $c++;

                }

                //Stylování total řádku
                $phpExcelObject->getActiveSheet()->getStyle('A'.$r.':'.$cCount.$r)->applyFromArray($styleArrayTotal);

                $total = 0;

                $r++;

            }

        }

        //Sumace
        $c = 0;

        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, 'Celkem');
        $c++;
        $c++;
        $c++;
        $c++;

        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $grandTotal);
        $c++;

        foreach($return['data']['tableData']['userColumns'] as $column){

            //TODO - Výpočet z formuly
            $c++;

        }

        //Stylování grandtotal řádku
        $phpExcelObject->getActiveSheet()->getStyle('A'.$r.':'.$cCount.$r)->applyFromArray($styleArrayGrandTotal);

        //Čtvrtý list
        $timesheets = $return['data']['timesheets'];
        usort($timesheets, array($this, "cmpByDay"));

        //TODO - zajistit generování podle nastavení sloupců uživatele
        // == začátek plnění dat
        $phpExcelObject->createSheet();
        $phpExcelObject->setActiveSheetIndex(3);
        $phpExcelObject->getActiveSheet()->setTitle('Timesheety podle dní');

        $r = 1;
        $c = 0;
        $grandTotal = 0;

        foreach($return['data']['tableData']['table']->getTableDefaultColumns() as $column){

//            if(!isset($return['data']['tableData']['userDefaultColumns'][$column->getId()]) or !$return['data']['tableData']['userDefaultColumns'][$column->getId()]->getHidden()){
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $column->getTitle());
            $c++;
//            }

        }
        foreach($return['data']['tableData']['userColumns'] as $column){

            //TODO - zobrazení sloupce pro formulu
//            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $column->getTitle());
            $c++;

        }

        //Ukládáme počet sloupců do proměnné ve formě písmena
        $cCount = chr(64 + $c);

        //Stylování prvního řádku
        $phpExcelObject->getActiveSheet()->getStyle('A1:'.$cCount.'1')->applyFromArray($styleArrayHeader);

        $r++;

        $total = 0;

        $i = 0;
        foreach($timesheets as $row){

            $c = 0;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getAuthor()->getFullname());
            $c++;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getDate()->format('j. n. Y'));
            $c++;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getActivity()->getName());
            $c++;

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $row['timesheet']->getDescription());
            $c++;

            $duration = $row['timesheet']->getDuration();
            $duration = round($duration/60, 2);
            $total = $total + $duration;
            $grandTotal = $grandTotal + $duration;
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $duration);
            $c++;

            foreach($return['data']['tableData']['userColumns'] as $column){

                //TODO - Výpočet z formuly
                $c++;

            }

            $i++;

            $r++;

            $day = $row['timesheet']->getDate()->format('j. n. Y');

            if(count($timesheets) === $i or (isset($timesheets[$i]) and $timesheets[$i]['timesheet']->getDate()->format('j. n. Y') !== $day)){

                $c = 0;

                $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, 'Celkem '.$day);
                $c++;
                $c++;
                $c++;
                $c++;

                $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $total);
                $c++;

                foreach($return['data']['tableData']['userColumns'] as $column){

                    //TODO - Výpočet z formuly
                    $c++;

                }

                //Stylování total řádku
                $phpExcelObject->getActiveSheet()->getStyle('A'.$r.':'.$cCount.$r)->applyFromArray($styleArrayTotal);

                $total = 0;

                $r++;

            }

        }

        //Sumace
        $c = 0;

        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, 'Celkem');
        $c++;
        $c++;
        $c++;
        $c++;

        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow($c, $r, $grandTotal);
        $c++;

        foreach($return['data']['tableData']['userColumns'] as $column){

            //TODO - Výpočet z formuly
            $c++;

        }

        //Stylování grandtotal řádku
        $phpExcelObject->getActiveSheet()->getStyle('A'.$r.':'.$cCount.$r)->applyFromArray($styleArrayGrandTotal);

        // == konec plnění dat

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // Auto size columns for each worksheet
        foreach ($phpExcelObject->getWorksheetIterator() as $worksheet) {

            $phpExcelObject->setActiveSheetIndex($phpExcelObject->getIndex($worksheet));

            $sheet = $phpExcelObject->getActiveSheet();
            $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            /** @var \PHPExcel_Cell $cell */
            foreach ($cellIterator as $cell) {
                $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
            }
        }

        $filename = $title.'.xls';
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        $objWriter = new \PHPExcel_Writer_Excel5($phpExcelObject);
        $objWriter->save('php://output');
        exit;

    }

    private function cmpByActivity($a, $b)
    {
        return strcmp($a['timesheet']->getActivity()->getName(), $b['timesheet']->getActivity()->getName());
    }

    private function cmpByAuthor($a, $b)
    {
        return strcmp($a['timesheet']->getAuthor()->getLastname(), $b['timesheet']->getAuthor()->getLastname());
    }

    private function cmpByDay($a, $b)
    {
        return strcmp($a['timesheet']->getDate()->format('Y-m-d'), $b['timesheet']->getDate()->format('Y-m-d'));
    }


    private function initTimesheetListYearmonth($commission_id, $year, $month){

        $cr = false;

        $user = $this->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        $criteria = array(
            'year' => $year,
            'month' => $month,
        );
        $yearmonth = $em->getRepository('AppBundle:YearMonth')->findOneBy($criteria);

        if(!$commission or !$yearmonth){

            $cr = new ControllerRedirect('danger', 'Tato zakázka neexistuje.', 'app_commission_list');

        }

        $cm = new CommissionManager($em);

        $m = array(
            'observer',
            'admin',
        );
        $check = $cm->checkCommissionUserCompanyManagedByRoleTypeCodes($user, $commission, 'enabled', $m);

        if(!$check){

            $cr = new ControllerRedirect('danger', 'Nemáte oprávnění zobrazovat tuto zakázku.', 'app_commission_list');

        }

        $commissionUserCompany = $cm->getCommissionUserCompany($user, $commission, 'enabled');

        $timesheets = $em->getRepository('AppBundle:Timesheet')->findByCommissionAndYearmonth($commission, $yearmonth);

        $tableData = TableData::getData($em, $user, 'table-timesheetlistyearmonth');

        //Přepravka (simple)
        $data = [
            'commissionUserCompany' => $commissionUserCompany,
            'timesheets' => $timesheets,
            'tableData' => $tableData,
            'commission' => $commission,
            'yearmonth' => $yearmonth,
        ];

        $return = [];
        $return['controllerRedirect'] = $cr;
        $return['variables'] = [];
        $return['variables']['em'] = $em;
        $return['variables']['user'] = $user;
        $return['variables']['yearmonth'] = $yearmonth;
        $return['variables']['commission'] = $commission;
        $return['variables']['commissionManager'] = $cm;
        $return['data'] = $data;

        return $return;

    }

    /**
     * @param \AppBundle\Entity\Commission      $commission
     * @param                                   $params
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getCommissionForm(Commission $commission, Campaign $campaign, $params){

        $type = new CommissionFormType($campaign);

        $form = $this->createForm($type, $commission, $params);

        return $form;

    }

    /**
     * @param \AppBundle\Entity\AllocationContainer $allocationContainer
     * @param Commission                            $commission
     * @param                                       $params
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getForm(AllocationContainer $allocationContainer, Commission $commission, $params){

        $type = new AllocationContainerFormType($commission, $allocationContainer);

        $form = $this->createForm($type, $allocationContainer, $params);

        return $form;

    }

}
