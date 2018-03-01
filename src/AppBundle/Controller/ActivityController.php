<?php

namespace AppBundle\Controller;

use AppBundle\DataObject\ActivityDataObject;
use AppBundle\Entity\Activity;
use AppBundle\Form\Type\ActivityFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class ActivityController extends BaseController
{

    /**
     * @Route("/company/{company_id}/activities/")
     * @Template()
     */
    public function listAction($company_id){

        $em = $this->getDoctrine()->getManager();

        $company = $this->getCompany($company_id, true);
        $user = $this->getUser();

        if(!$company OR $company->getOwner() !== $user){
            $this->addFlash('danger', 'Tato společnost neexistuje, nebo nejste jejím vlastníkem.');
            return $this->redirectToRoute('app_default_index');
        }

        $criteria = array(
            'company' => $company
        );
        $orderBy = [
            'name' => 'ASC'
        ];
        $activities = $em->getRepository('AppBundle:Activity')->findBy($criteria, $orderBy);

        $activityDOs = [];
        foreach($activities as $activity){
            $activityDOs[] = new ActivityDataObject($activity);
        }

        $data = [
            'activityDOs' => $activityDOs,
            'company' => $company,
        ];

        return $data;

    }

    /**
     * @Route("/company/{company_id}/activity/create/")
     * @Template()
     */
    public function createAction($company_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $company = $this->getCompany($company_id, true);
        $user = $this->getUser();

        if(!$company OR $company->getOwner() !== $user){
            $this->addFlash('danger', 'Tato společnost neexistuje, nebo nejste jejím vlastníkem.');
            return $this->redirectToRoute('app_default_index');
        }

        $entity = new Activity();

        $form = $this->getForm($entity);

        $form->handleRequest($request);

        if($form->isValid()){

            $entity->setCompany($company);

            $em->persist($entity);

            $em->flush();

            $this->addFlash('success', 'Aktivita byla úspěšně vytvořena.');
            $params = [
                'company_id' => $company_id
            ];
            return $this->redirectToRoute('app_activity_list', $params);

        }

        $data = [
            'form' => $form->createView(),
            'company' => $company,
        ];

        return $data;

    }

    /**
     * @Route("/company/{company_id}/activity/{activity_id}/update/")
     * @Template()
     */
    public function updateAction($company_id, $activity_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $company = $this->getCompany($company_id, true);
        $user = $this->getUser();

        if(!$company OR $company->getOwner() !== $user){
            $this->addFlash('danger', 'Tato společnost neexistuje, nebo nejste jejím vlastníkem.');
            return $this->redirectToRoute('app_default_index');
        }

        $entity = $em->getRepository('AppBundle:Activity')->find($activity_id);

        if(!$entity OR $entity->getCompany() !== $company){
            $this->addFlash('danger', 'Tato aktivita neexistuje, nebo ji nemůžete upravovat.');
            $params = [
                'company_id' => $company_id
            ];
            return $this->redirectToRoute('app_activity_list', $params);
        }

        $form = $this->getForm($entity);

        $form->handleRequest($request);

        if($form->isValid()){

            $entity->setCompany($company);

            $em->persist($entity);

            $em->flush();

            $this->addFlash('success', 'Aktivita byla úspěšně upravena.');
            $params = [
                'company_id' => $company_id
            ];
            return $this->redirectToRoute('app_activity_list', $params);

        }

        $data = [
            'form' => $form->createView(),
            'company' => $company,
        ];

        return $data;

    }

    /**
     * @Route("/company/{company_id}/activity/{activity_id}/delete/")
     */
    public function deleteAction($company_id, $activity_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $company = $this->getCompany($company_id, true);
        $user = $this->getUser();

        if(!$company OR $company->getOwner() !== $user){
            $this->addFlash('danger', 'Tato společnost neexistuje, nebo nejste jejím vlastníkem.');
            return $this->redirectToRoute('app_default_index');
        }

        $entity = $em->getRepository('AppBundle:Activity')->find($activity_id);

        if(!$entity OR $entity->getCompany() !== $company){
            $this->addFlash('danger', 'Tato aktivita neexistuje, nebo ji nemůžete upravovat.');
            $params = [
                'company_id' => $company_id
            ];
            return $this->redirectToRoute('app_activity_list', $params);
        }

        //Check if activity can be deleted
        if($entity->getTimesheets()->count() === 0){

            //and delete it
            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'Aktivita byla odstraněna.');

        }else{

            //or not
            $this->addFlash('danger', 'Tato aktivita nemůže být odstraněna, protože byla již použita v timesheetech.');

        }

        $params = [
            'company_id' => $company_id
        ];
        return $this->redirectToRoute('app_activity_list', $params);

    }

    /**
     * @param \AppBundle\Entity\Activity $entity
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getForm(Activity $entity){

        $type = new ActivityFormType();

        $form = $this->createForm($type, $entity);

        return $form;

    }

}
