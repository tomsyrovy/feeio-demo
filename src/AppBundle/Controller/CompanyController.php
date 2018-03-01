<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Company;
use AppBundle\Entity\Factory\RoleFactory;
use AppBundle\Entity\Factory\UserCompanyTemporalityFactory;
use AppBundle\Entity\Role;
use AppBundle\Entity\UserCompany;
use AppBundle\Entity\UserCompanyTemporality;
use AppBundle\Form\Type\CompanyFormType;
use AppBundle\Form\Type\RoleFormType;
use AppBundle\Form\Type\UserCompanyTemporalityFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;
use TableBundle\DependencyInjection\TableData;

class CompanyController extends BaseController
{
    /**
     * @Route("/company/list/")
     * @Template()
     */
    public function listAction()
    {

        $ownedCompanies = $this->getUser()->getOwnedCompanies();

        $data = array(
            "ownedCompanies" => $ownedCompanies,
        );

        return $data;
    }

    /**
     *
     * @Route("/company/{company_id}/detail/")
     * @Template()
     */
    public function detailAction($company_id)
    {

        $company = $this->getCompany($company_id);

        if(!$company){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte přístup pro zobrazení jejího detailu.");

            return $this->redirectToRoute("app_company_list");

        }

        $data = array(
            "company" => $company,
        );

        return $data;
    }


    /**
     * @Route("/company/create/")
     * @Template()
     */
    public function createAction(Request $request)
    {

        $company = new Company();

        $form = $this->getCompanyForm($company, $this->generateUrl("app_company_create"));

        $form->handleRequest($request);

        if($form->isValid()){

            $em = $this->getDoctrine()->getManager();

            $now = new \DateTime();
            $owner = $this->getUser();

            //Tvorba společnosti
            $company->setCreated($now);
            $company->setOwner($owner);
            $owner->addOwnedCompany($company);

            //Tvorba rolí
            $roleFactory = new RoleFactory();
            $defaultAuthorizations = $em->getRepository("AppBundle:Authorization")->findAll();
            $role1 = $roleFactory->createRole($em, "Vlastník (superadmin)", $company, true, $defaultAuthorizations);
            $role2 = $roleFactory->createRole($em, "Člen společnosti", $company, false, array());

            //Tvorba první vazby uživatel-společnost
            $userCompany = new UserCompany();
            $userCompany->setCreated($now);
            $userCompany->setUser($owner);
            $userCompany->setCompany($company);

            $criteria = array(
                'code' => 'enabled',
            );
            $status = $em->getRepository('AppBundle:UserCompanyTemporalityStatus')->findOneBy($criteria);
            $userCompanyTemporalityFactory = new UserCompanyTemporalityFactory();
            $uct = $userCompanyTemporalityFactory->createUserCompanyTemporality($userCompany, $role1, $status, 0, 0, 0);


            $em->persist($owner);
            $em->persist($company);
            $em->persist($role1);
            $em->persist($role2);
            $em->persist($userCompany);
            $em->persist($uct);
            $em->flush();

            $this->addFlash("success", "Společnost byla vytvořena.");

            return $this->redirectToRoute("app_company_list");

        }

        $data = array(
            "form" => $form->createView()
        );

        return $data;
    }

    /**
     * @Route("/company/{company_id}/update/")
     * @Template()
     */
    public function updateAction($company_id, Request $request){

        $company = $this->getCompany($company_id);

        if(!$company){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte přístup pro zobrazení jejího detailu.");

            return $this->redirectToRoute("app_company_list");

        }

        $form = $this->getCompanyForm($company, $this->generateUrl("app_company_update", array("company_id" => $company->getId())));

        $form->handleRequest($request);

        if($form->isValid()){

            $now = new \DateTime();

            $company->setUpdated($now);

            $em = $this->getDoctrine()->getManager();

            $em->persist($company);
            $em->flush();

            $this->addFlash("success", "Společnost byla upravena.");

            return $this->redirectToRoute("app_company_detail", array("company_id" => $company->getId()));

        }

        $data = array(
            "company" => $company,
            "form" => $form->createView()
        );

        return $data;

    }

//    /**
//     * @Route("/company/{company_id}/change-status/{status}/")
//     */
//    public function changeStatusAction($company_id, $status){
//
//        $company = $this->getCompany($company_id);
//
//        if(!$company){
//
//            $this->addFlash("danger", "Společnost neexistuje nebo nemáte přístup pro zobrazení jejího detailu.");
//
//            return $this->redirectToRoute("app_company_list");
//
//        }
//
//        if($status == 0 OR $status == 1){
//
//            $now = new \DateTime();
//
//            if($status == 0){
//
//                //TODO - může být společnost deaktivována?
//
//                $status = false;
//                $text = "deaktivována";
//
//            }else{
//
//                $status = true;
//                $text = "aktivována";
//
//            }
//
//            $status = $status == 0 ? false : true;
//
//            $company->setUpdated($now);
//            $company->setEnabled($status);
//
//            $em = $this->getDoctrine()->getManager();
//
//            $em->persist($company);
//            $em->flush();
//
//            $this->addFlash("success", "Společnost byla ".$text.".");
//
//        }
//
//        return $this->redirectToRoute("app_company_detail", array("company_id" => $company->getId()));
//
//    }

