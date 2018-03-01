<?php

namespace AppBundle\Controller;

use AppBundle\DataObject\SubcommissionListDataObject;
use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
use AppBundle\DependencyInjection\TimeWindowManager;
use AppBundle\DependencyInjection\YearmonthManager\YearmonthManager;
use AppBundle\Entity\Commission;
use AppBundle\Entity\Subcommission;
use AppBundle\Entity\SubcommissionTeam;
use AppBundle\Entity\SubcommissionTemporality;
use AppBundle\Form\Type\SubcommissionTeamFormType;
use AppBundle\Form\Type\SubcommissionTemporalityFormType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;

class SubcommissionController extends Controller
{
    /**
     * @Route("/commission/{commission_id}/subcommissions/")
     * @Template()
     */
    public function listAction($commission_id)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        if(!$commission){

            $this->addFlash('danger', 'Tato zakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        $cm = new CommissionManager($em);

        $m = array(
            'admin',
            'observer',
        );
        $check = $cm->checkCommissionUserCompanyManagedByRoleTypeCodes($user, $commission, 'enabled', $m);

        if(!$check){

            $this->addFlash('danger', 'Nemáte oprávnění spravovat tuto zakázku.');

            $params = array(
                'commission_id' => $commission->getId(),
            );
            return $this->redirectToRoute('app_commission_detail', $params);

        }

        $timeWindowManager = new TimeWindowManager($em, $user);
        $timeWindow = $timeWindowManager->getTimeWindow();

        $sldo = new SubcommissionListDataObject($em, $cm, $user, $commission);

        $subcommissionListData = $sldo->getData($timeWindow);

        $subcommissions = $subcommissionListData['subcommissions'];

        $lastSubcommission = $commission->getSubcommissions()->last();
        $canCleverDuplicate = false;
        foreach($subcommissions as $subcommission){
            if($lastSubcommission === $subcommission){
                $canCleverDuplicate = true;
            }
        }

        $data = array(
            'commission' => $commission,
            'canCleverDuplicate' => $canCleverDuplicate,
            'lastSubcommission' => $lastSubcommission,
        );

        $data = array_merge($data, $subcommissionListData);

        return $data;

    }

    /**
     * @Route("/commission/{commission_id}/subcommission/create/")
     * @Template()
     */
    public function createAction(Request $request, $commission_id){

        $user = $this->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        if(!$commission){

            $this->addFlash('danger', 'Tato zakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        $cm = new CommissionManager($em);

        $m = array(
            'admin',
        );
        $check = $cm->checkCommissionUserCompanyManagedByRoleTypeCodes($user, $commission, 'enabled', $m);

        if(!$check){

            $this->addFlash('danger', 'Nemáte oprávnění spravovat tuto zakázku.');

            $params = array(
                'commission_id' => $commission->getId(),
            );
            return $this->redirectToRoute('app_commission_detail', $params);

        }

        $yearmonthManager = new YearmonthManager($em);
        $yearmonths = $yearmonthManager->getCollectionOfThisAndNextYears(date('Y'), 1);
        $subcommissionTemporality = new SubcommissionTemporality();
        $type = new SubcommissionTemporalityFormType($yearmonths);

        $form = $this->createForm($type, $subcommissionTemporality);

        $form->handleRequest($request);

        if($form->isValid()){

            $subcommissionTemporality->getSubcommission()->setCommission($commission);
            $subcommissionTemporality->setDateFrom(new \DateTime());

            $em->persist($subcommissionTemporality);

            $em->flush();

            $this->addFlash('success', 'Subzakázka '.$subcommissionTemporality->getSubcommission()->getCode().' byla úspěšně vytvořena.');

            $params = array(
                'commission_id' => $commission->getId(),
            );
            return $this->redirectToRoute('app_subcommission_list', $params);

        }

        $data = array(
            'form' => $form->createView(),
            'commission' => $commission,
        );

        return $data;

    }

    /**
     * @Route("/commission/{commission_id}/subcommission/{subcommission_id}/update/")
     * @Template()
     */
    public function updateAction(Request $request, $commission_id, $subcommission_id){

        $user = $this->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        $subcommission = $em->getRepository('AppBundle:Subcommission')->find($subcommission_id);

        if(!$commission){

            $this->addFlash('danger', 'Tato zakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        if(!$subcommission){

            $this->addFlash('danger', 'Tato subzakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        $cm = new CommissionManager($em);

        $m = array(
            'admin',
        );
        $check = $cm->checkCommissionUserCompanyManagedByRoleTypeCodes($user, $commission, 'enabled', $m);

        if(!$check){

            $this->addFlash('danger', 'Nemáte oprávnění spravovat tuto zakázku.');

            $params = array(
                'commission_id' => $commission->getId(),
            );
            return $this->redirectToRoute('app_commission_detail', $params);

        }

        $yearmonthManager = new YearmonthManager($em);
        $yearmonths = $yearmonthManager->getCollectionOfThisAndNextYears(date('Y'), 1);
        $subcommissionTemporalityOLD = $subcommission->getData();
        $subcommissionTemporality = clone($subcommissionTemporalityOLD);
        $type = new SubcommissionTemporalityFormType($yearmonths);

        $form = $this->createForm($type, $subcommissionTemporality);

        $form->handleRequest($request);

        if($form->isValid()){

            $subcommissionTemporality->getSubcommission()->setCommission($commission);
            $subcommissionTemporality->setDateFrom(new \DateTime());
            $subcommissionTemporalityOLD->setDateUntil(new \DateTime());

            $em->persist($subcommissionTemporality);
            $em->persist($subcommissionTemporalityOLD);

            $em->flush();

            $this->addFlash('success', 'Subzakázka '.$subcommission->getCode().' byla úspěšně uložena.');

            $params = array(
                'commission_id' => $commission->getId(),
            );
            return $this->redirectToRoute('app_subcommission_list', $params);

        }

        $data = array(
            'form' => $form->createView(),
            'commission' => $commission,
            'subcommission' => $subcommission,
        );

        return $data;

    }

    /**
     * @Route("/commission/{commission_id}/subcommission/{subcommission_id}/delete/")
     */
    public function deleteAction($commission_id, $subcommission_id){

        $user = $this->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        $subcommission = $em->getRepository('AppBundle:Subcommission')->find($subcommission_id);

        if(!$commission){

            $this->addFlash('danger', 'Tato zakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        if(!$subcommission){

            $this->addFlash('danger', 'Tato subzakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        $cm = new CommissionManager($em);

        $m = array(
            'admin',
        );
        $check = $cm->checkCommissionUserCompanyManagedByRoleTypeCodes($user, $commission, 'enabled', $m);

        if(!$check){

            $this->addFlash('danger', 'Nemáte oprávnění spravovat tuto zakázku.');

            $params = array(
                'commission_id' => $commission->getId(),
            );
            return $this->redirectToRoute('app_commission_detail', $params);

        }

        if($subcommission->canRemove()){

            $subcommission->setEnabled(false);
            $em->flush();

            $this->addFlash('success', 'Subzakázka '.$subcommission->getCode().' byla odstraněna.');

        }else{

            $this->addFlash('danger', 'Subzakázka '.$subcommission->getCode().' nemůže být odstraněna.');

        }

        $params = array(
            'commission_id' => $commission->getId(),
        );
        return $this->redirectToRoute('app_subcommission_list', $params);

    }

    /**
     * @Route("/commission/{commission_id}/subcommission/{subcommission_id}/duplicate/")
     */
    public function duplicateAction($commission_id, $subcommission_id = 0){

        $user = $this->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        if(!$commission){

            $this->addFlash('danger', 'Tato zakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        $subcommission = $em->getRepository('AppBundle:Subcommission')->find($subcommission_id);

        if(!$subcommission){

            $this->addFlash('danger', 'Tato subzakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }


        $cm = new CommissionManager($em);

        $m = array(
            'admin',
        );
        $check = $cm->checkCommissionUserCompanyManagedByRoleTypeCodes($user, $commission, 'enabled', $m);

        if(!$check){

            $this->addFlash('danger', 'Nemáte oprávnění spravovat tuto zakázku.');

            $params = array(
                'commission_id' => $commission->getId(),
            );
            return $this->redirectToRoute('app_commission_detail', $params);

        }

        $subcommissions = $commission->getSubcommissions();
        $subcommissionLast = $subcommissions->last();

        $subcommissionNEW = $this->duplicateSubcommissionAndPersist($em, $subcommission, $subcommissionLast, $commission, 1);

        $this->addFlash('success', 'Subzakázka '.$subcommissionNEW->getCode().' byla úspěšně vytvořena.');

        $em->flush();

        $params = array(
            'commission_id' => $commission->getId(),
        );
        return $this->redirectToRoute('app_subcommission_list', $params);

    }

    /**
     * @Route("/commission/{commission_id}/subcommissions/clever-duplicate/{d}/")
     */
    public function duplicateCleverAction($commission_id, $d = 1){

        //TODO 500 - tato metoda nefunguje korektně duplikují-li se subzakázky se zakázkami, které byly odstraněny

        $user = $this->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        if(!$commission){

            $this->addFlash('danger', 'Tato zakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        $cm = new CommissionManager($em);

        $m = array(
            'admin',
        );
        $check = $cm->checkCommissionUserCompanyManagedByRoleTypeCodes($user, $commission, 'enabled', $m);

        if(!$check){

            $this->addFlash('danger', 'Nemáte oprávnění spravovat tuto zakázku.');

            $params = array(
                'commission_id' => $commission->getId(),
            );
            return $this->redirectToRoute('app_commission_detail', $params);

        }

        $subcommissions = $commission->getSubcommissions();
        $subcommissionsCount = $subcommissions->count();
        $subcommissionLast = $subcommissions->last();

        //Hack, aby se spravily klíče v poli subzakázek (http://stackoverflow.com/questions/5943149/rebase-array-keys-after-unsetting-elements)
        $subcommissions = $subcommissions->toArray();
        $subcommissions = array_values($subcommissions);

        $flashes = array();

        for($i = 1; $i <= $d; $i++){

            $index = $subcommissionsCount - $d + $i - 1;

            $subcommission = $subcommissions[$index];

            $subcommissionNEW = $this->duplicateSubcommissionAndPersist($em, $subcommission, $subcommissionLast, $commission, $i);

            $flashes[] = $subcommissionNEW->getCode();

        }

        $this->addFlash('success', 'Subzakázky '.implode(', ', $flashes).' byly chytře a úspěšně vytvořeny.');

        $em->flush();

        $params = array(
            'commission_id' => $commission->getId(),
        );
        return $this->redirectToRoute('app_subcommission_list', $params);

    }

    /**
     * @Route("/commission/{commission_id}/subcommission/{subcommission_id}/team/update/")
     * @Template()
     */
    public function teamupdateAction(Request $request, $commission_id, $subcommission_id){

        $user = $this->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        $subcommission = $em->getRepository('AppBundle:Subcommission')->find($subcommission_id);

        if(!$commission){

            $this->addFlash('danger', 'Tato zakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        if(!$subcommission){

            $this->addFlash('danger', 'Tato subzakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        $cm = new CommissionManager($em);

        $m = array(
            'admin',
        );
        $check = $cm->checkCommissionUserCompanyManagedByRoleTypeCodes($user, $commission, 'enabled', $m);

        if(!$check){

            $this->addFlash('danger', 'Nemáte oprávnění spravovat tuto zakázku.');

            $params = array(
                'commission_id' => $commission->getId(),
            );
            return $this->redirectToRoute('app_commission_detail', $params);

        }

        $type = new SubcommissionTeamFormType($subcommission);

        $subcommissionTeamOLD = $subcommission->getTeamData();

        if($subcommissionTeamOLD){
            $subcommissionTeam = clone($subcommissionTeamOLD);
        }else{
            $subcommissionTeam = new SubcommissionTeam();
        }

        $form = $this->createForm($type, $subcommissionTeam);

        $form->handleRequest($request);

        if($form->isValid()){

            $subcommissionTeam->setSubcommission($subcommission);
            $subcommissionTeam->setDateFrom(new \DateTime());
            $em->persist($subcommissionTeam);

            if($subcommissionTeamOLD){

                $subcommissionTeamOLD->setDateUntil(new \DateTime());
                $em->persist($subcommissionTeamOLD);

            }

            $em->flush();

            $this->addFlash('success', 'Tým pro subzakázku '.$subcommission->getCode().' byl úspěšně uložen.');

            $params = array(
                'commission_id' => $commission->getId(),
            );
            return $this->redirectToRoute('app_subcommission_list', $params);

        }

        $data = array(
            'form' => $form->createView(),
            'commission' => $commission,
            'subcommission' => $subcommission,
        );

        return $data;

    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param \AppBundle\Entity\Subcommission            $subcommission
     * @param \AppBundle\Entity\Subcommission            $subcommissionLast
     * @param \AppBundle\Entity\Commission               $commission
     * @param int                                        $i
     *
     * @return \AppBundle\Entity\Subcommission
     */
    private function duplicateSubcommissionAndPersist(ObjectManager $em, Subcommission $subcommission, Subcommission $subcommissionLast, Commission $commission, $i = 1){

        $yearmonth = $subcommissionLast->getYearmonth();
        $yearmonthNEW = $em->getRepository('AppBundle:YearMonth')->find($yearmonth->getId()+$i);

        $subcommissionNEW = clone($subcommission);
        $subcommissionNEW->setYearmonth($yearmonthNEW);
        $commission->addSubcommission($subcommissionNEW);

        $subcommissionTemporality = $subcommission->getData();
        $subcommissionTemporalityNEW = clone($subcommissionTemporality);

        $subcommissionTemporalityNEW->setDateFrom(new \DateTime());
        $subcommissionTemporalityNEW->setSubcommission($subcommissionNEW);

        $subcommissionTeam = $subcommission->getTeamData();
        if($subcommissionTeam){
            $subcommissionTeamNEW = clone($subcommissionTeam);

            $subcommissionTeamNEW->setDateFrom(new \DateTime());
            $subcommissionTeamNEW->setSubcommission($subcommissionNEW);

            $em->persist($subcommissionTeamNEW);
        }

        $em->persist($subcommissionNEW);
        $em->persist($subcommissionTemporalityNEW);

        return $subcommissionNEW;

    }

}
