<?php

namespace AppBundle\Controller;

use AppBundle\DependencyInjection\Authorization\AuthorizationChecker;
use AppBundle\Entity\CompanyGroup;
use AppBundle\Entity\CompanyGroupUserCompanyRelation;
use AppBundle\Entity\CompanyGroupUserCompanyRelationTemporality;
use AppBundle\Entity\UserCompany;
use AppBundle\Form\Model\CompanyGroupFormModel;
use AppBundle\Form\Type\CompanyGroupFormModelType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;

class CompanyGroupController extends BaseController
{
    /**
     * @Route("/company/{company_id}/group/create/")
     * @Template()
     */
    public function createAction($company_id, Request $request)
    {

        $company = $this->getCompany($company_id);

        if(!$company){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte oprávnění pro její zobrazení.");

            return $this->redirectToRoute("app_company_list");

        }

        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $authorizationChecker = new AuthorizationChecker($em);
        $canCompanyGroupCreate = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('companygroup-create', $user, $company);

        if(!$canCompanyGroupCreate){

            $this->addFlash("danger", "Ve společnosti ".$company->getName()." nemáte oprávnění zakládat skupiny.");

            return $this->redirectToRoute("app_company_list");

        }

        $companyPeople = $company->getUserCompanyRelationsOfTemporalityStatus('enabled');

        $companyGroupUserCompanyTemporalityTypes = $em->getRepository('AppBundle:CompanyGroupUserCompanyRelationTemporalityType')->findAll();

        $companyGroupFormModel = new CompanyGroupFormModel();

        $companyGroupForm = $this->getCompanyGroupForm($companyGroupFormModel);

        $companyGroupForm->handleRequest($request);

        if($companyGroupForm->isValid()){

            $companyGroup = new CompanyGroup();
            $companyGroup->setOwner($user);
            $companyGroup->setCreated(new \DateTime());
            $companyGroup->setEnabled(true);
            $companyGroup->setCompany($company);
            $companyGroup->setName($companyGroupFormModel->getName());

            // TODO - vyčlenit do třídy CompanyGroupFormModel
            // === DEFINOVANI VAZEB a persist
            $companyGroupFormModelMemberRelationTypes = [];
            $companyGroupFormModelStringTypes = explode(';', $companyGroupFormModel->getMembers());
            foreach($companyGroupFormModelStringTypes as $companyGroupFormModelStringType){
                if(!empty($companyGroupFormModelStringType)){
                    $strings = explode(':', $companyGroupFormModelStringType);
                    $companyGroupFormModelMemberRelationTypes[$strings[0]] = explode('&', str_replace('userCompany[]=', '', $strings[1]));
                }
            }

            foreach($companyGroupFormModelMemberRelationTypes as $key => $companyGroupFormModelMemberRelationType ){

                $criteria = [
                    'code' => $key,
                ];
                $cgucrtt = $em->getRepository('AppBundle:CompanyGroupUserCompanyRelationTemporalityType')->findOneBy($criteria);

                if($cgucrtt){

                    foreach( $companyGroupFormModelMemberRelationType as $companyGroupFormModelMemberRelationMember ){

                        $userCompany = $em->getRepository( 'AppBundle:UserCompany' )->find( $companyGroupFormModelMemberRelationMember );

                        if( $userCompany and $userCompany->getData()->getStatus()->getCode() === 'enabled' ){

                            $companyGroupUserCompanyRelation = new CompanyGroupUserCompanyRelation();

                            $companyGroupUserCompanyRelation->setCompanyGroup( $companyGroup );
                            $companyGroupUserCompanyRelation->setUserCompany( $userCompany );
                            $userCompany->addCompanyGroupUserCompanyRelation($companyGroupUserCompanyRelation);
                            $companyGroup->addCompanyGroupUserCompanyRelation($companyGroupUserCompanyRelation);

                            $companyGroupUserCompanyRelationTemporality = new CompanyGroupUserCompanyRelationTemporality();

                            $companyGroupUserCompanyRelationTemporality->setCompanyGroupUserCompanyRelation( $companyGroupUserCompanyRelation );
                            $companyGroupUserCompanyRelation->addTemporality($companyGroupUserCompanyRelationTemporality);
                            $companyGroupUserCompanyRelationTemporality->setDateFrom( new \DateTime() );
                            $companyGroupUserCompanyRelationTemporality->setCompanyGroupUserCompanyRelationTemporalityType($cgucrtt);

                            $em->persist($userCompany);
                            $em->persist($companyGroupUserCompanyRelation);
                            $em->persist($companyGroupUserCompanyRelationTemporality);

                        }

                    }

                }

            }

            // == KONEC DEFINOVANI VAZEB

            $em->persist($companyGroup);
            $em->flush();

            $this->addFlash("success", "Skupina ve společnosti ".$company->getName()." byla úspěšně založena.");

            $parameters = array(
                'company_id' => $company->getId(),
            );
            return $this->redirectToRoute('app_company_detail', $parameters);

        }

        $data = array(
            "company" => $company,
            'companyPeople' => $companyPeople,
            'types' => $companyGroupUserCompanyTemporalityTypes,
            "form" => $companyGroupForm->createView(),
        );

        return $data;
    }

