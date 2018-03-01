<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use UserBundle\Tool\Vokativ;

class DashboardController extends BaseController
{
    /**
     * @Route("/dashboard/")
     * @Template()
     */
    public function indexAction(){

        if(!$this->checkLogin()){
            return $this->redirect($this->generateUrl("fos_user_security_login"));
        }

        return $this->redirectToRoute('app_timesheet_list');

//        $name = $this->getUser()->getFirstname();
//
//        $data = array(
//            "name" => $name,
//            "vokativ" => Vokativ::getVokativ($name),
//        );
//
//        return $data;
    }
}
