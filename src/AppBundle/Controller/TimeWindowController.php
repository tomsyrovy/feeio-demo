<?php

namespace AppBundle\Controller;

use AppBundle\DependencyInjection\TimeWindowManager;
use AppBundle\Form\Type\TimeWindowFormType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;
use UserBundle\Entity\User;

/**
 * TimeWindow controller.
 *
 */
class TimeWindowController extends Controller
{

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var User
     */
    private $user;

    /**
     * @var TimeWindowManager
     */
    private $timeWindowManager;


    private function init(){

        $this->em = $this->getDoctrine()->getManager();
        $this->user = $this->getUser();
        $this->timeWindowManager = new TimeWindowManager($this->em, $this->user);

    }

    /**
     * @Route("/timewindow/index/")
     * @Template()
     */
    public function indexAction()
    {

        $this->init();

        $timeWindow = $this->timeWindowManager->getTimeWindow();

        $timeWindowType = new TimeWindowFormType($timeWindow, 37);

        $form = $this->createForm($timeWindowType, $timeWindow, array(
            'action' => $this->generateUrl('app_timewindow_submitted'),
        ));

        $isDefault = $this->timeWindowManager->isDefaultTimeWindow();

        $data = array(
            'form' => $form->createView(),
            'isDefault' => $isDefault,
        );

        return $data;

    }

    /**
     * @Route("/timewindow/submitted/")
     * @Method("POST")
     */
    public function submittedAction(Request $request){

        $this->init();

        $timeWindow = $this->timeWindowManager->getTimeWindow();
        $timeWindowType = new TimeWindowFormType($timeWindow, 37);

        $form = $this->createForm($timeWindowType, $timeWindow, array(
            'action' => $this->generateUrl('app_timewindow_submitted'),
        ));

        $form->handleRequest($request);

        if($form->isValid()){

        	if($timeWindow->getYearmonthFrom()->getId() > $timeWindow->getYearmonthTo()->getId()){
		        $this->addFlash('danger', 'Časové období nebylo úspěšně uloženo.');
	        }else{
		        $this->em->persist($timeWindow);

		        $this->em->flush();

		        $this->addFlash('success', 'Časové období bylo úspěšně uloženo.');
	        }

        }else{

            $this->addFlash('danger', 'Časové období nebylo úspěšně uloženo.');

        }

        return $this->redirect($request->headers->get('referer'));

    }

    /**
     * @Route("/timewindow/reset/")
     */
    public function resetAction(Request $request){

        $this->init();

        $timeWindow = $this->timeWindowManager->setDefaultTimeWindow();

        $this->em->persist($timeWindow);

        $this->em->flush();

        $this->addFlash('success', 'Časové období bylo resetováno.');

        return $this->redirect($request->headers->get('referer'));

    }
}
