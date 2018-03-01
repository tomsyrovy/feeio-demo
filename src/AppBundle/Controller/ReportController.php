<?php

namespace AppBundle\Controller;
use AppBundle\DependencyInjection\Authorization\AuthorizationIndividual;
use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
use AppBundle\DependencyInjection\TimeWindowManager;
use AppBundle\Entity\AllocationContainer;
use AppBundle\Entity\AllocationContainerList;
use AppBundle\Entity\AllocationContainerListItem;
use AppBundle\Entity\Commission;
use AppBundle\Entity\Company;
use AppBundle\Report\Builder\DimensionManager;
use AppBundle\Report\Query\QueryManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Report controller.
 */
class ReportController extends Controller
{

    /**
     * @Route("/report-sim/")
     * @Template()
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \LogicException
     */
    public function rAction(){

        $em = $this->getDoctrine()->getManager();

        $criteria = [
            'year' => 2017,
            'month' => 1,
        ];
        $yearmonth = $em->getRepository('AppBundle:YearMonth')->findOneBy($criteria);

        $criteria = [
            'startDate' => $yearmonth,
            'enabled' => true,
        ];
        $orderBy = [
            'name' => 'ASC',
        ];
        $commissions = $em->getRepository('AppBundle:Commission')->findBy($criteria, $orderBy);

        $rows = [];

        /** @var Commission $commission */
        foreach($commissions as $commission){
            $criteria = [
                'enabled' => true,
                'clientApproved' => true,
                'commission' => $commission,
            ];
            /** @var AllocationContainer $allocationContainer */
            $allocationContainer = $em->getRepository('AppBundle:AllocationContainer')->findOneBy($criteria);

            $p_u_p = 0;
            $p_u_r = 0;
            $p_is_p = 0;
            $p_is_r = 0;
            $p_es_p = 0;
            $p_es_r = 0;
            $m_u_p = 0;
            $m_u_r = 0;
            $m_is_p = 0;
            $m_is_r = 0;
            $m_es_p = 0;
            $m_es_r = 0;

            if($allocationContainer){
                /** @var AllocationContainerList $allocationContainerList */
                foreach($allocationContainer->getAllocationContainerLists() as $allocationContainerList){
                    /** @var AllocationContainerListItem $allocationContainerListItem */
                    foreach($allocationContainerList->getAllocationContainerListItems() as $allocationContainerListItem){
                        if($allocationContainerListItem->getUnit() === 'h' and $allocationContainerListItem->getConcreteSource()){
                            $p_u_p = $p_u_p + $allocationContainerListItem->getQuantityPlan();
                            $p_u_r = $p_u_r + $allocationContainerListItem->getQuantityReal();
                            $p_is_p = $p_is_p + ($allocationContainerListItem->getQuantityPlan()*$allocationContainerListItem->getBuyingPricePlan());
                            $p_is_r = $p_is_r + ($allocationContainerListItem->getQuantityReal()*$allocationContainerListItem->getBuyingPriceReal());
                            $p_es_p = $p_es_p + ($allocationContainerListItem->getQuantityPlan()*$allocationContainerListItem->getSellingPricePlan());
                            $p_es_r = $p_es_r + ($allocationContainerListItem->getQuantityReal()*$allocationContainerListItem->getSellingPriceReal());
                        }else{
                            $m_u_p = $m_u_p + $allocationContainerListItem->getQuantityPlan();
                            $m_u_r = $m_u_r + $allocationContainerListItem->getQuantityReal();
                            $m_is_p = $m_is_p + ($allocationContainerListItem->getQuantityPlan()*$allocationContainerListItem->getBuyingPricePlan());
                            $m_is_r = $m_is_r + ($allocationContainerListItem->getQuantityReal()*$allocationContainerListItem->getBuyingPriceReal());
                            $m_es_p = $m_es_p + ($allocationContainerListItem->getQuantityPlan()*$allocationContainerListItem->getSellingPricePlan());
                            $m_es_r = $m_es_r + ($allocationContainerListItem->getQuantityReal()*$allocationContainerListItem->getSellingPriceReal());
                        }
                    }
                }
            }
            
            $row = [
                "n" => $commission->getName(),
                "p_u_p" => $p_u_p,
                "p_u_r" => $p_u_r,
                "p_is_p" => $p_is_p,
                "p_is_r" => $p_is_r,
                "p_es_p" => $p_es_p,
                "p_es_r" => $p_es_r,
                "m_u_p" => $m_u_p,
                "m_u_r" => $m_u_r,
                "m_is_p" => $m_is_p,
                "m_is_r" => $m_is_r,
                "m_es_p" => $m_es_p,
                "m_es_r" => $m_es_r,
                "i" => $commission->getInvoiceItemsSum()
            ];

            $rows[] = $row;
            
        }

        return [
            'rows' => $rows,
        ];

    }

