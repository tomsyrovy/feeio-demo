<?php

namespace AppBundle\Controller;

use AppBundle\DependencyInjection\ActivityManager;
use AppBundle\DependencyInjection\Authorization\AuthorizationChecker;
use AppBundle\DependencyInjection\Authorization\AuthorizationIndividual;
use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
use AppBundle\DependencyInjection\FreeHoursManager;
use AppBundle\DependencyInjection\TimeWindowManager;
use AppBundle\Entity\AllocationContainer;
use AppBundle\Entity\AllocationContainerList;
use AppBundle\Entity\AllocationContainerListItem;
use AppBundle\Entity\AllocationUnit;
use AppBundle\Entity\CampaignManager;
use AppBundle\Entity\Commission;
use AppBundle\Entity\Company;
use AppBundle\Entity\Timesheet;
use AppBundle\Entity\TimeWindow;
use AppBundle\Entity\UserCompany;
use AppBundle\Form\Type\TimesheetFormType;
use AppBundle\Services\Sumarization\SumarizationService;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Tool\Vokativ;

class TimesheetController extends BaseController
{

	//The function returns the no. of business days between two dates and it skips the holidays
	private function getWorkingDays($startDate,$endDate,$holidays){
		// do strtotime calculations just once
		$endDate = strtotime($endDate);
		$startDate = strtotime($startDate);


		//The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
		//We add one to inlude both dates in the interval.
		$days = ($endDate - $startDate) / 86400 + 1;

		$no_full_weeks = floor($days / 7);
		$no_remaining_days = fmod($days, 7);

		//It will return 1 if it's Monday,.. ,7 for Sunday
		$the_first_day_of_week = date("N", $startDate);
		$the_last_day_of_week = date("N", $endDate);

		//---->The two can be equal in leap years when february has 29 days, the equal sign is added here
		//In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
		if ($the_first_day_of_week <= $the_last_day_of_week) {
			if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
			if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
		}
		else {
			// (edit by Tokes to fix an edge case where the start day was a Sunday
			// and the end day was NOT a Saturday)

			// the day of the week for start is later than the day of the week for end
			if ($the_first_day_of_week == 7) {
				// if the start date is a Sunday, then we definitely subtract 1 day
				$no_remaining_days--;

				if ($the_last_day_of_week == 6) {
					// if the end date is a Saturday, then we subtract another day
					$no_remaining_days--;
				}
			}
			else {
				// the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
				// so we skip an entire weekend and subtract 2 days
				$no_remaining_days -= 2;
			}
		}

		//The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
		$workingDays = $no_full_weeks * 5;
		if ($no_remaining_days > 0 )
		{
			$workingDays += $no_remaining_days;
		}

		//We subtract the holidays
		foreach($holidays as $holiday){
			$time_stamp=strtotime($holiday);
			//If the holiday doesn't fall in weekend
			if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
				$workingDays--;
		}

		return $workingDays;
	}

    /**
     * @Route("/timesheets/")
     * @Template()
     */
    public function listAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entity = new Timesheet();

        $commissionManager = new CommissionManager($em);

        $commissions = $commissionManager->getCommissionsOfUserInAllCompaniesWhereIsEnabledWithFavourites($user);

        $form = $this->getForm($entity, $commissions, array(
            'method' => 'post',
            'action' => $this->generateUrl('app_timesheet_list'))
        );

        $form->handleRequest($request);