    /**
     * @Route("/company/{company_id}/group/{companygroup_id}/update/")
     * @Template()
     */
    public function updateAction($company_id, $companygroup_id, Request $request)
    {

        $company = $this->getCompany($company_id);

        if(!$company){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte oprávnění pro její zobrazení.");

            return $this->redirectToRoute("app_company_list");

        }

        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $authorizationChecker = new AuthorizationChecker($em);
        $canCompanyGroupUpdate = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('companygroup-update', $user, $company);

        if(!$canCompanyGroupUpdate){

            $this->addFlash("danger", "Ve společnosti ".$company->getName()." nemáte oprávnění editovat skupiny.");

            return $this->redirectToRoute("app_company_list");

        }

        $criteria = array(
            'company' => $company,
            'id' => $companygroup_id,
        );
        $companyGroup = $em->getRepository('AppBundle:CompanyGroup')->findOneBy($criteria);

        if(!$companyGroup){

            $this->addFlash("danger", "Skupina neexistuje nebo nemáte oprávnění pro její zobrazení.");

            return $this->redirectToRoute("app_company_list");

        }

        $companyPeople = $company->getUserCompanyRelationsOfTemporalityStatus('enabled');

        $companyGroupUserCompanyTemporalityTypes = $em->getRepository('AppBundle:CompanyGroupUserCompanyRelationTemporalityType')->findAll();

        $companyGroupFormModel = new CompanyGroupFormModel();
        $companyGroupFormModel->setDataFromCompanyGroup($companyGroup, $em);

        $companyGroupForm = $this->getCompanyGroupForm($companyGroupFormModel);

        $companyGroupForm->handleRequest($request);

        if($companyGroupForm->isValid()){

            $companyGroup->setUpdated(new \DateTime());
            $companyGroup->setName($companyGroupFormModel->getName());

            // TODO - vyčlenit do třídy CompanyGroupFormModel
            // === DEFINOVANI VAZEB a persist
            // TODO - nelíbí se mi, že všechny dosavadní temporality jsou deaktivovány, byť by nemusely
            $cgucrs = $companyGroup->getCompanyGroupUserCompanyRelationsOfEnabledTemporality();
            foreach($cgucrs as $cgucr){

                $cgucr->getData()->setDateUntil(new \DateTime());
                $em->persist($cgucr);

            }

            $companyGroupFormModelMemberRelationTypes = [];
            $companyGroupFormModelStringTypes = explode(';', $companyGroupFormModel->getMembers());
            foreach($companyGroupFormModelStringTypes as $companyGroupFormModelStringType){
                if(!empty($companyGroupFormModelStringType)){
                    $strings = explode(':', $companyGroupFormModelStringType);
                    $companyGroupFormModelMemberRelationTypes[$strings[0]] = explode('&', str_replace('userCompany[]=', '', $strings[1]));
                }
            }

            foreach($companyGroupFormModelMemberRelationTypes as $key => $companyGroupFormModelMemberRelationType ){

                $criteria = [
                    'code' => $key,
                ];
                $cgucrtt = $em->getRepository('AppBundle:CompanyGroupUserCompanyRelationTemporalityType')->findOneBy($criteria);

                if($cgucrtt){

                    foreach( $companyGroupFormModelMemberRelationType as $companyGroupFormModelMemberRelationMember ){

                        $userCompany = $em->getRepository( 'AppBundle:UserCompany' )->find( $companyGroupFormModelMemberRelationMember );

                        if( $userCompany and $userCompany->getData()->getStatus()->getCode() === 'enabled' ){

                            $criteria = [
                                'userCompany' => $userCompany,
                                'companyGroup' => $companyGroup,
                            ];
                            $companyGroupUserCompanyRelation = $em->getRepository("AppBundle:CompanyGroupUserCompanyRelation")->findOneBy($criteria);

                            if(!$companyGroupUserCompanyRelation){

                                $companyGroupUserCompanyRelation = new CompanyGroupUserCompanyRelation();
                                $companyGroupUserCompanyRelation->setCompanyGroup( $companyGroup );
                                $companyGroupUserCompanyRelation->setUserCompany( $userCompany );

                            }

                            $userCompany->addCompanyGroupUserCompanyRelation($companyGroupUserCompanyRelation);
                            $companyGroup->addCompanyGroupUserCompanyRelation($companyGroupUserCompanyRelation);

                            $companyGroupUserCompanyRelationTemporality = new CompanyGroupUserCompanyRelationTemporality();

                            $companyGroupUserCompanyRelationTemporality->setCompanyGroupUserCompanyRelation( $companyGroupUserCompanyRelation );
                            $companyGroupUserCompanyRelation->addTemporality($companyGroupUserCompanyRelationTemporality);
                            $companyGroupUserCompanyRelationTemporality->setDateFrom( new \DateTime() );
                            $companyGroupUserCompanyRelationTemporality->setCompanyGroupUserCompanyRelationTemporalityType($cgucrtt);

                            $em->persist($userCompany);
                            $em->persist($companyGroupUserCompanyRelation);
                            $em->persist($companyGroupUserCompanyRelationTemporality);

                        }

                    }

                }

            }
            // == KONEC DEFINOVANI VAZEB

            $em->persist($companyGroup);
            $em->flush();

            $this->addFlash("success", "Skupina ve společnosti ".$company->getName()." byla úspěšně založena.");

            $parameters = array(
                'company_id' => $company->getId(),
            );
            return $this->redirectToRoute('app_company_detail', $parameters);

        }

//        $companyGroupForm = $this->getCompanyGroupForm($em, $company, $companyGroup);
//
//        $companyGroupForm->handleRequest($request);
//
//        if($companyGroupForm->isValid()){
//
//            $companyGroup->setUpdated(new \DateTime());
//
//            $em->persist($companyGroup);
//            $em->flush();
//
//            $this->addFlash("success", "Skupina ve společnosti ".$company->getName()." byla úspěšně upravena.");
//
//            $parameters = array(
//                'company_id' => $company->getId(),
//            );
//            return $this->redirectToRoute('app_company_detail', $parameters);
//
//        }

        $data = array(
            "company" => $company,
            'companyPeople' => $companyPeople,
            'types' => $companyGroupUserCompanyTemporalityTypes,
            "form" => $companyGroupForm->createView(),
        );

        return $data;
    }