    /**
     * @Route("/company/{company_id}/user-company/{userCompany_id}/detail/")
     * @Template()
     */
    public function companyUserCompanyDetailAction($company_id, $userCompany_id){

        $em = $this->getDoctrine()->getManager();

        $userCompany = $em->getRepository("AppBundle:UserCompany")->find($userCompany_id);

        $company = $this->getCompany($company_id);

        if(!$company OR $userCompany->getCompany() != $company){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte přístup pro zobrazení jejího detailu.");

            return $this->redirectToRoute("app_company_list");

        }

        $data = array(
            "company" => $company,
            "userCompany" => $userCompany,
        );

        return $data;

    }

    /**
     * @Route("/company/{company_id}/user-company/{userCompany_id}/timesheets/")
     * @Template()
     */
    public function companyUserCompanyTimesheetAction($company_id, $userCompany_id){

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $userCompany = $em->getRepository("AppBundle:UserCompany")->find($userCompany_id);

        $company = $this->getCompany($company_id);

        if(!$company OR $userCompany->getCompany() != $company){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte přístup pro zobrazení jejího detailu.");

            return $this->redirectToRoute("app_company_list");

        }

        $timesheets = $em->getRepository('AppBundle:Timesheet')->findByUserCompany($userCompany);

        $tableData = TableData::getData($em, $user, 'table-companyusercompanytimesheetlist');

        $data = array(
            'timesheets' => $timesheets,
            "company" => $company,
            "userCompany" => $userCompany,
            'tableData' => $tableData,
        );

        return $data;

    }

    /**
     * @Route("/company/{company_id}/user-company/{userCompany_id}/timesheets/{year}/{month}/")
     * @Template()
     */
    public function companyUserCompanyTimesheetYearmonthAction($company_id, $userCompany_id, $year, $month){

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $userCompany = $em->getRepository("AppBundle:UserCompany")->find($userCompany_id);

        $company = $this->getCompany($company_id);

        $criteria = array(
            'year' => $year,
            'month' => $month
        );
        $yearmonth = $em->getRepository('AppBundle:YearMonth')->findOneBy($criteria);

        if(!$company OR $userCompany->getCompany() != $company OR !$yearmonth){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte přístup pro zobrazení jejího detailu.");

            return $this->redirectToRoute("app_company_list");

        }

        $timesheets = $em->getRepository('AppBundle:Timesheet')->findByUserCompanyAndYearmonth($userCompany, $yearmonth);

        $tableData = TableData::getData($em, $user, 'table-companyusercompanytimesheetlistyearmonth');

        $data = array(
            'timesheets' => $timesheets,
            "company" => $company,
            "userCompany" => $userCompany,
            'tableData' => $tableData,
            'yearmonth' => $yearmonth,
        );

        return $data;

    }

