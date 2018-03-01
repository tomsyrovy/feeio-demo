<?php

namespace AppBundle\Controller;

use AppBundle\DependencyInjection\Authorization\AuthorizationIndividual;
use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
use AppBundle\DependencyInjection\YearmonthManager\YearmonthManager;
use AppBundle\Entity\CostRepository;
use AppBundle\Form\Filter\Type\CostFormFilterType;
use AppBundle\Form\Type\CostFormType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Cost;
use TableBundle\DependencyInjection\TableData;
use UserBundle\Entity\User;


/**
 * Cost controller.
 *
 */
class CostController extends BaseCommissionController
{

    /**
     * @var CostRepository
     */
    private $costRepository;

    protected function init(){

        parent::init();
        $this->costRepository = $this->em->getRepository('AppBundle:Cost');

    }

    /**
     * Creates a new Cost entity.
     *
     * @Route("/cost/create/", name="app_cost_create")
     * @Route("/commission/{commission_id}/cost/create/", name="app_cost_create_viaCommission")
     * @Route("/commission/{commission_id}/{year}/{month}/cost/create/", name="app_cost_create_viaCommissionyearmonth")
     * @Template()
     */
    public function createAction($commission_id = null, $year = null, $month = null, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $commissions = $this->getCommissions($em, $user);

        $yearmonthManager = new YearmonthManager($em);
        $yearmonths = $yearmonthManager->getCollectionOfThisAndNextYears(date('Y'), 1); //tento rok a jeden následující

        $entity = new Cost();

        if($commission_id !== null){

            if($year !== null and $month !== null){

                $criteria = array(
                    'year' => $year,
                    'month' => $month
                );
                $yearmonth = $em->getRepository('AppBundle:YearMonth')->findOneBy($criteria);

                if(!$yearmonth){
                    $this->addFlash('danger', 'Časové období neexistuje.');
                    $params = [
                        'commission_id' => $commission_id
                    ];
                    return $this->redirectToRoute('app_cost_list', $params);
                }

                if(in_array($yearmonth, $yearmonths)){

                    $entity->setYearmonthReal($yearmonth);

                }

            }

            $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

            if(!$commission){

                $this->addFlash('danger', 'Tato zakázka neexistuje.');
                return $this->redirectToRoute('app_commission_list');

            }

            if(in_array($commission, $commissions)){

                $entity->setCommission($commission);

            }else{

                $this->addFlash('danger', 'K této zakázce nejste oprávněn přidávat náklady.');
                $params = [
                    'commission_id' => $commission_id,
                ];
                return $this->redirectToRoute('app_cost_list', $params);

            }

        }else{

            $commission = false;

        }

        $form = $this->getForm($entity, $commissions, $yearmonths);


        $form->handleRequest($request);

        if ($form->isValid()) {

            $entity->setCreatedBy($user);
            $entity->setCreated(new \DateTime());

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'Náklad byl úspěšně přidán k zakázce '.$entity->getCommission()->getName(),'.');

            $params = [
                'commission_id' => $entity->getCommission()->getId(),
            ];
            return $this->redirectToRoute('app_cost_list', $params);
        }

