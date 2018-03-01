<?php
//
//namespace AppBundle\Controller;
//
//use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
//use AppBundle\DependencyInjection\ControllerRedirect;
//use AppBundle\DependencyInjection\YearmonthManager\YearmonthManager;
//use AppBundle\Entity\BudgetRepository;
//use AppBundle\Entity\Commission;
//use AppBundle\Entity\CommissionRepository;
//use AppBundle\Form\Type\BudgetFormType;
//use Doctrine\Common\Persistence\ObjectManager;
//use Doctrine\Common\Util\Debug;
//use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Component\EventDispatcher\EventDispatcher;
//use Symfony\Component\Form\Extension\Validator\Constraints\Form;
//use Symfony\Component\Form\FormError;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
//use AppBundle\Entity\Budget;
//use Symfony\Component\VarDumper\VarDumper;
//use TableBundle\DependencyInjection\TableData;
//use UserBundle\Entity\User;
//
///**
// * Budget controller.
// */
//class BudgetController extends BaseCommissionController
//{
//
//    /**
//     * @var BudgetRepository
//     */
//    private $budgetRepository;
//
//    protected function init(){
//
//        parent::init();
//        $this->budgetRepository = $this->em->getRepository('AppBundle:Budget');
//
//    }
//
//    /**
//     * Lists all Budget entities.
//     *
//     * @Route("/commission/{commission_id}/budgets/")
//     * @Template()
//     */
//    public function listAction($commission_id)
//    {
//
//        $this->init();
//        $this->initCommission($commission_id);
//
//        $cr = $this->checkCommission();
//        if($cr !== false){
//            return $this->redirectNow($cr);
//        }
//
//        $m = array(
//            'admin',
//            'observer',
//        );
//        $cr = $this->checkCommissionMgm($m);
//        if($cr !== false){
//            return $this->redirectNow($cr);
//        }
//
////        $twm = new TimeWindowManager($this->em, $this->user);
//
//        $criteria = array(
//            'commission' => $this->commission,
//        );
//        $budgets = $this->budgetRepository->findBy($criteria);
//
//        $tableData = TableData::getData($this->em, $this->user, 'table-budgets');
//
//
//        $data = array(
//            'budgets' => $budgets,
//            'commission' => $this->commission,
//            'tableData' => $tableData,
//        );
//
//        return $data;
//
//    }
//    /**
//     * Creates a new Budget entity.
//     *
//     * @Route("/commission/{commission_id}/budget/create/")
//     * @Template()
//     */
//    public function createAction($commission_id, Request $request)
//    {
//
//        $this->init();
//        $this->initCommission($commission_id);
//
//        $cr = $this->checkCommission();
//        if($cr !== false){
//            return $this->redirectNow($cr);
//        }
//
//        $m = array(
//            'admin',
//            'observer',
//        );
//        $cr = $this->checkCommissionMgm($m);
//        if($cr !== false){
//            return $this->redirectNow($cr);
//        }
//
//        $criteria = array(
//            'user' => $this->user,
//            'company' => $this->commission->getCompany(),
//        );
//        $userCompany = $this->em->getRepository('AppBundle:UserCompany')->findOneBy($criteria);
//
//        if(!$userCompany){
//            $params = array(
//                'commission_id' => $this->commission->getId(),
//            );
//            $cr = new ControllerRedirect('danger', 'Pro tuto zakázku nemůžete vytvořit rozpočet.', 'app_commission_detail', $params);
//            return $this->redirectNow($cr);
//        }
//
//        $entity = new Budget();
//        $yearmonthManager = new YearmonthManager($this->em);
//        $yearmonths = $yearmonthManager->getCollectionOfThisAndNextYears(date('Y'), 1);
//        $form = $this->getForm($entity, $yearmonths);
//
//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
//
//            $entity->setAuthor($userCompany);
//            $entity->setCommission($this->commission);
//            $entity->setCreated(new \DateTime());
//            $entity->setVersion($this->commission->getBudgets()->count()+1);
//
//            $this->em->persist($entity);
//            $this->em->flush();
//
//            $params = array(
//                'commission_id' => $this->commission->getId(),
//            );
//            $cr = new ControllerRedirect('success', 'Rozpočet byl vytvořen.', 'app_budget_list', $params);
//            return $this->redirectNow($cr);
//
//        }else{
//
//            $entity = $form->getData();
//            $newForm = $this->getForm($entity, $yearmonths);
//            self::copyErrorsRecursively($form, $newForm);
//            $form = $newForm;
//
//        }
//
//        return array(
//            'budget' => $entity,
//            'commission' => $this->commission,
//            'form'   => $form->createView(),
//        );
//    }
//
//    /**
//     * Displays a form to edit an existing Budget entity.
//     *
//     * @Route("/commission/{commission_id}/budget/{budget_id}/update/")
//     * @Template()
//     */
//    public function updateAction($commission_id, $budget_id, Request $request)
//    {
//        $this->init();
//        $this->initCommission($commission_id);
//
//        $cr = $this->checkCommission();
//        if($cr !== false){
//            return $this->redirectNow($cr);
//        }
//
//        $m = array(
//            'admin',
//            'observer',
//        );
//        $cr = $this->checkCommissionMgm($m);
//        if($cr !== false){
//            return $this->redirectNow($cr);
//        }
//
//        $criteria = array(
//            'user' => $this->user,
//            'company' => $this->commission->getCompany(),
//        );
//        $userCompany = $this->em->getRepository('AppBundle:UserCompany')->findOneBy($criteria);
//
//        if(!$userCompany){
//            $params = array(
//                'commission_id' => $this->commission->getId(),
//            );
//            $cr = new ControllerRedirect('danger', 'Pro tuto zakázku nemůžete vytvořit rozpočet.', 'app_commission_detail', $params);
//            return $this->redirectNow($cr);
//        }
//
//        $entity = $this->budgetRepository->find($budget_id);
//
//        if(!$entity){
//            $params = array(
//                'commission_id' => $this->commission->getId(),
//            );
//            $cr = new ControllerRedirect('danger', 'Rozpočet neexistuje.', 'app_commission_detail', $params);
//            return $this->redirectNow($cr);
//        }
//
//
//        $yearmonthManager = new YearmonthManager($this->em);
//        $yearmonths = $yearmonthManager->getCollectionOfThisAndNextYears(date('Y'), 1);
//        $form = $this->getForm($entity, $yearmonths);
//
//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
//
//            $this->em->persist($entity);
//            $this->em->flush();
//
//            $params = array(
//                'commission_id' => $this->commission->getId(),
//            );
//            $cr = new ControllerRedirect('success', 'Rozpočet byl upraven.', 'app_budget_list', $params);
//            return $this->redirectNow($cr);
//
//        }else{
//
//            $entity = $form->getData();
//            $newForm = $this->getForm($entity, $yearmonths);
//            self::copyErrorsRecursively($form, $newForm);
//            $form = $newForm;
//
//        }
//
//        return array(
//            'budget' => $entity,
//            'commission' => $this->commission,
//            'form'   => $form->createView(),
//        );
//    }
//
//    /**
//     * Creates a form to create a Budget entity.
//     *
//     * @param \AppBundle\Entity\Budget $entity
//     * @param                          $yearmonths
//     *
//     * @return \Symfony\Component\Form\Form
//     */
//    private function getForm(Budget $entity, $yearmonths)
//    {
//
//        $form = $this->createForm(new BudgetFormType($yearmonths), $entity);
//
//        return $form;
//    }
//
//    private static function copyErrorsRecursively(\Symfony\Component\Form\Form &$copyFrom, \Symfony\Component\Form\Form &$copyTo)
//    {
//        /** @var $error FormError */
//        foreach ($copyFrom->getErrors() as $error)
//        {
//            $copyTo->addError($error);
//        }
//
//        foreach ($copyFrom->all() as $key => $child)
//        {
//            if ($copyTo->has($key))
//            {
//                $childTo = $copyTo->get($key);
//                self::copyErrorsRecursively($child, $childTo);
//            }
//        }
//    }
//}
