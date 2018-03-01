<?php

namespace AppBundle\Controller;

use AppBundle\DataObject\CRUDLDataObject;
use AppBundle\DependencyInjection\Authorization\AuthorizationChecker;
use AppBundle\DependencyInjection\Authorization\AuthorizationCompany;
use AppBundle\DependencyInjection\Authorization\AuthorizationIndividual;
use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
use AppBundle\Entity\Campaign;
use AppBundle\Entity\CampaignManager;
use AppBundle\Entity\Client;
use AppBundle\Entity\Commission;
use AppBundle\Entity\Company;
use AppBundle\Entity\CompanyGroupUserCompanyRelation;
use AppBundle\Entity\Contact;
use AppBundle\Entity\JobPosition;
use AppBundle\Entity\UserCompany;
use AppBundle\Entity\UserCompanyTemporality;
use AppBundle\Form\Type\CampaignFormType;
use AppBundle\Form\Type\ClientFormType;
use AppBundle\Form\Type\ContactFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;
use UserBundle\Entity\User;

class CampaignController extends Controller
{

	/**
	 * @route("/api/campaignmanagers/get", name="app.campaignManagers.get")
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 */
	public function sourcelistAction(Request $request){

		$em = $this->getDoctrine()->getManager();

		$campaignManagers = [];
		$ucrs = [];

		$company_id = $request->request->get('company_id');
		if($company_id){
			$company = $em->getRepository('AppBundle:Company')->find($company_id);
			if($company){
				$ucrs = $company->getUserCompanyRelationsOfTemporalityStatus('enabled');
				/** @var UserCompany $ucr */
				foreach($ucrs as $ucr){
					$campaignManagers[] = [
						'id' => $ucr->getId(),
						'fullname' => $ucr->getUser()->getFullName(),
					];
				}
			}
		}

		$companyGroup_id = $request->request->get('companygroup_id');
		if($companyGroup_id){
			$companyGroup = $em->getRepository('AppBundle:CompanyGroup')->find($companyGroup_id);
			if($companyGroup){
				/** @var CompanyGroupUserCompanyRelation $cgucr */
				foreach($companyGroup->getCompanyGroupUserCompanyRelationsOfEnabledTemporality() as $cgucr){
					$campaignManagers[] = [
						'id' => $cgucr->getUserCompany()->getId(),
						'fullname' => $cgucr->getUserCompany()->getUser()->getFullName(),
						'owner' => ($cgucr->getData()->getCompanyGroupUserCompanyRelationTemporalityType()->getCode() === 'admin'),
						'jobManager' => ($cgucr->getData()->getCompanyGroupUserCompanyRelationTemporalityType()->getCode() === 'admin'),
						'jobConsultant' => true,
					];
				}
			}
		}




		//TODO - to co už je

		$data = array(
			'managers' => $campaignManagers,
		);

		$response = new JsonResponse($data);

		return $response;

	}

	/**
     * @Route("/client/{client_id}/campaign/create/")
     * @Template()
     */
    public function createAction($client_id, Request $request)
    {

	    $em = $this->getDoctrine()->getManager();

	    $client = $em->getRepository('AppBundle:Client')->find($client_id);

	    if(!$client){
		    $this->addFlash('danger', 'Tento klient neexistuje.');
		    return $this->redirectToRoute('app_client_list');
	    }

	    $user = $this->getUser();
	    $companies = $user->getCompaniesEnabled();
	    $company = $client->getCompany();

	    //Kontakt je ve společnosti, do které uživatel nemá přístup
	    if(!$companies->contains($company)){
		    $this->addFlash('danger', 'K tomuto klientu nemáte přístup.');
		    return $this->redirectToRoute('app_client_list');
	    }

	    $authorizationChecker = new AuthorizationChecker($em);
	    $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('campaign-create', $user, $company);

	    if(!$check){
		    $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění vytvářet kampaně.');

		    $params = [
			    'client_id' => $client_id,
		    ];
		    return $this->redirectToRoute('app_client_detail', $params);
	    }

	    $entity = new Campaign();

        $type = new CampaignFormType($client);

        $form = $this->createForm($type, $entity);

	    $form->handleRequest($request);

	    if($form->isValid()){

		    $authorizationChecker = new AuthorizationChecker($em);
		    $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('campaign-create', $user, $company);

		    if(!$check){
			    $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění vytvářet kampaně.');

			    $params = [
				    'client_id' => $client_id,
			    ];
			    return $this->redirectToRoute('app_client_detail', $params);
		    }

		    $entity->setClient($client);
		    $entity->setName($entity->generateName());

		    $em->persist($entity);
		    $em->flush();

		    $this->addFlash('success', 'Kampaň byla vytvořena.');

		    $params = [
			    'client_id' => $client_id,
		    ];
		    return $this->redirectToRoute('app_client_detail', $params);

	    }

        $data = array(
            'form' => $form->createView(),
	        'client' => $client,
        );

        return $data;
    }