    /**
     * @Route("/report/")
     * @Template()
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \LogicException
     */
    public function listAction(){

        $em = $this->getDoctrine()->getManager();

        $criteria = [];
        $orderBy = [];

        $user = $this->getUser();
        $dimensionManager = new DimensionManager($em, $user);

        $reportConfigurations = $em->getRepository('AppBundle:ReportConfiguration')->findBy($criteria, $orderBy);

        $reportConfigurationDOs = [];

        foreach($reportConfigurations as $reportConfiguration){

            $commissions = $dimensionManager->getCommissions($reportConfiguration);
            $users = $dimensionManager->getUsers($reportConfiguration);

            $formBuilder = $this->createFormBuilder();
            $formBuilder->add('commissions', 'entity', [
                'label' => 'Zakázky',
                'class' => 'AppBundle\Entity\Commission',
                'choices' => $commissions,
                'choice_label' => 'name',
            ]);
            $formBuilder->add('users', 'entity', [
                'label' => 'Uživatelé',
                'class' => 'UserBundle\Entity\User',
                'choices' => $users,
                'choice_label' => 'fullname',
            ]);

            $reportConfigurationDO = [
                'reportConfiguration' => $reportConfiguration,
                'form' => $formBuilder->getForm()->createView(),
            ];

            $reportConfigurationDOs[] = $reportConfigurationDO;

        }

        $user = $this->getUser();

        $timeWindowManager = new TimeWindowManager($em, $user);
        $timeWindow = $timeWindowManager->getTimeWindow();

        $data = [
            'reportConfigurationDOs' => $reportConfigurationDOs,
            'timeWindowDefault' => $timeWindowManager->isDefaultTimeWindow(),
            'timeWindow' => $timeWindow,
        ];

        return $data;

    }

    /**
     * @Route("/report/{report_configuration_id}/")
     * @Template()
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \LogicException
     */
    public function defaultAction($report_configuration_id){

        $em = $this->getDoctrine()->getManager();

        $rc = $em->getRepository('AppBundle:ReportConfiguration')->find($report_configuration_id);

        if(!$rc){

            $this->addFlash('danger', 'Požadovaný report neexistuje.');

            return $this->redirectToRoute('app_report_list');

        }

        //TODO - mám právo zobrazit si tento report

        $rc = $rc->getFields();

        $user = $this->getUser();
        $timeWindowManager = new TimeWindowManager($em, $user);
        $timeWindow = $timeWindowManager->getTimeWindow();

        $qm = new QueryManager($em);

        $query = $qm->getAllocations();
//        $result = $qm->execute($query);

        $rc = [
            'indicator' => 'allocation',
            'fields' => [
                'co_name',
                'cl_name',
                'ca_name',
                'c_id',
                'ac_name',
                'acl_name',
                'acli_id',
                'ym_year',
                'ym_month',
//                'acli_quantityPlan',
//                'acli_buyingPricePlan',
//                'acli_sellingPricePlan',
//                'acli_profitPlan',
//                'acli_totalBuyingPricePlan',
//                'acli_totalSellingPricePlan',
                'acli_totalProfitPlan',
//                'acli_quantityReal',
//                'acli_buyingPriceReal',
//                'acli_sellingPriceReal',
//                'acli_profitReal',
//                'acli_totalBuyingPriceReal',
//                'acli_totalSellingPriceReal',
                'acli_totalProfitReal',
            ],
            'options' => [
                'convertToInt' => [
//                    'acli_quantityPlan',
//                    'acli_buyingPricePlan',
//                    'acli_sellingPricePlan',
//                    'acli_profitPlan',
//                    'acli_totalBuyingPricePlan',
//                    'acli_totalSellingPricePlan',
                    'acli_totalProfitPlan',
//                    'acli_quantityReal',
//                    'acli_buyingPriceReal',
//                    'acli_sellingPriceReal',
//                    'acli_profitReal',
//                    'acli_totalBuyingPriceReal',
//                    'acli_totalSellingPriceReal',
                    'acli_totalProfitReal',
                ],
            ],
        ];

//        switch($rc['indicator']){
//            case 'timesheet' : {
//                $query = $qm->getTimesheetsInTimeWindow($timeWindow);
//            }break;
//            default: {
//                throw new \Exception('There is no report for defined indicator.');
//            }
//        }

        $result = $qm->execute($query, $rc['fields']);

//        dump($result);exit;

        if(array_key_exists('options', $rc)){

            if(array_key_exists('convertToInt', $rc['options'])){

                $result = $qm->convertToInt($result, $rc['options']['convertToInt']);

            }

        }

        $data = [
            'data' => json_encode($result),
        ];

        return $data;

    }

}
