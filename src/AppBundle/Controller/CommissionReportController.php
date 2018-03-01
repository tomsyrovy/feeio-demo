<?php

namespace AppBundle\Controller;

use AppBundle\DataObject\CommissionUserCompanyRelationDataObject;
use AppBundle\DataObject\SubcommissionListDataObject;
use AppBundle\DataObject\TimesheetListDataObject;
use AppBundle\DependencyInjection\Authorization\AuthorizationCompany;
use AppBundle\DependencyInjection\Authorization\AuthorizationIndividual;
use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
use AppBundle\DependencyInjection\ControllerRedirect;
use AppBundle\DependencyInjection\ImageCreator\ImageCreator;
use AppBundle\DependencyInjection\TimeWindowManager;
use AppBundle\Entity\CommissionUserCompanyRelation;
use AppBundle\Entity\Company;
use AppBundle\Entity\UserCompany;
use AppBundle\Entity\YearMonth;
use AppBundle\Library\Slug;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use PhpExcel\Library\PhpExcelStyles;
use PhpExcel\PhpExcelGenerator;
use PhpExcel\PhpExcelGeneratorSubcommission;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Commission;
use AppBundle\Form\Type\CommissionFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\VarDumper\VarDumper;
use TableBundle\DependencyInjection\TableData;
use UserBundle\Entity\User;

/**
 * Commission controller.
 */
class CommissionReportController extends BaseController
{

    /**
     * @route("/commission/{commission_id}/report/")
     * @template()
     *
     * @return Response
     */
    public function indexAction($commission_id){

        $user = $this->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        if(!$commission){

            $this->addFlash('danger', 'Tato zakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        $cm = new CommissionManager($em);

        $m = array(
            'observer',
            'admin'
        );
        $check = $cm->checkCommissionUserCompanyManagedByRoleTypeCodes($user, $commission, 'enabled', $m);

        if(!$check){

            $this->addFlash('danger', 'Nemáte oprávnění zobrazovat tuto zakázku.');

            return $this->redirectToRoute('app_commission_list');

        }
        $timeWindowManager = new TimeWindowManager($em, $user);
        $timeWindow = $timeWindowManager->getTimeWindow();

//        $commissionUserCompany = $cm->getCommissionUserCompany($user, $commission, 'enabled');

        $data = array(
            'timeWindowDefault' => $timeWindowManager->isDefaultTimeWindow(),
            'timeWindow' => $timeWindowManager->getTimeWindow(),
            'commission' => $commission,
        );

        $tldo = new TimesheetListDataObject($em, $cm, $user, $commission);
        $sldo = new SubcommissionListDataObject($em, $cm, $user, $commission);

        $data = array_merge($data, $tldo->getData($timeWindow), $sldo->getData($timeWindow));

        return $data;

    }

    /**
     * @route("/commission/{commission_id}/subcommission/{subcommission_id}/export/xls/")
     * @template()
     * @param $commission_id
     * @param $subcommission_id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function subcommissionexportxlsAction($commission_id, $subcommission_id){

        $user = $this->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);
        $subcommission = $em->getRepository('AppBundle:Subcommission')->find($subcommission_id);


        if(!$commission or !$subcommission or $subcommission->getCommission() !== $commission){

            $this->addFlash('danger', 'Tato zakázka neexistuje.');

            return $this->redirectToRoute('app_commission_list');

        }

        $cm = new CommissionManager($em);

        $m = array(
            'observer',
            'admin'
        );
        $check = $cm->checkCommissionUserCompanyManagedByRoleTypeCodes($user, $commission, 'enabled', $m);

        if(!$check){

            $this->addFlash('danger', 'Nemáte oprávnění zobrazovat tuto zakázku.');

            return $this->redirectToRoute('app_commission_list');

        }

        //Generování XLS

        //TODO - vyřešit problém s kódováním
//        $author = $user->getFullname();
//        $author = mb_convert_encoding($author, 'ISO-8859-1', 'UTF-8');
        $author = 'Feeio';
        //TODO - CompanyName se neukládá korektně
        $companyName = $commission->getCompany()->getName();
        $title = Slug::getSlug($subcommission->getCode());


        $phpExcelGenerator = new PhpExcelGeneratorSubcommission($em, $title);
        $phpExcelGenerator->setMetaProperties($author, $companyName);
        $phpExcelGenerator->setData($subcommission);
        $phpExcelGenerator->generate();

    }


}