	/**
	 * @Route("/campaign/{campaign_id}/detail/")
	 * @Template()
	 */
	public function detailAction($campaign_id)
	{

		$em = $this->getDoctrine()->getManager();

		$campaign = $em->getRepository('AppBundle:Campaign')->find($campaign_id);

		if(!$campaign){
			$this->addFlash('danger', 'Tato kampaň neexistuje.');
			return $this->redirectToRoute('app_client_list');
		}

		$user = $this->getUser();
		$companies = $user->getCompaniesEnabled();
		$company = $campaign->getClient()->getCompany();

		//Kontakt je ve společnosti, do které uživatel nemá přístup
		if(!$companies->contains($company)){
			$this->addFlash('danger', 'K této kampani nemáte přístup.');
			return $this->redirectToRoute('app_client_list');
		}

		$authorizationChecker = new AuthorizationChecker($em);
		$check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('campaign-read', $user, $company);

		if(!$check){
			$this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění zobrazovat si detaily kampaní.');

			return $this->redirectToRoute('app_client_list');
		}

		$campaignDO = new CRUDLDataObject();
		$campaignDO->setData($campaign);

		$canDelete = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('campaign-delete', $user, $company);
		$canUpdate = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('campaign-update', $user, $company);

		$campaignDO->setCanDelete($canDelete);
		$campaignDO->setCanUpdate($canUpdate);

		$canCreateJob = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('commission-create', $user, $company);

		$commissionManager = new CommissionManager($em);
		$commissions = $commissionManager->getCommissionsWhereParticipate($user);


		$commissionDOs = [];
		$items = [];

		/** @var Commission $commission */
		foreach($commissions as $commission){

			if($commission->getCampaign() !== $campaign){
				continue;
			}

			if(in_array($commission, $items, true)){
				continue;
			}

			$commissionDO = new CRUDLDataObject();
			$commissionDO->setData($commission);
			$commissionDOs[] = $commissionDO;

			$items[] = $commission;

		}


		$data = array(
			'campaign' => $campaignDO,
			'canCreateJob' => $canCreateJob,
			'commissions' => $commissionDOs,
		);

		return $data;
	}

	/**
	 * @Route("/campaign/{campaign_id}/delete/")
	 * @Template()
	 */
	public function deleteAction($campaign_id, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('AppBundle:Campaign')->find($campaign_id);

		if(!$entity){
			$this->addFlash('danger', 'Tato kampaň neexistuje.');
			return $this->redirectToRoute('app_client_list');
		}

		$user = $this->getUser();
		$companies = $user->getCompaniesEnabled();
		$company = $entity->getClient()->getCompany();

		//Kontakt je ve společnosti, do které uživatel nemá přístup
		if(!$companies->contains($company)){
			$this->addFlash('danger', 'K této kampani nemáte přístup.');
			return $this->redirectToRoute('app_client_list');
		}

		$authorizationChecker = new AuthorizationChecker($em);
		$check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('campaign-update', $user, $company);

		if(!$check){
			$this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění editovat kampaně.');

			return $this->redirectToRoute('app_client_list');
		}

		if($entity->getCommissions()->count() > 0){
			$this->addFlash('danger', 'Tuto kampaň nelze odstranit.');

			$params = [
				'client_id' => $entity->getClient()->getId(),
			];
			return $this->redirectToRoute('app_client_detail', $params);
		}

		$em->remove($entity);

		$em->flush();

		$this->addFlash('success', 'Kampaň byla odstraněna.');

		$params = [
			'client_id' => $entity->getClient()->getId(),
		];
		return $this->redirectToRoute('app_client_detail', $params);


	}

