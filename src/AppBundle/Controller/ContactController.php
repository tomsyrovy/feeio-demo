<?php

namespace AppBundle\Controller;

use AppBundle\DataObject\CRUDLDataObject;
use AppBundle\DependencyInjection\Authorization\AuthorizationChecker;
use AppBundle\DependencyInjection\Authorization\AuthorizationCompany;
use AppBundle\DependencyInjection\Authorization\AuthorizationIndividual;
use AppBundle\Entity\Contact;
use AppBundle\Form\Type\ContactFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;
use UserBundle\Entity\User;

class ContactController extends Controller
{
    /**
     * @Route("/contact/create/")
     * @Template()
     */
    public function createAction(Request $request)
    {

	    $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entity = new Contact();

	    $authorizationIndividual = new AuthorizationIndividual($em);
	    $companies = $authorizationIndividual->getCompaniesWhereUserHasAuthorizationCode('contact-create', $user);

	    if($companies->count() == 0){
		    $this->addFlash('danger', 'Nemáte oprávnění vytvářet kontakty.');

		    return $this->redirectToRoute('app_contact_list');
	    }

        $type = new ContactFormType($user, $this->generateUrl('ajax_vat_load'), $companies);

        $form = $this->createForm($type, $entity, array(
            'method' => 'post'
        ));

	    $form->handleRequest($request);

	    if($form->isValid()){

		    $authorizationChecker = new AuthorizationChecker($em);
		    $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('contact-create', $user, $entity->getCompany());

		    if(!$check){
			    $this->addFlash('danger', 'Ve společnosti '.$entity->getCompany()->getName().' nemáte oprávnění vytvářet kontakty.');

			    return $this->redirectToRoute('app_contact_create');
		    }

		    $entity->setEnabled(true);
		    $entity->setClosed(false);

		    $em->persist($entity);
		    $em->flush();

		    if($request->request->has('submit_and_more')){
			    $this->addFlash('success', 'Kontakt byl vytvořen. Můžete vytvořit další.');
			    return $this->redirectToRoute('app_contact_create');
		    }else{
			    $this->addFlash('success', 'Kontakt byl vytvořen.');
			    return $this->redirectToRoute('app_contact_list');
		    }

	    }

        $data = array(
            'form' => $form->createView(),
        );

        return $data;
    }

    /**
     * @Route("/contacts/")
     * @Template()
     */
    public function listAction()
    {

	    $user = $this->getUser();
	    $companies = $user->getCompaniesEnabled();
	    $em = $this->getDoctrine()->getManager();

        $authorizationChecker = new AuthorizationChecker($em);

	    $contacts = new ArrayCollection();

	    foreach($companies as $company){
		    foreach($company->getContacts() as $contact){

			    $contactDO = new CRUDLDataObject();
			    $contactDO->setData($contact);

			    $canCreate = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('contact-create', $user, $company);
			    $canRead = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('contact-read', $user, $company);
			    $canUpdate = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('contact-update', $user, $company);
			    $canDelete = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('contact-delete', $user, $company);
			    $canList = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('contact-list', $user, $company);

			    if($canList){

				    $canDelete = false; //TODO

				    $contactDO->setCanCreate($canCreate);
				    $contactDO->setCanRead($canRead);
				    $contactDO->setCanUpdate($canUpdate);
				    $contactDO->setCanDelete($canDelete);

				    $contacts->add($contactDO);
			    }
		    }
	    }

	    $authorizationIndividual = new AuthorizationIndividual($em);
	    $companies = $authorizationIndividual->getCompaniesWhereUserHasAuthorizationCode('contact-create', $user);
	    $canCreateContactBtn = $companies->count() !== 0;

	    $data = array(
		    'contacts' => $contacts,
		    'canCreateContactBtn' => $canCreateContactBtn,
	    );

	    return $data;
    }