        if($form->isValid()){

            $ac = new AuthorizationChecker($em);
            $check = $ac->checkAuthorizationCodeOfUserInCompany('timesheet-create', $user, $entity->getCommission()->getCompany());
            if(!$check){
                $this->addFlash('danger', 'Nemáte oprávnění vytvářet timesheety pro tuto zakázku.');

                return $this->redirectToRoute('app_timesheet_list');
            }

            $entity->setAuthor($user);
            $entity->setCreated(new \DateTime());
            $criteria = array(
                'year' => $entity->getDate()->format('Y'),
                'month' => $entity->getDate()->format('n'),
            );
            $yearmonth = $em->getRepository('AppBundle:YearMonth')->findOneBy($criteria);
            $entity->setYearmonth($yearmonth);

            $criteria = [
                'user' => $user,
                'company' => $entity->getCommission()->getCompany(),
            ];
            $userCompany = $em->getRepository('AppBundle:UserCompany')->findOneBy($criteria);

            $update = $request->get('update');

            if($update){

                /** @var Timesheet $entityToUpdate */
                $entityToUpdate = $em->getRepository('AppBundle:Timesheet')->find($update);

                $entityToUpdate->setAuthor($entity->getAuthor());
                $entityToUpdate->setCreated($entity->getCreated());
                $entityToUpdate->setYearmonth($entity->getYearmonth());
                $entityToUpdate->setCommission($entity->getCommission());
                $entityToUpdate->setActivity($entity->getActivity());
                $entityToUpdate->setDescription($entity->getDescription());
                $entityToUpdate->setDuration($entity->getDuration());
                $entityToUpdate->setDate($entity->getDate());

                $em->persist($entityToUpdate);
                $em->flush();

                $this->addFlash('success', 'Záznam byl upraven.');

            }else{

                $criteria = [
                    'yearMonth' => $yearmonth,
                    'commission' => $entity->getCommission(),
                    'userCompany' => $userCompany,
                ];
                $au = $em->getRepository('AppBundle:AllocationUnit')->findOneBy($criteria);

                if($au){
                    $oldValue = $au->getHoursReal();
                    if(!$oldValue){
                        $oldValue = 0;
                    }
                    $newValue = $oldValue+($entity->getDuration()/60);
                    $au->setHoursReal($newValue);
                    $em->persist($au);
                }

                $em->persist($entity);
                $em->flush();

                $this->addFlash('success', 'Timesheet záznam byl vytvořen.');

            }

            return $this->redirectToRoute('app_timesheet_list');

        }

        $twm = new TimeWindowManager($em, $user);
        $tw = $twm->getTimeWindow();

        $timesheetsInTw = $em->getRepository('AppBundle:Timesheet')->findByUserAndTimeWindow($user, $tw);

        $timesheetsGraph = [];
        $diff = $tw->getYearmonthTo()->getId()-$tw->getYearmonthFrom()->getId();
        if($diff <= 12*2){

            if($diff === 0){
                $days = cal_days_in_month(CAL_GREGORIAN, $tw->getYearmonthFrom()->getMonth(), $tw->getYearmonthFrom()->getYear());
                for($i = 1; $i <= $days; $i++){
                    $timesheetsGraph[$i] = [
                    	0 => 0,
	                    1 => 0
                    ];
                }
                /** @var Timesheet $timesheet */
                foreach($timesheetsInTw as $timesheet){
	                $index = $timesheet->getCommission()->getBillable() ? 0 : 1;
                    $timesheetsGraph[$timesheet->getDate()->format('j')][$index] += round($timesheet->getDuration()/60*100)/100;
                }
            }else{
                $id = $tw->getYearmonthFrom()->getId();
                do{
                    $yearmonth = $em->getRepository('AppBundle:YearMonth')->find($id);
                    $timesheetsGraph[$yearmonth->getCode()] = [
	                    0 => 0,
	                    1 => 0
                    ];
                    $id++;
                }while($yearmonth !== $tw->getYearmonthTo());

                /** @var Timesheet $timesheet */
                foreach($timesheetsInTw as $timesheet){
	                $index = $timesheet->getCommission()->getBillable() ? 0 : 1;
                    $timesheetsGraph[$timesheet->getYearmonth()->getCode()][$index] += round($timesheet->getDuration()/60*100)/100;
                }
            }
        }