    /**
     * @Route("/campaign/{campaign_id}/update/")
     * @Template()
     */
    public function updateAction($campaign_id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Campaign')->find($campaign_id);

        if(!$entity){
            $this->addFlash('danger', 'Tato kampaň neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $user = $this->getUser();
        $companies = $user->getCompaniesEnabled();
        $company = $entity->getClient()->getCompany();

        //Kontakt je ve společnosti, do které uživatel nemá přístup
        if(!$companies->contains($company)){
            $this->addFlash('danger', 'K této kampani nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

	    $authorizationChecker = new AuthorizationChecker($em);
	    $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('campaign-update', $user, $company);

	    if(!$check){
		    $this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte oprávnění editovat kampaně.');

		    return $this->redirectToRoute('app_client_list');
	    }

//	    $authorizationIndividual = new AuthorizationIndividual($em);
//	    $companies = $authorizationIndividual->getCompaniesWhereUserHasAuthorizationCode('campaign-update', $user);

	    $type = new CampaignFormType($entity->getClient());

	    $form = $this->createForm($type, $entity, array(
		    'method' => 'post',
	    ));

	    $entity->setContactPersonList(null);
	    $entity->setSourceList(null);
	    /** @var CampaignManager $campaignManager */
	    foreach($entity->getCampaignManagers() as $campaignManager){
		    $campaignManager->setCampaign(null);
	    }

	    $form->handleRequest($request);

	    if($form->isValid()){

		    $data = $form->getData()->getCampaignManagers();
            /** @var CampaignManager $campaignManager */
		    foreach($data as $campaignManager){
			    $campaignManager->setCampaign($entity);
			    $entity->addCampaignManager($campaignManager);
		    }

		    $em->persist($entity);
		    $em->flush();

		    $this->addFlash('success', 'Kampaň byla upravena.');

		    $params = [
			    'campaign_id' => $campaign_id,
		    ];
		    return $this->redirectToRoute('app_campaign_detail', $params);

	    }

	    $data = array(
		    'form' => $form->createView(),
		    'campaign' => $entity,
		    'client' => $entity->getClient(),
	    );

	    return $data;
    }

//
//    /**
//     * @Route("/contact/{contact_id}/delete/")
//     * @Template()
//     */
//	public function deleteAction($contact_id, Request $request)
//	{
//
//		$em = $this->getDoctrine()->getManager();
//
//		$entity = $em->getRepository('AppBundle:Contact')->find($contact_id);
//
//		if(!$entity){
//			$this->addFlash('danger', 'Tento kontakt neexistuje.');
//			return $this->redirectToRoute('app_contact_list');
//		}
//
//		$user = $this->getUser();
//		$companies = $user->getCompaniesEnabled();
//		$company = $entity->getCompany();
//
//		//Kontakt je ve společnosti, do které uživatel nemá přístup
//		if(!$companies->contains($company)){
//			$this->addFlash('danger', 'K tomuto kontaktu nemáte přístup.');
//			return $this->redirectToRoute('app_contact_list');
//		}
//
//		$ac = new AuthorizationChecker($em);
//		$check = $ac->checkAuthorizationCodeOfUserInCompany('contact-delete', $user, $company);
//		if(!$check){
//			$this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte právo odstraňovat kontakty.');
//			return $this->redirectToRoute('app_contact_list');
//		}
//
//		if($entity->getCommissions()->count() !== 0){
//
//			$this->addFlash('danger', 'Kontakt nemůže být odstraněn, protože již má evidované zakázky.');
//			return $this->redirectToRoute('app_contact_list');
//
//		}
//
//		//TODO - kontakt může být odstraněn - nemá náklady, apod.
//
//		$em->remove($entity);
//		$em->flush();
//
//		$this->addFlash('success', 'Kontakt byl odstraněn.');
//
//		return $this->redirectToRoute('app_contact_list');
//
//	}

}
