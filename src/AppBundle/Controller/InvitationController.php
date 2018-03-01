<?php

namespace AppBundle\Controller;

use AppBundle\DependencyInjection\Invitator\Invitator;
use AppBundle\Entity\Company;
use AppBundle\Entity\Factory\UserCompanyTemporalityFactory;
use AppBundle\Entity\Invitation;
use AppBundle\Entity\UserCompany;
use AppBundle\Form\Type\InvitationFormType;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class InvitationController extends BaseController
{
    /**
     * @Route("company/{company_id}/invitation/")
     * @Template()
     */
    public function createAction($company_id, Request $request)
    {

        $company = $this->getCompany($company_id);

        if(!$company){

            $this->addFlash("danger", "Společnost neexistuje.");

            return $this->redirectToRoute("app_default_index");

        }

        $invitation = new Invitation();
        $form = $this->createInvitationForm($invitation, $company);

        $form->handleRequest($request);

        if($form->isValid()){

            $em = $this->getDoctrine()->getManager();

            //TODO - kontrolu uživatele ve společnosti by měl dělat invitator
            if(!$this->isUserWithEmailInCompany($em, $invitation->getEmail(), $company)){

                $invitation->setCreated(new \DateTime());
                $invitation->setAuthor($this->getUser());
                $invitation->setCompany($company);
                $invitation->setAccepted(false);
                $invitation->setEnabled(true);

                $invitator = new Invitator($this, $invitation);
                $invitatorResult = $invitator->invite();

                switch($invitatorResult->getText()){
                    case "EMAIL_HAS_BEEN_SENT" : {
                        $type = "success";
                        $text = "Uživatel byl pozván do společnosti prostřednictvím e-mailu.";
                    }break;
                    case "USER_CREATED_EMAIL_HAS_BEEN_SENT" : {
                        $type = "success";
                        $text = "Uživatel byl pozván do aplikace a do společnosti prostřednictvím e-mailu.";
                    }break;
                    case "USER_INVITED" : {
                        $type = "danger";
                        $text = "Uživateli již pozvánka do společnosti byla zaslána.";
                    }break;
                }

                if($invitatorResult->getType() == "success"){
                    $em->persist($invitation);
                    $em->flush();
                }

            }else{

                $type = "danger";
                $text = "Tento uživatel se ve společnosti již nachází.";

            }

            $this->addFlash($type, $text);
            return $this->redirectToRoute("app_company_detail", array(
                "company_id" => $company->getId(),
            ));

        }else{

            $data = array(
                "company" => $company,
                "form" => $form->createView(),
            );

            return $data;

        }

    }

    /**
     * @Route("invitation/{invitation_id}/submitted/{respond_type}/")
     */
    public function invitationRespondAction($invitation_id, $respond_type){

        $em = $this->getDoctrine()->getManager();

        $criteria = [
            'enabled' => true,
            'id' => $invitation_id,
        ];

        $invitation = $em->getRepository("AppBundle:Invitation")->findOneBy($criteria);

        if(!$invitation){
            return $this->redirectToRoute("app_default_index");
        }

        switch($respond_type){
            case "accepted" : {

                $userCompany = new UserCompany();
                $userCompany->setUser($this->getUser());
                $userCompany->setCompany($invitation->getCompany());
                $userCompany->setCreated(new \DateTime());
                $userCompanyTemporalityFactory = new UserCompanyTemporalityFactory();
                $status = $em->getRepository("AppBundle:UserCompanyTemporalityStatus")->findOneBy(array("code" => "enabled"));
                $userCompanyTemporality = $userCompanyTemporalityFactory->createUserCompanyTemporality($userCompany, $invitation->getRole(), $status, $invitation->getHours(), $invitation->getRateInternal());

                $invitation->setEnabled(false);
                $invitation->setAccepted(true);

                $em->persist($userCompany);
                $em->persist($userCompanyTemporality);
                $em->persist($invitation);
                $em->flush();

                $this->addFlash("success", "Pozvánka byla přijata.");

            }break;
            case "rejected" : {

                $invitation->setEnabled(false);
                $invitation->setAccepted(false);

                $em->persist($invitation);
                $em->flush();

                $this->addFlash("success", "Pozvánka nebyla přijata.");

            }break;
        }

        return $this->redirectToRoute("app_default_index");

    }

//== PRIVATE METHODS =================================================================================================//

    private function createInvitationForm(Invitation $invitation, Company $company){

        $form = $this->createForm(new InvitationFormType($company), $invitation, array(
            "action" => $this->generateUrl("app_invitation_create", array(
                "company_id" => $company->getId(),
            )),
            "method" => "post"
        ));

        return $form;

    }

    /**
     * Vrací true/false pokud uživatel zadaného e-mailu je již v zadané společnosti
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param                             $email
     * @param \AppBundle\Entity\Company   $company
     *
     * @return bool
     */
    private function isUserWithEmailInCompany(EntityManager $em, $email, Company $company){

        $criteria = array(
            "email" => $email
        );

        $user = $em->getRepository("UserBundle:User")->findOneBy($criteria);
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq("user", $user));

        return ($company->getUserCompanyRelations()->matching($criteria)->count() > 0);

    }

}