        $now = new \DateTime();
        $currentWeek = $now->format('W');
        $currentMonth = $now->format('m');
	    $workDaysInCurrentWeek = max($now->format('N'), 5);
	    $f = new \DateTime();
	    $f->setDate($f->format('Y'), $f->format('m'), 1);
	    $workDaysInCurrentMonth = $this->getWorkingDays($f->format('Y-m-d'), $now->format('Y-m-d'), []);
	    $stats = [];
	    $stats['week'] = [
		    0 => 0,
		    1 => 0
	    ];
	    $stats['month'] = [
		    0 => 0,
		    1 => 0
	    ];
        $dql = '
            SELECT t
            FROM AppBundle\Entity\Timesheet t
            WHERE t.author = :author
            ORDER BY t.date DESC
        ';
        $query = $em->createQuery($dql);
        $query->setParameters([
        	'author' => $user
        ]);
        $query->setMaxResults(300);
        $timesheets = $query->getResult();
        /** @var Timesheet $timesheet */
        foreach($timesheets as $timesheet){
        	$d = $timesheet->getDate();
        	if($d->format('m') === $currentMonth){
		        $index = $timesheet->getCommission()->getBillable() ? 0 : 1;
		        $stats['month'][$index] += $timesheet->getDuration()/60;
	        }
	        if($d->format('W') === $currentWeek){
		        $index = $timesheet->getCommission()->getBillable() ? 0 : 1;
		        $stats['week'][$index] += $timesheet->getDuration()/60;
	        }
        }
	    $stats['month'][0] = round($stats['month'][0]/$workDaysInCurrentMonth*100)/100;
	    $stats['month'][1] = round($stats['month'][1]/$workDaysInCurrentMonth*100)/100;
	    $stats['week'][0] = round($stats['week'][0]/$workDaysInCurrentWeek*100)/100;
	    $stats['week'][1] = round($stats['week'][1]/$workDaysInCurrentWeek*100)/100;

        $data = array(
            'form' => $form->createView(),
            'now' => new \DateTime(),
            'timesheets' => $timesheetsInTw,
            'vokative' => Vokativ::getVokativ($user->getFirstName()),
            'timesheetsGraph' => $timesheetsGraph,
	        'stats' => $stats
        );

