<?php

namespace AppBundle\Controller;

use AppBundle\DataObject\CRUDLDataObject;
use AppBundle\DataObject\TimesheetListDataObject;
use AppBundle\DependencyInjection\Authorization\AuthorizationChecker;
use AppBundle\DependencyInjection\Authorization\AuthorizationCompany;
use AppBundle\DependencyInjection\Authorization\AuthorizationIndividual;
use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
use AppBundle\DependencyInjection\ControllerRedirect;
use AppBundle\DependencyInjection\FreeHoursManager;
use AppBundle\DependencyInjection\ImageCreator\ImageCreator;
use AppBundle\Entity\AllocationContainer;
use AppBundle\Entity\AllocationContainerList;
use AppBundle\Entity\AllocationContainerListItem;
use AppBundle\Entity\AllocationUnit;
use AppBundle\Entity\Campaign;
use AppBundle\Entity\CampaignManager;
use AppBundle\Entity\CommissionUserCompanyRelation;
use AppBundle\Entity\Company;
use AppBundle\Entity\UserCompany;
use AppBundle\Entity\YearMonth;
use AppBundle\Form\Type\AllocationContainerFormType;
use AppBundle\Form\Type\AllocationFormUnitType;
use AppBundle\Form\Type\AllocationUnitFormType;
use AppBundle\Library\Slug;
use DeepCopy\DeepCopy;
use DeepCopy\Filter\Doctrine\DoctrineCollectionFilter;
use DeepCopy\Matcher\PropertyTypeMatcher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;
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
use Symfony\Component\VarDumper\VarDumper;
use TableBundle\DependencyInjection\TableData;
use UserBundle\Entity\User;

/**
 * Allocation controller.
 */
class AllocationController extends BaseController
{

//    /**
//     * @Route("/commission/{commission_id}/allocation/create/")
//     *
//     * @Template()
//     *
//     * @param \Symfony\Component\HttpFoundation\Request $request
//     *
//     * @return Response
//     */
//    public function createAction($commission_id, Request $request){
//
//        $em = $this->getDoctrine()->getManager();
//
//        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);
//
//        if(!$commission){
//            $this->addFlash('danger', 'Tato zakázka neexistuje.');
//            return $this->redirectToRoute('app_client_list');
//        }
//
//        $user = $this->getUser();
//        $companies = $user->getCompaniesEnabled();
//        $company = $commission->getCampaign()->getClient()->getCompany();
//
//        //Kontakt je ve společnosti, do které uživatel nemá přístup
//        if(!$companies->contains($company)){
//            $this->addFlash('danger', 'K této zakázce nemáte přístup.');
//            return $this->redirectToRoute('app_client_list');
//        }
//
//        $authorizationChecker = new AuthorizationChecker($em);
//        $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('allocation-create', $user, $company);
//
//        if(!$check){
//            $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění plánovat zakázku.');
//
//            return $this->redirectToRoute('app_client_list');
//        }
//
//        $allocationContainer = new AllocationContainer();
//        $allocationContainer->setYearmonth($commission->getStartDate());
//        $allocationContainer->setName($allocationContainer->getYearmonth()->getCode());
//
//        $yearmonthAllocation = ($allocationContainer->getYearmonth() !== null);
//
//        $params = [
//            'commission_id' => $commission_id,
//        ];
//        $form = $this->getForm($allocationContainer, $commission, array(
//            'method' => 'post',
//            'action' => $this->generateUrl('app_allocation_create', $params),
//        ));
//
//        $form->handleRequest($request);
//
//        if($form->isValid()){
//
//            $allocationContainer->setCommission($commission);
//            $allocationContainer->setYearmonth($commission->getStartDate());
//            $allocationContainer->setName($commission->getStartDate()->getCode());
//            $l = 'Rozpočet';
//
//            $em->persist($allocationContainer);
//
//            $em->flush();
//
//            $this->addFlash('success', $l.' pro '.$commission->getName().' byl vytvořen.');
//
//            $params = [
//                'commission_id' => $commission_id,
//            ];
//            return $this->redirectToRoute('app_allocation_list', $params);
//
//        }
//
//        $data = array(
//            'allocationContainer' => $allocationContainer,
//            'commission' => $commission,
//            'form' => $form->createView(),
//            'yearmonthAllocation' => $yearmonthAllocation,
//        );
//
//        return $data;
//
//    }


