<?php

namespace AppBundle\Controller;

use AppBundle\DependencyInjection\Invitator\InvitatorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Entity\User;

class DefaultController extends BaseController
{
    /**
     * @Route("/")
     */
    public function indexAction(){

        if(!$this->checkLogin()){
            return $this->redirect($this->generateUrl("fos_user_security_login"));
        }else{
            if(!$this->checkUserCredentials()){
                return $this->redirect($this->generateUrl("app_profile_create"));
            }else{

                if($this->getUser()->getAdmin()){
                    return $this->redirectToRoute('app_admin_userlist');
                }

                $invitatorService = new InvitatorService($this);
                $invitations = $invitatorService->getInvitationsByUser($this->getUser());
                if(count($invitations) > 0){
                    return $this->redirectToRoute("app_profile_invitations");
                }else{
                    return $this->redirect($this->generateUrl("app_timesheet_list")); //TODO - změnit
                }
            }
        }
    }

}