        return array(
            'form'   => $form->createView(),
            'commission' => $commission,
        );
    }

    /**
     * @Route("/commission/{commission_id}/costs/")
     * @Template()
     */
    public function listAction($commission_id, Request $request)
    {

        $this->init();
        $this->initCommission($commission_id);

        $cr = $this->checkCommission();
        if($cr !== false){
            return $this->redirectNow($cr);
        }

        $m = array(
            'admin',
            'observer',
        );
        $cr = $this->checkCommissionMgm($m);
        if($cr !== false){
            return $this->redirectNow($cr);
        }

        $yearmonths = $this->getYearmonths(1);



        //Filter form
        $entity = new Cost();
        $entity->setCommission($this->commission);
        $form = $this->createForm(new CostFormFilterType([$this->commission], $yearmonths), $entity);

        //Get filter result
        $params = [
            'commissions' => $this->commission,
        ];
        $qb = $this->getCostQueryBuilder($params);
        $filterResult = $this->getFilteredResults($form, $qb, $request);

        $tableData = TableData::getData($this->em, $this->user, 'table-costsofcommission');

        $data = array(
            'costs' => $filterResult['costs'],
            'commission' => $this->commission,
            'tableData' => $tableData,
            'canCreateCostBtn' => true,
            'form' => $form->createView(),
            'canResetFilterForm' => $filterResult['canResetFilterForm'],
        );

        return $data;

    }

    /**
     * @Route("/costs/")
     * @Template()
     */
    public function listallAction(Request $request)
    {

        $this->init();

        $commissions = $this->getCommissions($this->em, $this->user);

        $yearmonths = $this->getYearmonths(1);


        //Filter form
        $entity = new Cost();
        $form = $this->createForm(new CostFormFilterType($commissions, $yearmonths), $entity);

        //Filter Result
        $params = [
            'commissions' => $commissions,
        ];
        $qb = $this->getCostQueryBuilder($params);
        $filterResult = $this->getFilteredResults($form, $qb, $request);

        $tableData = TableData::getData($this->em, $this->user, 'table-costsofcommission');

        $data = array(
            'costs' => $filterResult['costs'],
            'commission' => null,
            'tableData' => $tableData,
            'canCreateCostBtn' => count($commissions) > 0,
            'form' => $form->createView(),
            'canResetFilterForm' => $filterResult['canResetFilterForm'],
        );

        return $data;

    }

    /**
     * Displays a form to edit an existing Cost entity.
     *
     * @Route("/commission/{commission_id}/cost/{cost_id}/update/")
     * @Template()
     */
    public function updateAction($commission_id, $cost_id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $commissions = $this->getCommissions($em, $user);

        $yearmonthManager = new YearmonthManager($em);
        $yearmonths = $yearmonthManager->getCollectionOfThisAndNextYears(date('Y'), 1); //tento rok a jeden následující

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        $entity = $em->getRepository('AppBundle:Cost')->find($cost_id);

        if(!$commission or !$entity or $entity->getCommission() != $commission){

            $this->addFlash('danger', 'Tato zakázka nebo náklad neexistuje.');
            return $this->redirectToRoute('app_commission_list');

        }

        if(in_array($commission, $commissions)){

            $entity->setCommission($commission);

        }else{

            $this->addFlash('danger', 'K této zakázce nejste oprávněn přidávat náklady.');
            $params = [
                'commission_id' => $commission_id
            ];
            return $this->redirectToRoute('app_cost_list', $params);

        }

        $form = $this->getForm($entity, $commissions, $yearmonths);


        $form->handleRequest($request);

        if ($form->isValid()) {

            $entity->setUpdated(new \DateTime());
            $entity->setUpdatedBy($user);

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'Náklad byl úspěšně upraven.');

            $params = [
                'commission_id' => $commission_id
            ];
            return $this->redirectToRoute('app_cost_list', $params);
        }

        return array(
            'form'   => $form->createView(),
            'commission' => $commission,
        );
    }

    /**
     * Displays a form to edit an existing Budget entity.
     *
     * @Route("/commission/{commission_id}/cost/{cost_id}/delete/")
     * @Template()
     */
    public function deleteAction($commission_id, $cost_id)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        $entity = $em->getRepository('AppBundle:Cost')->find($cost_id);

        if(!$commission or !$entity or $entity->getCommission() != $commission){

            $this->addFlash('danger', 'Tato zakázka nebo náklad neexistuje.');
            return $this->redirectToRoute('app_commission_list');

        }

        $commissions = $this->getCommissions($em, $user);

        if(!in_array($entity->getCommission(), $commissions)){

            $this->addFlash('danger', 'Nemáte právo odstraňovat tento náklad.');
            $params = [
                'commission_id' => $commission_id
            ];
            return $this->redirectToRoute('app_cost_list', $params);

        }else{

            $commission->removeCost($entity);
            $em->persist($commission);
            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'Náklad byl úspěšně odstraněn.');

            $params = [
                'commission_id' => $commission_id
            ];
            return $this->redirectToRoute('app_cost_list', $params);

        }

    }

    /**
     * @param $entity
     * @param $commissions
     * @param $yearmonths
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getForm($entity, $commissions, $yearmonths){

        return $this->createForm(new CostFormType($commissions, $yearmonths), $entity);

    }


    /**
     * Vrátí seznam zakázek se kterými může přihlašený uživatel v rámci nákladů pracovat
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param \UserBundle\Entity\User                    $user
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    private function getCommissions(ObjectManager $em, User $user){

        $resultset = []; //definuj resultset

        $ai = new AuthorizationIndividual($em);

        $companies = $ai->getCompaniesWhereUserHasAuthorizationCode('cost-manage', $user); //získej všechny společnosti, kde uživatel má právo spravovat všechny náklady

        foreach($companies as $company){ //pro každou společnost

            $comissions = $company->getCommissions(); //získej zakázky

            foreach($comissions as $comission){ //pro každou zakázku

                if(!in_array($comission, $resultset)){ //ověř, zda-li není v resultsetu - a pokud není

                    $resultset[] = $comission; //tak ji do resultsetu ulož

                }

            }

        }

        $cm = new CommissionManager($em);

        $cucrs = $cm->getCommissionUserCompanyRelationDOsOfUser($user, 'enabled', ['admin']); //získej všechny zakázky uživatele, kde je daný uživatel adminem

        foreach($cucrs as $cucr){ //pro každou zakázku

            $commission = $cucr->getCommission();

            if(!in_array($commission, $resultset)){ //ověř, zda-li není v resultsetu - a pokud není

                $resultset[] = $commission; //tak ji do resultsetu ulož

            }

        }

        usort($resultset, function($a, $b){
            return strcmp($a->getName(), $b->getName());
        });

        return $resultset;

    }

    /**
     * Return an array of yearmonths of actual year and defined next years
     *
     * @param int $years
     *
     * @return array
     */
    private function getYearmonths($years = 1){

        $yearmonthManager = new YearmonthManager($this->em);
        $yearmonths = $yearmonthManager->getCollectionOfThisAndNextYears(date('Y'), $years); //tento rok a x následující

        return $yearmonths;

    }

    /**
     * Return QueryBuilder for select costs
     *
     * @param array $params
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getCostQueryBuilder(array $params){

        //Create QB for select costs
        $qb = new QueryBuilder($this->em);
        $qb->select('c');
        $qb->from('AppBundle:Cost', 'c');
        $qb->where($qb->expr()->in('c.commission', ':commissions'));
        $qb->setParameters($params);

        return $qb;

    }

    /**
     * Return filteredCosts
     *
     * @param \Symfony\Component\Form\Form $form
     *
     * @return array
     */
    private function getFilteredCostsViaFilter(Form $form){

        // initialize a query builder
        $filterBuilder = $this->costRepository->createQueryBuilder('c');

        // build the query from the given form object
        $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);

        $filteredCosts = $filterBuilder->getQuery()->getResult();

        return $filteredCosts;

    }

    /**
     * Return filtered cost via submitted form, and querybuilder
     *
     * @param \Symfony\Component\Form\Form              $form
     * @param \Doctrine\ORM\QueryBuilder                $qb
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    private function getFilteredResults(Form $form, QueryBuilder $qb, Request $request){

        $canResetFilterForm = false;

        $form->handleRequest($request);

        if($form->isSubmitted()){

            $filteredCosts = $this->getFilteredCostsViaFilter($form);

            $qb->andWhere($qb->expr()->in('c', ':filteredCosts'));
            $qb->setParameter('filteredCosts', $filteredCosts);

            $canResetFilterForm = true;

        }

        //Get results of defined QB
        $costs = $qb->getQuery()->getResult();

        return [
            'canResetFilterForm' => $canResetFilterForm,
            'costs' => $costs,
        ];

    }
}