	/**
	 * @Route("/contact/{contact_id}/detail/")
	 * @Template()
	 */
	public function detailAction($contact_id)
	{

		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('AppBundle:Contact')->find($contact_id);

		if(!$entity){
			$this->addFlash('danger', 'Tento kontakt neexistuje.');
			return $this->redirectToRoute('app_contact_list');
		}

		$user = $this->getUser();
		$companies = $user->getCompaniesEnabled();
		$company = $entity->getCompany();

		//Kontakt je ve společnosti, do které uživatel nemá přístup
		if(!$companies->contains($company)){
			$this->addFlash('danger', 'K tomuto kontaktu nemáte přístup.');
			return $this->redirectToRoute('app_contact_list');
		}

		$authorizationChecker = new AuthorizationChecker($em);
		$check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('contact-read', $user, $entity->getCompany());

		if(!$check){
			$this->addFlash('danger', 'Ve společnosti '.$entity->getCompany()->getName().' nemáte oprávnění zobrazovat si detaily kontaktů.');

			return $this->redirectToRoute('app_contact_list');
		}

		$contactDO = new CRUDLDataObject();
		$contactDO->setData($entity);

		$canDelete = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('contact-delete', $user, $company);

		$contactDO->setCanDelete($canDelete);


		$data = array(
			'contact' => $contactDO
		);

		return $data;
	}

    /**
     * @Route("/contact/{contact_id}/update/")
     * @Template()
     */
    public function updateAction($contact_id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Contact')->find($contact_id);

        if(!$entity){
            $this->addFlash('danger', 'Tento kontakt neexistuje.');
            return $this->redirectToRoute('app_contact_list');
        }

        $user = $this->getUser();
        $companies = $user->getCompaniesEnabled();
        $company = $entity->getCompany();

        //Kontakt je ve společnosti, do které uživatel nemá přístup
        if(!$companies->contains($company)){
            $this->addFlash('danger', 'K tomuto kontaktu nemáte přístup.');
            return $this->redirectToRoute('app_contact_list');
        }

	    $authorizationChecker = new AuthorizationChecker($em);
	    $check = $authorizationChecker->checkAuthorizationCodeOfUserInCompany('contact-update', $user, $entity->getCompany());

	    if(!$check){
		    $this->addFlash('danger', 'Ve společnosti '.$entity->getCompany()->getName().' nemáte oprávnění editovat kontakty.');

		    return $this->redirectToRoute('app_contact_list');
	    }

	    $authorizationIndividual = new AuthorizationIndividual($em);
	    $companies = $authorizationIndividual->getCompaniesWhereUserHasAuthorizationCode('contact-update', $user);

	    $type = new ContactFormType($user, $this->generateUrl('ajax_vat_load'), $companies);

	    $form = $this->createForm($type, $entity, array(
		    'method' => 'post'
	    ));

	    $form->handleRequest($request);

	    if($form->isValid()){

		    $em->persist($entity);
		    $em->flush();

		    $this->addFlash('success', 'Kontakt byl upraven.');

		    return $this->redirectToRoute('app_contact_list');

	    }

	    $data = array(
		    'form' => $form->createView(),
	    );

	    return $data;
    }

    /**
     * @Route("/contact/{contact_id}/delete/")
     * @Template()
     */
	public function deleteAction($contact_id, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('AppBundle:Contact')->find($contact_id);

		if(!$entity){
			$this->addFlash('danger', 'Tento kontakt neexistuje.');
			return $this->redirectToRoute('app_contact_list');
		}

		$user = $this->getUser();
		$companies = $user->getCompaniesEnabled();
		$company = $entity->getCompany();

		//Kontakt je ve společnosti, do které uživatel nemá přístup
		if(!$companies->contains($company)){
			$this->addFlash('danger', 'K tomuto kontaktu nemáte přístup.');
			return $this->redirectToRoute('app_contact_list');
		}

		$ac = new AuthorizationChecker($em);
		$check = $ac->checkAuthorizationCodeOfUserInCompany('contact-delete', $user, $company);
		if(!$check){
			$this->addFlash('danger', 'Ve společnosti '.$company->getName().' nemáte právo odstraňovat kontakty.');
			return $this->redirectToRoute('app_contact_list');
		}

		if($entity->getCommissions()->count() !== 0){

			$this->addFlash('danger', 'Kontakt nemůže být odstraněn, protože již má evidované zakázky.');
			return $this->redirectToRoute('app_contact_list');

		}

		//TODO - kontakt může být odstraněn - nemá náklady, apod.

		$em->remove($entity);
		$em->flush();

		$this->addFlash('success', 'Kontakt byl odstraněn.');

		return $this->redirectToRoute('app_contact_list');

	}

}