    /**
     * @Route("/company/{company_id}/user-company/{userCompany_id}/update/")
     * @Template()
     */
    public function companyUserCompanyUpdateAction($company_id, $userCompany_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $userCompany = $em->getRepository("AppBundle:UserCompany")->find($userCompany_id);

        $company = $this->getCompany($company_id);

        if(!$company OR $userCompany->getCompany() != $company){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte přístup pro zobrazení jejího detailu.");

            return $this->redirectToRoute("app_company_list");

        }

        $actualTemporality = $userCompany->getData();

        //We must clone old (actual) data to helper variable to secure its overwriting
        $oldTemporality = clone($actualTemporality);

        $form = $this->createForm(new UserCompanyTemporalityFormType($company), $oldTemporality);

        $form->handleRequest($request);

        if($form->isValid()){

            //If there is change against oldTemporality
            if(
                $actualTemporality->getHours() != $oldTemporality->getHours() OR
                $actualTemporality->getRateInternal() != $oldTemporality->getRateInternal() OR
                $actualTemporality->getRole() != $oldTemporality->getRole() OR
                $actualTemporality->getStatus() != $oldTemporality->getStatus() OR
                $actualTemporality->getJobposition() != $oldTemporality->getJobposition()
                //TODO - můžu změnit stav povoleného uživatele?
            ){
                $userCompanyTemporalityFactory = new UserCompanyTemporalityFactory();
                $userCompanyTemporality = $userCompanyTemporalityFactory->createUserCompanyTemporality($oldTemporality->getUserCompany(), $oldTemporality->getRole(), $oldTemporality->getStatus(), $oldTemporality->getHours(), $oldTemporality->getRateInternal());

                $actualTemporality->setUntil(new \DateTime());

                $em->persist($actualTemporality);
                $em->persist($userCompanyTemporality);

                $em->flush();

                $this->addFlash("success", "Změny byly provedeny.");
                return $this->redirectToRoute("app_company_companyusercompanydetail", array("company_id" => $company->getId(), "userCompany_id" => $userCompany->getId()));

            }
        }

        $data = array(
            "company" => $company,
            "userCompany" => $userCompany,
            "form" => $form->createView(),
        );

        return $data;

    }

    /**
     * @Route("/company/{company_id}/roles/")
     * @Template()
     */
    public function companyRolesAction($company_id, Request $request){

        $company = $this->getCompany($company_id);

        if(!$company){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte přístup pro zobrazení jejího detailu.");

            return $this->redirectToRoute("app_company_list");

        }

        $em = $this->getDoctrine()->getManager();

        $criteria = array(
            "company" => $company,
            "enabled" => true,
        );
        $roles = $em->getRepository("AppBundle:Role")->findBy($criteria);
        $authorizations = $em->getRepository("AppBundle:Authorization")->findAll();

        $formData = $request->get("form");
        if($formData){
            $this->proccessForm($formData, $roles, $authorizations, $em);
            $this->addFlash("success", "Oprávnění bylo uloženo.");
            return $this->redirectToRoute("app_company_companyroles", array("company_id" => $company->getId()));
        }

        $form = $this->getRoleAuthorizationForm($roles);

        $data = array(
            "company" => $company,
            "form" => $form->createView(),
            "roles" => $roles,
            "authorizations" => $authorizations
        );

        return $data;

    }

    /**
     * @Route("/company/{company_id}/role/create/")
     * @Template()
     */
    public function companyRoleCreateAction($company_id, Request $request) {

        $company = $this->getCompany($company_id);

        if(!$company){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte přístup pro zobrazení jejího detailu.");

            return $this->redirectToRoute("app_company_list");

        }

        $role = new Role();

        $form = $this->getRoleForm($role, $this->generateUrl("app_company_companyrolecreate", array("company_id" => $company->getId())));

        $form->handleRequest($request);

        if($form->isValid()){

            $em = $this->getDoctrine()->getManager();

            $roleFactory = new RoleFactory();
            $role = $roleFactory->createRole($em, $role->getName(), $company, false, array());

            $em->persist($role);
            $em->flush();

            $this->addFlash("success", "Role byla ve společnosti vytvořena.");

            return $this->redirectToRoute("app_company_companyroles", array("company_id" => $company->getId()));

        }

        $data = array(
            "company" => $company,
            "form" => $form->createView()
        );

        return $data;

    }

