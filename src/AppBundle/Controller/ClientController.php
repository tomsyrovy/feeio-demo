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
use AppBundle\Entity\Source;
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

class ClientController extends Controller
{
	/**
	 * @route("/api/sourcelist", name="app.sourcelist.get")
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *
	 */
	public function sourcelistAction(Request $request){

		$em = $this->getDoctrine()->getManager();

		$jps = [];

		$company_id = $request->request->get('company_id');
		if($company_id){
			$company = $em->getRepository('AppBundle:Company')->find($company_id);
			if($company){
				$jps = $company->getJobPositionsEnabled();

				$jobPositions = [];
				/** @var JobPosition $jp */
				foreach($jps as $jp){
					$jobPositions[] = [
						'id' => $jp->getId(),
						'name' => $jp->getName(),
						'rateExternal' => $jp->getExternalRate(),
					];
				}

			}
		}

		$client_id = $request->request->get('client_id');
		if($client_id){
			$client = $em->getRepository('AppBundle:Client')->find($client_id);
			if($client){
				$sources = $client->getSourceList()->getSources();

				$jobPositions = [];
				/** @var Source $source */
				foreach($sources as $source){
					$jobPositions[] = [
						'id' => $source->getJobPosition()->getId(),
						'name' => $source->getJobPosition()->getName(),
						'rateExternal' => $source->getRateExternal(),
					];
				}

			}
		}


		//TODO - to co už je

		$data = array(
			'jobPositions' => $jobPositions,
		);

		$response = new JsonResponse($data);

		return $response;

	}

	/**
     * @Route("/client/create/")
     * @Template()
     */
    public function createAction(Request $request)
    {

	    $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entity = new Client();

	    $authorizationIndividual = new AuthorizationIndividual($em);
	    $companies = $authorizationIndividual->getCompaniesWhereUserHasAuthorizationCode('client-create', $user);

	    if($companies->count() == 0){
		    $this->addFlash('danger', 'Nemáte oprávnění vytvářet klienty.');

		    return $this->redirectToRoute('app_client_list');
	    }

        $type = new ClientFormType($companies);

        $form = $this->createForm($type, $entity);

	    $form->handleRequest($request);

	    if($form->isValid()){

		    $authorizationChecker = new AuthorizationChecker($em);
		    $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('client-create', $user, $entity->getContact()->getCompany());

		    if(!$check){
			    $this->addFlash('danger', 'Ve společnosti '.$entity->getContact()->getCompany()->getName().' nemáte oprávnění vytvářet klienty.');

			    return $this->redirectToRoute('app_client_create');
		    }

		    $entity->setAuthor($this->getUser());

		    $em->persist($entity);
		    $em->flush();

		    $this->addFlash('success', 'Klient byl vytvořen.');
		    return $this->redirectToRoute('app_client_list');

	    }

        $data = array(
            'form' => $form->createView(),
        );

        return $data;
    }