    /**
     * @Route("/allocation/{allocation_id}/duplicate")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return Response
     */
    public function duplicateAction($allocation_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $allocationContainer = $em->getRepository('AppBundle:AllocationContainer')->find($allocation_id);

        if(!$allocationContainer){
            $this->addFlash('danger', 'Tento rozpočet neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $user = $this->getUser();
        $companies = $user->getCompaniesEnabled();
        $company = $allocationContainer->getCommission()->getCampaign()->getClient()->getCompany();

        //Kontakt je ve společnosti, do které uživatel nemá přístup
        if(!$companies->contains($company)){
            $this->addFlash('danger', 'K tomuto rozpočtu nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

        $authorizationChecker = new AuthorizationChecker($em);
        $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('allocation-create', $user, $company); //TODO - jiné oprávnění

        if(!$check){
            $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění plánovat zakázku.');

            return $this->redirectToRoute('app_client_list');
        }

//        $ym = $allocationContainer->getYearmonth();
//        $ym2 = $em->getRepository('AppBundle:YearMonth')->find($ym->getId()+1);
        $allocationContainerCopy = clone $allocationContainer;
        $allocationContainerCopy->setVersion($allocationContainer->getCommission()->getAllocationContainers()->count()+1);
        $allocationContainerCopy->setClientApproved(false);

//        if($allocationContainer->getYearmonth()){
//            $criteria = [
//                'id' => ($allocationContainer->getYearmonth()->getId()+1),
//            ];
//            $ym = $em->getRepository('AppBundle:YearMonth')->findOneBy($criteria);
//            $allocationContainerCopy->setYearmonth($ym);
//
//            $em->persist($allocationContainerCopy);
//            $em->flush();
//
//            $this->addFlash('success', 'Byla vytvořena nová alokace.');
//
//            $params = [
//                'commission_id' => $allocationContainer->getCommission()->getId(),
//            ];
//            return $this->redirectToRoute('app_allocation_list', $params);
//
//        }else{

            $em->persist($allocationContainerCopy);
            $em->flush();

            $this->addFlash('success', 'Byla vytvořena nová verze rozpočtu.');

            $params = [
                'commission_id' => $allocationContainer->getCommission()->getId(),
            ];
            return $this->redirectToRoute('app_commission_detail', $params);

//        }

    }

    /**
     * @Route("/allocation/{allocation_id}/choose")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return Response
     */
    public function chooseAction($allocation_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $allocationContainer = $em->getRepository('AppBundle:AllocationContainer')->find($allocation_id);

        if(!$allocationContainer){
            $this->addFlash('danger', 'Tento rozpočet neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $user = $this->getUser();
        $companies = $user->getCompaniesEnabled();
        $company = $allocationContainer->getCommission()->getCampaign()->getClient()->getCompany();

        //Kontakt je ve společnosti, do které uživatel nemá přístup
        if(!$companies->contains($company)){
            $this->addFlash('danger', 'K tomuto rozpočtu nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

        $authorizationChecker = new AuthorizationChecker($em);
        $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('allocation-update', $user, $company); //TODO - jiné oprávnění

        if(!$check){
            $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění plánovat zakázku.');

            return $this->redirectToRoute('app_client_list');
        }

        /** @var AllocationContainer $v */
        foreach($allocationContainer->getCommission()->getAllocationContainers() as $v){
            $v->setClientApproved(false);
            $em->persist($v);
        }
        $allocationContainer->setClientApproved(true);
        $em->persist($allocationContainer);

        $em->flush();

        $this->addFlash('success', 'Označili jste rozpočet verze '.$allocationContainer->getVersion().' jako schválený klientem.');

        $params = [
            'commission_id' => $allocationContainer->getCommission()->getId(),
        ];
        return $this->redirectToRoute('app_commission_detail', $params);

    }

    /**
     * @Route("/commission/{commission_id}/allocations/")
     *
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return Response
     */
    public function listAction($commission_id, Request $request, $allocationContainer_id = null ){

        $em = $this->getDoctrine()->getManager();

        /** @var Commission $commission */
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
        $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('allocation-list', $user, $company); //TODO změnit na oprávnění

        if(!$check){
            $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění plánovat zakázku.');

            return $this->redirectToRoute('app_client_list');
        }

        $criteria = [
            'commission' => $commission,
        ];
        $orderBy = [
            'id' => 'ASC',
        ];
        $aus = $em->getRepository('AppBundle:AllocationUnit')->findBy($criteria, $orderBy);

        $commissionDO = new CRUDLDataObject();
        $commissionDO->setData($commission);

        $freeHoursManager = new FreeHoursManager($em);

        //Vytváření formuláře
        $jobConsultants = $commission->getCampaign()->getCampaignManagers()->filter(function($entry){
            return $entry->getJobConsultant();
        });
        $jobConsultants = $jobConsultants->toArray();
        uasort($jobConsultants, function($a, $b){
            return $a->getUserCompany()->getUser()->getLastname() > $b->getUserCompany()->getUser()->getLastname();
        });
        $yearMonths = [];
        $model = [
            'aus' => [],
        ];

        for($i = $commission->getEndDate()->getId(); $i >= $commission->getStartDate()->getId(); $i--){

            $yearMonth = $em->getRepository('AppBundle:YearMonth')->find($i);
            $yearMonths[] = $yearMonth;

            /** @var CampaignManager $jobConsultant */
            foreach($jobConsultants as $jobConsultant){

                $criteria = [
                    'yearMonth' => $yearMonth,
                    'userCompany' => $jobConsultant->getUserCompany(),
                    'commission' => $commission,
                ];
                $au = $em->getRepository('AppBundle:AllocationUnit')->findOneBy($criteria);

                if(!$au){
                    $au = new AllocationUnit();
                    $au->setYearMonth($yearMonth);
                    $au->setUserCompany($jobConsultant->getUserCompany());
                    $au->setCommission($commission);
                    $au->setHoursPlan(0);
                }

                //TODO - provizorně
                $id = 118;
                $company = $em->getRepository('AppBundle:Company')->find($id);

                $freeHours = $freeHoursManager->getFreeHoursOfUserInCompany($jobConsultant->getUserCompany()->getUser(), $yearMonth, $company);
                $au->setFreeHours($freeHours);

                $model['aus'][] = $au;

            }
        }

        $formBuilder = $this->createFormBuilder($model);
        $formBuilder->add('aus', 'collection', [
            'entry_type' => new AllocationUnitFormType($commission),
        ]);
        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $aus = $form->get('aus')->getData();
            foreach($aus as $au){
                $em->persist($au);
            }

            $em->flush();

            $this->addFlash('success', 'Alokace hodiny byly uloženy.');

            $params = [
                'commission_id' => $commission_id,
            ];
            return $this->redirectToRoute('app_allocation_list', $params);

        }

        //Předvyplnění
        $criteria = [
            'commission' => $commission,
            'clientApproved' => true,
            'enabled' => true,
        ];
        /** @var AllocationContainer $allocationContainer */
        $allocationContainer = $em->getRepository('AppBundle:AllocationContainer')->findOneBy($criteria);
        $auT = [];
        if($allocationContainer){
            /** @var AllocationContainerList $allocationContainerList */
            foreach($allocationContainer->getAllocationContainerLists() as $allocationContainerList){
                /** @var AllocationContainerListItem $allocationContainerListItem */
                foreach($allocationContainerList->getAllocationContainerListItems() as $allocationContainerListItem){
                    if($allocationContainerListItem->getConcreteSource() and $allocationContainerListItem->getQuantityPlan() > 0 and $allocationContainerListItem->getUnit() === 'h'){
                        /** @var YearMonth $yearMonth */
                        foreach($yearMonths as $yearMonth){
                            $key = $yearMonth->getId().'_'.$allocationContainerListItem->getConcreteSource()->getId();
                            $value = round($allocationContainerListItem->getQuantityPlan()/count($yearMonths));
                            $auT[$key] = $value;
                        }
                    }
                }
            }
        }

        $data = array(
            'commission' => $commissionDO,
            'aus' => $aus,
            'form' => $form->createView(),
            'model' => $model,
            'allocationContainer' => $allocationContainer,
            'auT' => $auT
        );

        return $data;

    }


    /**
     * @Route("/allocation/{allocation_id}/update/")
     *
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return Response
     */
    public function updateAction($allocation_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        /** @var AllocationContainer $allocationContainer */
        $allocationContainer = $em->getRepository('AppBundle:AllocationContainer')->find($allocation_id);

        if(!$allocationContainer){
            $this->addFlash('danger', 'Tento rozpočet neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        /** @var Commission $commission */
        $commission = $allocationContainer->getCommission();

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

//        $yearmonthAllocation = ($allocationContainer->getYearmonth() !== null);

        $params = [
            'allocation_id' => $allocation_id,
        ];
        $form = $this->getForm($allocationContainer, $commission, array(
            'method' => 'post',
            'action' => $this->generateUrl('app_allocation_update', $params),
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
//            $allocationContainer->setYearmonth($allocationContainerData->getYearmonth());
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

//            dump($allocationContainer);exit;
//
//            $criteria = [
//                'version' => $allocationContainer->getVersion(),
//                'commission' => $allocationContainer->getCommission(),
//            ];
//            $acs = $em->getRepository('AppBundle:AllocationContainer')->findBy($criteria);
//            if(count($acs) !== 1){
//                $allocationContainerCopy = clone $allocationContainer;
//                $allocationContainerCopy->setCommission($commission);
//                $allocationContainer->setEnabled(false);
//                $em->persist($allocationContainerCopy);
//            }
//
//            $em->persist($allocationContainer);
//            $em->persist($commission);

            $em->flush();

//            if($allocationContainer->getYearmonth()){
//
//                $this->addFlash('success', 'Alokace byla upravena.');
//
//                $params = [
//                    'commission_id' => $commission->getId(),
//                ];
//                return $this->redirectToRoute('app_allocation_list', $params);
//
//            }else{

                $l = 'Rozpočet';

                $this->addFlash('success', $l.' pro '.$commission->getName().' byl upraven.');

                $params = [
                    'commission_id' => $commission->getId(),
                ];
                return $this->redirectToRoute('app_commission_detail', $params);

//            }

        }

        $data = array(
            'allocationContainer' => $allocationContainer,
            'commission' => $commission,
            'form' => $form->createView(),
//            'yearmonthAllocation' => $yearmonthAllocation,
        );

        return $data;

    }

    /**
     * @route("/allocation/{allocation_id}/delete/")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return Response
     */
    public function deleteAction($allocation_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        /** @var AllocationContainer $allocationContainer */
        $allocationContainer = $em->getRepository('AppBundle:AllocationContainer')->find($allocation_id);

        if(!$allocationContainer){
            $this->addFlash('danger', 'Tento rozpočet neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        /** @var Commission $commission */
        $commission = $allocationContainer->getCommission();

        $user = $this->getUser();
        $companies = $user->getCompaniesEnabled();
        $company = $commission->getCampaign()->getClient()->getCompany();

        //Kontakt je ve společnosti, do které uživatel nemá přístup
        if(!$companies->contains($company)){
            $this->addFlash('danger', 'K tomuto rozpočtu nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

        $authorizationChecker = new AuthorizationChecker($em);
        $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('allocation-delete', $user, $company); //TODO - jiné oprávnění

        if(!$check){
            $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění odstraňovat rozpočty zakázky.');

            return $this->redirectToRoute('app_client_list');
        }

        $allocationContainer->setEnabled(false);
        $allocationContainer->setClientApproved(false);

        $em->persist($allocationContainer);
        $em->flush();

        $this->addFlash('success', 'Rozpočet byl odstraněn.');

        $params = [
            'commission_id' => $commission->getId(),
        ];
        return $this->redirectToRoute('app_commission_detail', $params);

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