        return $data;

    }

	/**
	 * @Route("/my/commissions/")
	 * @Template()
	 */
	public function commissionsAction(Request $request){

		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

		$commissionManager = new CommissionManager($em);

		$commissions = $commissionManager->getCommissionsWhereParticipateInTimeWindow($user);

		$data = array(
			'commissions' => $commissions,
		);

		return $data;

	}

	/**
	 * @Route("/my/people/")
	 * @Template()
	 */
	public function peopleAction(Request $request){

		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

		$commissionManager = new CommissionManager($em);

		$twm = new TimeWindowManager($em, $user);
		$tw = $twm->getTimeWindow();

		$diff = $tw->getYearmonthTo()->getId()-$tw->getYearmonthFrom()->getId();


		$aus = [];
		$freeHours = [];
		$jobConsultants = [];
		$freeHoursSum = [];
		$commissionsInAu = [];

		$commissionWhereParticipate = $commissionManager->getCommissionsWhereParticipate($user);

		if($diff === 0){

			$criteria = [
				'yearMonth' => $tw->getYearmonthFrom(),
			];
			$allocationUnits = $em->getRepository('AppBundle:AllocationUnit')->findBy($criteria);
			/** @var AllocationUnit $allocationUnit */
			foreach($allocationUnits as $au){
				$commission = $au->getCommission();

				if(in_array($commission, $commissionWhereParticipate, true)){

					if(!in_array($commission, $commissionsInAu, true)){
						$commissionsInAu[] = $commission;
						$freeHoursSum['c'.$commission->getId()] = [
							'plan' => 0,
							'real' => 0,
						];
					}

					$u = $au->getUserCompany()->getUser();

					$aus['c'.$commission->getId().'_u'.$u->getId()] = $au;
					if(!in_array($u, $jobConsultants, true)){
						$jobConsultants[] = $u;

						$freeHoursSum['u'.$u->getId()] = [
							'plan' => 0,
							'real' => 0,
						];

					}

					$freeHoursSum['c'.$commission->getId()]['plan'] += $au->getHoursPlan();
					$freeHoursSum['u'.$u->getId()]['plan'] += $au->getHoursPlan();

					if($au->getHoursReal() !== null){
						$freeHoursSum['c'.$commission->getId()]['real'] += $au->getHoursReal();
						$freeHoursSum['u'.$u->getId()]['real'] += $au->getHoursReal();
					}

				}



			}

			uasort($commissionsInAu, function($a, $b){
				return $a->getName() > $b->getName();
			});

			uasort($jobConsultants, function($a, $b){
				return $a->getLastname() > $b->getLastname();
			});

			$ym = $tw->getYearmonthFrom();

		}else{
			$ym = null;
		}

		//Free hours
		$persons = [];
		$companies = $user->getCompaniesEnabled();
		/** @var Company $company */
		foreach($companies as $company){
			$ucs = $company->getUserCompanyRelationsOfTemporalityStatus('enabled');
			/** @var UserCompany $uc */
			foreach($ucs as $uc){
				if(!in_array($uc->getUser(), $persons, true)){
					$persons[] = $uc->getUser();
				}
			}
		}
		uasort($persons, function($a, $b){
			return $a->getLastname() > $b->getLastname();
		});

		$freeHoursManager = new FreeHoursManager($em);

		//TODO - provizorně
		$id = 118;
		$company = $em->getRepository('AppBundle:Company')->find($id);

		$yearmonths = [];
		for($i = $twm->getTimeWindow()->getYearmonthFrom()->getId(); $i <= $twm->getTimeWindow()->getYearmonthTo()->getId(); $i++){
			$yearmonth = $em->getRepository('AppBundle:YearMonth')->find($i);
			$yearmonths[] = $yearmonth;
			foreach($persons as $person){
				$key = 'u'.$person->getId().'_ym'.$yearmonth->getId();
				$freeHours[$key] = $freeHoursManager->getFreeHoursOfUserInCompany($person, $yearmonth, $company);
			}
		}

		$data = array(
			'commissionsInAu' => $commissionsInAu,
			'jobConsultants' => $jobConsultants,
			'aus' => $aus,
			'persons' => $persons,
			'yearmonths' => $yearmonths,
			'freeHours' => $freeHours,
			'freeHoursSum' => $freeHoursSum,
			'ym' => $ym,
		);

		return $data;

	}

    /**
     * @Route("/timesheet/{timesheet_id}/delete/")
     */
    public function deleteAction($timesheet_id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Timesheet')->find($timesheet_id);

        if(!$entity){
            $this->addFlash('danger', 'Tento timesheet neexistuje.');
            return $this->redirectToRoute('app_timesheet_list');
        }

        $user = $this->getUser();
        $companies = $user->getCompaniesEnabled();
        $company = $entity->getCommission()->getCompany();

        //Timesheet je ve společnosti, do které uživatel nemá přístup
        if(!$companies->contains($company)){
            $this->addFlash('danger', 'K tomuto timesheetu nemáte přístup.');
            return $this->redirectToRoute('app_timesheet_list');
        }

        $ac = new AuthorizationChecker($em);
        $check = $ac->checkAuthorizationCodeOfUserInCompany('timesheet-delete-own', $user, $company);
        if(!$check){
            $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte právo odstraňovat své timesheety.');
            return $this->redirectToRoute('app_timesheet_list');
        }

        $em->remove($entity);
        $em->flush();

        $this->addFlash('success', 'Timesheet byl odstraněn.');

        return $this->redirectToRoute('app_timesheet_list');

    }

    private function getForm($entity, $commissions, $params){

        $type = new TimesheetFormType($this->getUser(), $commissions);

        return $this->createForm($type, $entity, $params);

    }

}
