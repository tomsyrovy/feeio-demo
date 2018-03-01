<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseController extends Controller
{
    /**
     * Check if user is logged in
     *
     * @return bool
     */
    protected function checkLogin(){
        return $this->getUser() != null;
    }

    /**
     * Check if user has filled firstname and lastname
     *
     * @return bool
     */
    protected function checkUserCredentials(){

        $firstname = $this->getUser()->getFirstname();
        $lastname = $this->getUser()->getLastname();

        return !(empty($firstname) OR empty($lastname));
    }

    /**
     * Return company by defined ID and enable status
     *
     * @param int $company_id
     * @param boolean $enabled
     *
     * @return mixed
     */
    protected function getCompany($company_id, $enabled = null){

        if($enabled == null){
            $criteria = array(
                "id" => $company_id,
            );
        }else{
            $criteria = array(
                "id" => $company_id,
                "enabled" => $enabled,
            );
        }

        //TODO - uživatel má oprávnění ke správě společnosti

        $company = $this->getDoctrine()->getManager()->getRepository("AppBundle:Company")->findOneBy($criteria);

        return $company;

    }

}