    /**
     * @Route("/clients/")
     * @Template()
     */
    public function listAction()
    {
	    $user = $this->getUser();
	    $companies = $user->getCompaniesEnabled();
	    $em = $this->getDoctrine()->getManager();

        $authorizationChecker = new AuthorizationChecker($em);

	    $items = [];

	    /** @var Company $company */
	    foreach($companies as $company){
		    $criteria = [
			    'user' => $user,
			    'company' => $company,
		    ];
		    $userCompany = $em->getRepository('AppBundle:UserCompany')->findOneBy($criteria);
		    if(!$userCompany){
			    continue;
		    }
		    $uct = $userCompany->getData();

		    /** @var Client $client */
		    foreach($company->getClients() as $client){

			    if($client->getEnabled()){

				    //Jsi-li člověkem ve společnosti v ne-editovatelný roli, nebo jsi autorem klienta
				    if($uct->getRole()->getNoneditable() or $client->getAuthor() === $user){

					    $items[] = $client;

				    }else{

					    /** @var Campaign $campaign */
					    foreach($client->getCampaigns() as $campaign){

						    $criteria = [
							    'userCompany' => $userCompany,
							    'campaign' => $campaign,
							    'owner' => true
						    ];
						    $check = $em->getRepository('AppBundle:CampaignManager')->findOneBy($criteria);
						    if($check){
							    if(in_array($client, $items, true)){
								    continue;
							    }
							    $items[] = $client;
						    }

						    $isCgAdmin = false;
						    $cgAdmins = $campaign->getCompanyGroup()->getCompanyGroupUserCompanyRelationsOfTemporalityStatus('admin');
						    /** @var CompanyGroupUserCompanyRelation $cgAdmin */
						    foreach($cgAdmins as $cgAdmin){
							    if($cgAdmin->getUserCompany()->getUser() === $user){
								    $isCgAdmin = true;
								    break 1;
							    }
						    }
						    if($isCgAdmin){
							    if(in_array($client, $items, true)){
								    continue;
							    }
							    $items[] = $client;
							    continue;
						    }

					    }

				    }

			    }

		    }

	    }

	    $commissionManager = new CommissionManager($em);
	    $commissions = $commissionManager->getCommissionsWhereParticipate($user);

	    /** @var Commission $commission */
	    foreach($commissions as $commission){

		    $client = $commission->getCampaign()->getClient();

		    if(in_array($client, $items, true)){
			    continue;
		    }

		    $items[] = $client;

	    }

	    $clients = [];
	    /** @var Client $client */
	    foreach($items as $client){

		    $company = $client->getCompany();

		    $clientDO = new CRUDLDataObject();
		    $clientDO->setData($client);

		    $canCreate = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('client-create', $user, $company);
		    $canRead = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('client-read', $user, $company);
		    $canUpdate = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('client-update', $user, $company);
		    $canDelete = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('client-delete', $user, $company);
		    $canList = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('client-list', $user, $company);

		    if($canList){

			    $canDelete = false; //TODO - podle oprávnění a podle potomků

			    $clientDO->setCanCreate($canCreate);
			    $clientDO->setCanRead($canRead);
			    $clientDO->setCanUpdate($canUpdate);
			    $clientDO->setCanDelete($canDelete);

			    $clients[] = $clientDO;
		    }

	    }

	    $authorizationIndividual = new AuthorizationIndividual($em);
	    $companies = $authorizationIndividual->getCompaniesWhereUserHasAuthorizationCode('client-create', $user);
	    $canCreateContactBtn = $companies->count() !== 0;

	    $data = array(
		    'clients' => $clients,
		    'canCreateContactBtn' => $canCreateContactBtn,
	    );

	    return $data;
    }

	/**
	 * @Route("/client/{client_id}/detail/")
	 * @Template()
	 */
	public function detailAction($client_id)
	{

		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('AppBundle:Client')->find($client_id);

		if(!$entity){
			$this->addFlash('danger', 'Tento klient neexistuje.');
			return $this->redirectToRoute('app_client_list');
		}

		$user = $this->getUser();
		$companies = $user->getCompaniesEnabled();
		$company = $entity->getCompany();

		//Kontakt je ve společnosti, do které uživatel nemá přístup
		if(!$companies->contains($company)){
			$this->addFlash('danger', 'K tomuto klientu nemáte přístup.');
			return $this->redirectToRoute('app_client_list');
		}

		$authorizationChecker = new AuthorizationChecker($em);
		$check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('client-read', $user, $entity->getCompany());

		if(!$check){
			$this->addFlash('danger', 'Ve společnosti '.$entity->getCompany()->getName().' nemáte oprávnění zobrazovat si detaily klientů.');

			return $this->redirectToRoute('app_client_list');
		}

		$clientDO = new CRUDLDataObject();
		$clientDO->setData($entity);

		$canDelete = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('client-delete', $user, $company);
		$canUpdate = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('client-update', $user, $company);

		$clientDO->setCanDelete($canDelete);
		$clientDO->setCanUpdate($canUpdate);

		$canCreateCampaign = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('campaign-create', $user, $company);

		$items = [];

		/** @var Company $company */
		foreach($companies as $company){
			$criteria = [
				'user' => $user,
				'company' => $company,
			];
			$userCompany = $em->getRepository('AppBundle:UserCompany')->findOneBy($criteria);
			if(!$userCompany){
				continue;
			}
			$uct = $userCompany->getData();

			/** @var Client $client */
			foreach($company->getClients() as $client){

				if($client->getEnabled()){

					/** @var Campaign $campaign */
					foreach($client->getCampaigns() as $campaign){

						if($campaign->getEnabled()){

							if($campaign->getClient() !== $entity){
								continue;
							}

							if($uct->getRole()->getNoneditable()){

								$items[] = $campaign;

							}else{

								//Nebo jsi-li adminem v pracovní skupině kampaně pod který spadá job
								$isCgAdmin = false;
								$cgAdmins = $campaign->getCompanyGroup()->getCompanyGroupUserCompanyRelationsOfTemporalityStatus('admin');
								/** @var CompanyGroupUserCompanyRelation $cgAdmin */
								foreach($cgAdmins as $cgAdmin){
									if($cgAdmin->getUserCompany()->getUser() === $user){
										$isCgAdmin = true;
										break 1;
									}
								}
								if($isCgAdmin){
									$items[] = $campaign;
									continue;
								}

								//Nebo jsi-li ownerem, či jobmanagerem kampaně pod kterou spadá job
								/** @var CampaignManager $campaignManager */
								foreach($campaign->getCampaignManagers() as $campaignManager){
									if($campaignManager->getUserCompany()->getUser() === $user){
										if($campaignManager->getJobManager() or $campaignManager->getOwner()){
											$items[] = $campaign;
										}
									}
								}

							}

						}

					}

				}

			}


		}

//		$commissionManager = new CommissionManager($em);
//		$commissions = $commissionManager->getCommissionsWhereParticipate($user);
//
//		/** @var Commission $commission */
//		foreach($commissions as $commission){
//
//			$campaign = $commission->getCampaign();
//
//			if($campaign->getClient() !== $entity){
//				continue;
//			}
//
//			if(in_array($campaign, $items, true)){
//				continue;
//			}
//
//			$items[] = $campaign;
//
//		}

		$campaignDOs = [];
		/** @var Campaign $campaign */
		foreach($items as $campaign){

			$campaignDO = new CRUDLDataObject();
			$campaignDO->setData($campaign);
			$campaignDOs[] = $campaignDO;

		}

		$data = array(
			'client' => $clientDO,
			'canCreateCampaign' => $canCreateCampaign,
			'campaigns' => $campaignDOs,
		);

		return $data;
	}