    /**
     * @Route("/company/{company_id}/role/{role_id}/update/")
     * @Template()
     */
    public function companyRoleUpdateAction($company_id, $role_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $role = $em->getRepository("AppBundle:Role")->find($role_id);

        $company = $this->getCompany($company_id);

        if(!$company OR $role->getCompany() != $company){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte přístup pro zobrazení jejího detailu.");

            return $this->redirectToRoute("app_company_list");

        }

        $form = $this->getRoleForm($role, $this->generateUrl("app_company_companyroleupdate", array("company_id" => $company->getId(), "role_id" => $role->getId())));

        $form->handleRequest($request);

        if($form->isValid()){

            $now = new \DateTime();

            $role->setUpdated($now);

            $em->persist($role);
            $em->flush();

            $this->addFlash("success", "Název role byl upraven.");

            return $this->redirectToRoute("app_company_companyroles", array("company_id" => $company->getId()));

        }

        $data = array(
            "company" => $company,
            "form" => $form->createView()
        );

        return $data;

    }

    /**
     * @Route("/company/{company_id}/role/{role_id}/delete/")
     * @Template()
     */
    public function companyRoleDeleteAction($company_id, $role_id){

        $em = $this->getDoctrine()->getManager();

        $role = $em->getRepository("AppBundle:Role")->find($role_id);

        $company = $this->getCompany($company_id);

        if(!$company OR $role->getCompany() != $company){

            $this->addFlash("danger", "Společnost neexistuje nebo nemáte přístup pro zobrazení jejího detailu.");

            return $this->redirectToRoute("app_company_list");

        }

        if($role->getActualTemporalities()->count() != 0 OR $role->getNoneditable() == true){

            if($role->getNoneditable() == true){
                $this->addFlash("danger", "Role nemůže být odstraněna.");
            }else{
                $this->addFlash("danger", "Role nemůže být odstraněna, protože někteří uživatelé společnosti mají tuto roli přiřazenou.");
            }

        }else{

            $role->setUpdated(new \DateTime());
            $role->setEnabled(false);
            $em->flush();
            $this->addFlash("success", "Role byla odstraněna.");

        }

        return $this->redirectToRoute("app_company_companyroles", array("company_id" => $company->getId()));

    }



//== PRIVATE METHODS =================================================================================================//

    /**
     * Return RoleAuthorization Matrix form
     *
     * @param $roles
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getRoleAuthorizationForm($roles){

        $formBuilder = $this->createFormBuilder();

        foreach($roles as $role){
            foreach($role->getRoleAuthorizationRelations() as $roleAuthorizationRelation){

                $formBuilder->add($role->getId()."_".$roleAuthorizationRelation->getAuthorization()->getId(), "checkbox", array(
                    "label" => $role->getName()." - ".$roleAuthorizationRelation->getAuthorization()->getName(),
                    "data" => $roleAuthorizationRelation->getEnabled(),
                    "disabled" => $role->getNoneditable() ? true : false,
                    "required" => false,
                ));
            }
        }

        $formBuilder->add("submit", "submit", array(
            "label" => "Uložit",
            "attr" => array(
                "class" => "btn btn-primary"
            )
        ));

        return $formBuilder->getForm();

    }

    /**
     * Proccess RoleAuthorization Matrix Form Based on SelectedData
     *
     * @param                             $formData
     * @param                             $roles
     * @param                             $authorizations
     * @param \Doctrine\ORM\EntityManager $em
     */
    private function proccessForm($formData, $roles, $authorizations, EntityManager $em){

        foreach($roles as $role){

            if($role->getNoneditable() != true){

                $criteria = array(
                    "role" => $role,
                );
                $roleAuthorizations = $em->getRepository("AppBundle:RoleAuthorization")->findBy($criteria);

                foreach($roleAuthorizations as $roleAuthorization){

                    $roleAuthorization->setEnabled(false);

                    $key = $role->getId()."_".$roleAuthorization->getAuthorization()->getId();
                    if(array_key_exists($key, $formData)){
                        if($formData[$key] == 1){
                            $roleAuthorization->setEnabled(true);
                        }
                    }

                    $em->persist($roleAuthorization);

                }
            }
        }

        $em->flush();

    }

    /**
     * Return instance of form with defined instance of Company
     *
     * @param Company $company
     * @param string $action
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getCompanyForm($company, $action){

        $companyFormType = new CompanyFormType();

        $form = $this->createForm($companyFormType, $company, array(
            "method" => "post",
            "action" => $action,
        ));

        return $form;

    }

    private function getRoleForm(Role $role, $action){

        $roleFormType = new RoleFormType();

        $form = $this->createForm($roleFormType, $role, array(
            "method" => "post",
            "action" => $action,
        ));

        return $form;
    }

}