    /**
     * @Route("/company/{company_id}/group/{companygroup_id}/change-status/{status}/")
     */
    public function changeStatusAction($company_id, $companygroup_id, $status){

        $company = $this->getCompany($company_id);

        if(!$company){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte přístup pro zobrazení jejího detailu.");

            return $this->redirectToRoute("app_company_list");

        }

        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $criteria = array(
            'company' => $company,
            'id' => $companygroup_id,
        );
        $companyGroup = $em->getRepository('AppBundle:CompanyGroup')->findOneBy($criteria);

        if(!$companyGroup){

            $this->addFlash("danger", "Skupina neexistuje nebo nemáte oprávnění pro její zobrazení.");

            return $this->redirectToRoute("app_company_list");

        }

        $authorizationChecker = new AuthorizationChecker($em);
        $canCompanyGroupUpdate = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('companygroup-update', $user, $company);

        if(!$canCompanyGroupUpdate){

            $this->addFlash("danger", "Ve společnosti ".$company->getName()." nemáte oprávnění editovat skupiny.");

            return $this->redirectToRoute("app_company_list");

        }

        //Zde povinně musí být slabé porovnání
        if($status == 0 OR $status == 1){

            $now = new \DateTime();

            if($status == 0){
                $text = "deaktivována";

            }else{

                $text = "aktivována";

            }

            $status = $status == 0 ? false : true;

            $companyGroup->setUpdated($now);
            $companyGroup->setEnabled($status);

            $em->persist($companyGroup);
            $em->flush();

            $this->addFlash("success", "Skupina byla ".$text.".");

        }

        return $this->redirectToRoute("app_company_detail", array("company_id" => $company->getId()));

    }

    private function getCompanyGroupForm(CompanyGroupFormModel $companyGroupFormModel){

        $companyGroupFormType = new CompanyGroupFormModelType();

        $companyGroupForm = $this->createForm($companyGroupFormType, $companyGroupFormModel);

        return $companyGroupForm;

    }

}
