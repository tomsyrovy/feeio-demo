<?php

namespace AppBundle\Controller;

use AppBundle\DataObject\ActivityDataObject;
use AppBundle\Entity\Activity;
use AppBundle\Entity\JobPosition;
use AppBundle\Form\Type\ActivityFormType;
use AppBundle\Form\Type\JobPositionFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class JobPositionController extends BaseController
{

    /**
     * @Route("/company/{company_id}/job-positions/")
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
            'company' => $company,
            'enabled' => true
        );
        $orderBy = [
            'name' => 'ASC'
        ];
        $jobPositions = $em->getRepository('AppBundle:JobPosition')->findBy($criteria, $orderBy);

        $data = [
            'jobPositions' => $jobPositions,
            'company' => $company,
        ];

        return $data;

    }

    /**
     * @Route("/company/{company_id}/job-position/create/")
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

        $entity = new JobPosition();

        $form = $this->getForm($entity);

        $form->handleRequest($request);

        if($form->isValid()){

            $entity->setCompany($company);

            $em->persist($entity);

            $em->flush();

            $this->addFlash('success', 'Pracovní pozice byla úspěšně vytvořena.');
            $params = [
                'company_id' => $company_id
            ];
            return $this->redirectToRoute('app_jobposition_list', $params);

        }

        $data = [
            'form' => $form->createView(),
            'company' => $company,
        ];

        return $data;

    }

    /**
     * @Route("/company/{company_id}/job-position/{jobposition_id}/update/")
     * @Template()
     */
    public function updateAction($company_id, $jobposition_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $company = $this->getCompany($company_id, true);
        $user = $this->getUser();

        if(!$company OR $company->getOwner() !== $user){
            $this->addFlash('danger', 'Tato společnost neexistuje, nebo nejste jejím vlastníkem.');
            return $this->redirectToRoute('app_default_index');
        }

        $entity = $em->getRepository('AppBundle:JobPosition')->find($jobposition_id);

        if(!$entity OR $entity->getCompany() !== $company){
            $this->addFlash('danger', 'Tato pracovní pozice neexistuje, nebo ji nemůžete upravovat.');
            $params = [
                'company_id' => $company_id
            ];
            return $this->redirectToRoute('app_jobposition_list', $params);
        }

        $form = $this->getForm($entity);

        $form->handleRequest($request);

        if($form->isValid()){

            $entity->setCompany($company);

            $em->persist($entity);

            $em->flush();

            $this->addFlash('success', 'Pracovní pozice byla úspěšně upravena.');
            $params = [
                'company_id' => $company_id
            ];
            return $this->redirectToRoute('app_jobposition_list', $params);

        }

        $data = [
            'form' => $form->createView(),
            'company' => $company,
        ];

        return $data;

    }

    /**
     * @Route("/company/{company_id}/job-position/{jobposition_id}/delete/")
     */
    public function deleteAction($company_id, $jobposition_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $company = $this->getCompany($company_id, true);
        $user = $this->getUser();

        if(!$company OR $company->getOwner() !== $user){
            $this->addFlash('danger', 'Tato společnost neexistuje, nebo nejste jejím vlastníkem.');
            return $this->redirectToRoute('app_default_index');
        }

        $entity = $em->getRepository('AppBundle:JobPosition')->find($jobposition_id);

        if(!$entity OR $entity->getCompany() !== $company){
            $this->addFlash('danger', 'Tato aktivita neexistuje, nebo ji nemůžete upravovat.');
            $params = [
                'company_id' => $company_id
            ];
            return $this->redirectToRoute('app_jobposition_list', $params);
        }

        //Check if jp can be deleted
        if($entity->canBeRemoved()){

            //and delete it
            $entity->setEnabled(false);
            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'Pracovní pozice byla odstraněna.');

        }else{

            //or not
            $this->addFlash('danger', 'Tato pracovní pozice nemůže být odstraněna, protože je používána.');

        }

        $params = [
            'company_id' => $company_id
        ];
        return $this->redirectToRoute('app_jobposition_list', $params);

    }

    /**
     * @Route("/api/get/job-position")
     */
    public function getAction(Request $request){

        $entity = [];

        $em = $this->getDoctrine()->getManager();

        $company_id = $request->request->get('company_id');
        $jobposition_id = $request->request->get('jobposition_id');

        $company = $this->getCompany($company_id, true);
        $user = $this->getUser();

        if(!$company OR $company->getOwner() !== $user){
            $code = 404;
            $message = 'Tato společnost neexistuje, nebo nejste jejím vlastníkem.';
        }else{
            $entity = $em->getRepository('AppBundle:JobPosition')->find($jobposition_id);

            if(!$entity){
                $code = 404;
                $message = 'Tato společnost neexistuje, nebo nejste jejím vlastníkem.';
            }else{
                $code = 200;
                $message = '';
                $entity = [
                    'internalRate' => $entity->getInternalRate(),
                    'externalRate' => $entity->getExternalRate()
                ];
            }
        }

        $data = [
            'code' => $code,
            'message' => $message,
            'entity' => $entity
        ];

        return new JsonResponse($data, 200);

    }

    /**
     * @param \AppBundle\Entity\JobPosition $entity
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getForm(JobPosition $entity){

        $type = new JobPositionFormType();

        $form = $this->createForm($type, $entity);

        return $form;

    }

}
