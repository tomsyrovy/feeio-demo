<?php

namespace AppBundle\Controller;

use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
use AppBundle\DependencyInjection\TimeWindowManager;
use AppBundle\Library\Slug;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class PersonController extends BaseController
{

    /**
     * @Route("/person/{person_id}/{output}")
     * @Template()
     */
    public function defaultAction(Request $request, $person_id, $output = null){

	    $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $person = $em->getRepository('UserBundle:User')->find($person_id);

        if(!$person){
	        $this->addFlash('danger', 'Tato osoba neexistuje.');
	        return $this->redirectToRoute('app_timesheet_list');
        }

	    $red = false;
	    foreach($request->request as $key => $item){
		    $item = (float)$item;
		    $parts = explode('_', $key);
		    if(array_key_exists(0, $parts) && array_key_exists(1, $parts)){
			    if($parts[0] == 'au'){
				    $au = $em->getRepository('AppBundle:AllocationUnit')->find($parts[1]);
				    if($au){
//				    	dump($au->getHoursPlan());
					    if($item != $au->getHoursPlan()){
						    $au->setHoursPlan($item);
						    $em->persist($au);
						    $red = true;
//						    dump($au);
//						    exit;
					    }
				    }
			    }
		    }
	    }
//	    exit;
	    if($red){
		    $em->flush();
		    $this->addFlash('success', 'Alokace osoby byla upravena.');
		    $params = [
		    	'person_id' => $person->getId(),
		    ];
		    return $this->redirectToRoute('app_person_default', $params);
	    }

	    $twm = new TimeWindowManager($em, $user);
	    $tw = $twm->getTimeWindow();

	    $dql = '
	        SELECT ym
	        FROM AppBundle\Entity\YearMonth ym
	        WHERE ym.id >= :ym1
	        AND ym.id <= :ym2
	    ';
	    $query = $em->createQuery($dql);
	    $query->setParameters([
		    'ym1' => $tw->getYearmonthFrom(),
		    'ym2' => $tw->getYearmonthTo(),
	    ]);
	    $yearMonths = $query->getResult();

	    if(count($yearMonths) > 12){
		    $this->addFlash('danger', 'Alokaci osoby si můžete zobrazit pouze v časovém okně 12 měsíců.');
		    return $this->redirectToRoute('app_timesheet_list');
	    }

	    $sql = '
			SELECT DISTINCT au.id AS au_id, co.id AS co_id, co.name AS co_name, ym.id AS ym_id, ym.year AS ym_year, ym.month AS ym_month, au.hoursPlan, au.hoursReal
		  	FROM AllocationUnit au
			JOIN Commission co ON au.commission_id = co.id
			JOIN Campaign ca ON co.campaign_id = ca.id
			JOIN Client cl ON ca.client_id = cl.id
			JOIN Company c ON cl.company_id = c.id
			JOIN CampaignManager cm ON ca.id = cm.campaign_id
			JOIN YearMonth ym ON au.yearMonth_id = ym.id
			JOIN UserCompany uc ON au.userCompany_id = uc.id
			JOIN UserCompanyTemporality uct ON uc.id = uct.userCompany_id
			JOIN UserCompanyTemporalityStatus ucts ON uct.status_id = ucts.id
			JOIN Company c2 ON uc.company_id = c2.id
			JOIN User u ON uc.user_id = u.id
			WHERE co.enabled = 1
			AND ca.enabled = 1
			AND cl.enabled = 1
			AND c.enabled = 1
			AND cm.jobConsultant = 1
			AND ucts.code = "enabled"
			AND c2.enabled = 1
			AND c.id = c2.id
			AND u.enabled = 1
			AND cm.userCompany_id = uc.id
			AND u.id = :person_id
			AND ym.id >= :yearmonthFrom_id
			AND ym.id <= :yearmonthTo_id
			ORDER BY co.name ASC
		';

	    /** @var \Doctrine\DBAL\Statement $stmt */
	    $stmt = $em->getConnection()->prepare($sql);
	    $stmt->bindParam('person_id', $person_id);
	    $ymF_id = $tw->getYearmonthFrom()->getId();
	    $ymT_id = $tw->getYearmonthTo()->getId();
	    $stmt->bindParam('yearmonthFrom_id', $ymF_id);
	    $stmt->bindParam('yearmonthTo_id', $ymT_id);
	    $stmt->execute();
	    $result = $stmt->fetchAll();

	    $cm = new CommissionManager($em);
	    $commissions = $cm->getCommissionsWhereParticipateInTimeWindow($user);

//	    dump($commissions);exit;

	    $aus = [];

//	    $k = 0;

	    $commissionsInAu = [];
	    $freeHoursSum = [];
	    foreach($result as $r){
	    	$co = $em->getRepository('AppBundle:Commission')->find($r['co_id']);
		    if(in_array($co, $commissions, true)){
			    if(!in_array($co, $commissionsInAu, true)){
				    $commissionsInAu[] = $co;
			    }
			    if(!isset($freeHoursSum['ym'.$r['ym_id']])){
				    $freeHoursSum['ym'.$r['ym_id']] = [
					    'plan' => 0,
					    'real' => 0,
				    ];
			    }
//			    $k++;
			    $freeHoursSum['ym'.$r['ym_id']]['plan'] = $freeHoursSum['ym'.$r['ym_id']]['plan'] + $r['hoursPlan'];
			    $freeHoursSum['ym'.$r['ym_id']]['real'] = $freeHoursSum['ym'.$r['ym_id']]['real'] + $r['hoursReal'];
			    $key = 'co'.$r['co_id'].'_ym'.$r['ym_id'];
			    $aus[$key] = $r;
		    }
	    }

//	    dump($k);
//	    dump($commissionsInAu);exit;

	    if($output !== null && $output = 'xls'){

		    //TODO - vyřešit problém s kódováním
//        $author = $user->getFullname();
//        $author = mb_convert_encoding($author, 'ISO-8859-1', 'UTF-8');
		    $author = "Feeio";

		    $title = Slug::getSlug($person->getFullName());

		    //TODO - generování XLS zjednodušit a převést do jiné třídy
		    // ask the service for a Excel5
		    $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

		    $phpExcelObject->getProperties()->setCreator($author)
		                   ->setLastModifiedBy($author)
		                   ->setTitle($title)
		                   ->setSubject($title)
		                   ->setDescription("Document ".$title." generated by Feeio (www.feeio.com). ")
		                   ->setKeywords($title." feeio");

		    $styleArrayTotal = array(
			    'font'  => array(
				    'bold'  => true,
				    'size'  => 12,
			    ));

		    $styleArrayGrandTotal = array(
			    'font'  => array(
				    'bold'  => true,
				    'size'  => 14,
			    ));

		    $styleArrayHeader = array(
			    'font'  => array(
				    'bold'  => true,
				    'size'  => 14,
			    ));

		    // == začátek plnění dat
		    $phpExcelObject->setActiveSheetIndex(0);
		    $phpExcelObject->getActiveSheet()->setTitle('Alokace');

		    $sheet = $phpExcelObject->getActiveSheet();

		    $c = 0;
		    $r = 1;
		    $grandTotal = 0;

		    $sheet->getCellByColumnAndRow($c, $r)->setValue('Alokace pro '.$person->getFullName());
		    $r++;
		    $r++;

		    $c = 1;
		    /** @var \AppBundle\Entity\YearMonth $yearMonth */
		    foreach($yearMonths as $yearMonth){
		    	$sheet->getCellByColumnAndRow($c, $r)->setValue($yearMonth->getCode());
		    	$c++;
			    $sheet->getCellByColumnAndRow($c, $r)->setValue($yearMonth->getCode());
			    $c++;
		    }
		    $r++;
		    $c = 1;
		    /** @var \AppBundle\Entity\YearMonth $yearMonth */
		    foreach($yearMonths as $yearMonth){
			    $sheet->getCellByColumnAndRow($c, $r)->setValue('plán');
			    $c++;
			    $sheet->getCellByColumnAndRow($c, $r)->setValue('reál');
			    $c++;
		    }
		    $r++;
		    /** @var \AppBundle\Entity\Commission $commission */
		    foreach($commissionsInAu as $commission){
			    $c = 0;
			    $sheet->getCellByColumnAndRow($c, $r)->setValue($commission->getName().' ('.$commission->getNameOwn().')');
			    $c++;
			    foreach($yearMonths as $yearMonth){
				    $sheet->getCellByColumnAndRow($c, $r)->setValue(round($aus['co' . $commission->getId() . '_ym' . $yearMonth->getId()]['hoursPlan']*100)/100);
				    $c++;
				    $sheet->getCellByColumnAndRow($c, $r)->setValue(round($aus['co' . $commission->getId() . '_ym' . $yearMonth->getId()]['hoursReal']*100)/100);
				    $c++;
			    }
			    $r++;
		    }

		    // == konec plnění dat

		    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
		    $phpExcelObject->setActiveSheetIndex(0);

		    // Auto size columns for each worksheet
		    foreach ($phpExcelObject->getWorksheetIterator() as $worksheet) {

			    $phpExcelObject->setActiveSheetIndex($phpExcelObject->getIndex($worksheet));

			    $sheet = $phpExcelObject->getActiveSheet();
			    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
			    $cellIterator->setIterateOnlyExistingCells(true);
			    /** @var \PHPExcel_Cell $cell */
			    foreach ($cellIterator as $cell) {
				    $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
			    }
		    }

		    $filename = $title.'.xls';
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/vnd.ms-excel');
		    header('Content-Disposition: attachment; filename="'.$filename.'"');
		    header('Content-Transfer-Encoding: binary');
		    header('Connection: Keep-Alive');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		    header('Pragma: public');

		    $objWriter = new \PHPExcel_Writer_Excel5($phpExcelObject);
		    $objWriter->save('php://output');
		    exit;

	    }

	    $data = array(
	    	'person' => $person,
		    'yearMonths' => $yearMonths,
		    'commissionsInAu' => $commissionsInAu,
		    'aus' => $aus,
		    'freeHoursSum' => $freeHoursSum,
	    );

	    return $data;


    }

}