	/**
	 * @Route("/client/{client_id}/delete/")
	 * @Template()
	 */
	public function deleteAction($client_id, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		/** @var Client $entity */
		$entity = $em->getRepository('AppBundle:Client')->find($client_id);

		if(!$entity){
			$this->addFlash('danger', 'Tento klient neexistuje.');
			return $this->redirectToRoute('app_client_list');
		}

		$user = $this->getUser();
		$companies = $user->getCompaniesEnabled();
		$company = $entity->getCompany();

		//Kontakt je ve společnosti, do které uživatel nemá přístup
		if(!$companies->contains($company)){
			$this->addFlash('danger', 'K tomuto klientu nemáte přístup.');
			return $this->redirectToRoute('app_client_list');
		}

		$authorizationChecker = new AuthorizationChecker($em);
		$check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('client-update', $user, $entity->getCompany());

		if(!$check){
			$this->addFlash('danger', 'Ve společnosti '.$entity->getCompany()->getName().' nemáte oprávnění editovat klienty.');

			return $this->redirectToRoute('app_client_list');
		}

		if($entity->getCampaigns()->count() > 0){
			$this->addFlash('danger', 'Tohoto klienta nelze odstranit.');

			return $this->redirectToRoute('app_client_list');
		}

		$em->remove($entity);

		$em->flush();

		$this->addFlash('success', 'Klient byl odstraněn.');

		return $this->redirectToRoute('app_client_list');
	}

    /**
     * @Route("/client/{client_id}/update/")
     * @Template()
     */
    public function updateAction($client_id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

	    /** @var Client $entity */
        $entity = $em->getRepository('AppBundle:Client')->find($client_id);

        if(!$entity){
            $this->addFlash('danger', 'Tento klient neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $user = $this->getUser();
        $companies = $user->getCompaniesEnabled();
        $company = $entity->getCompany();

        //Kontakt je ve společnosti, do které uživatel nemá přístup
        if(!$companies->contains($company)){
            $this->addFlash('danger', 'K tomuto klientu nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

	    $authorizationChecker = new AuthorizationChecker($em);
	    $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('client-update', $user, $entity->getCompany());

	    if(!$check){
		    $this->addFlash('danger', 'Ve společnosti '.$entity->getCompany()->getName().' nemáte oprávnění editovat klienty.');

		    return $this->redirectToRoute('app_client_list');
	    }

	    $authorizationIndividual = new AuthorizationIndividual($em);
	    $companies = $authorizationIndividual->getCompaniesWhereUserHasAuthorizationCode('client-update', $user);

	    $type = new ClientFormType($companies);

	    $form = $this->createForm($type, $entity, array(
		    'method' => 'post',
	    ));

	    $entity->setContactPersonList(null);
	    $entity->setSourceList(null);

	    $form->handleRequest($request);

	    if($form->isValid()){

		    $em->persist($entity);
		    $em->flush();

		    //TODO - opravit odstraňování

		    $this->addFlash('success', 'Klient byl upraven.');

		    $params = [
			    'client_id' => $client_id,
		    ];
		    return $this->redirectToRoute('app_client_detail', $params);

	    }

	    $data = array(
		    'form' => $form->createView(),
		    'client' => $entity,
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
