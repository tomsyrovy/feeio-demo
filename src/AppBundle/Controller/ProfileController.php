<?php

namespace AppBundle\Controller;

use AppBundle\DependencyInjection\ImageCreator\ImageCreator;
use AppBundle\DependencyInjection\Invitator\InvitatorService;
use AppBundle\Form\Type\ProfileChangeEmailFormType;
use AppBundle\Form\Type\ProfileImageFormType;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Form\Type\ProfileFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends BaseController
{

    /**
     * @Route("/profile/invitations/")
     * @Template()
     */
    public function invitationsAction(){

        $invitatorService = new InvitatorService($this);
        $invitations = $invitatorService->getInvitationsByUser($this->getUser());

        $data = array(
            "invitations" => $invitations,
        );

        return $data;

    }

    /**
     * @Route("/profile/create")
     * @Template()
     */
    public function createAction(Request $request){

        if(!$this->checkLogin()){
            return $this->redirect($this->generateUrl("fos_user_security_login"));
        }

        $profileFormType = new ProfileFormType();
        $user = $this->getUser();

        $form = $this->createForm($profileFormType, $user, array(
            "action" => $this->generateUrl("app_profile_create"),
            "method" => "post",
        ));

        $form->handleRequest($request);

        if($form->isValid()){

            $em = $this->getDoctrine()->getManager();

            $user->setSignupAt(new \DateTime());

            $text = mb_substr($user->getFirstname(), 0, 1, 'UTF-8').mb_substr($user->getLastname(), 0, 1, 'UTF-8');
            $imageCreator = new ImageCreator();
            $image = $imageCreator->getImage($text);

            $user->setImage($image);
            $em->persist($image);
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "Účet byl vytvořen.");

            return $this->redirect($this->generateUrl("app_default_index"));

        }

        return array(
            "form" => $form->createView(),
        );

    }

    /**
     * @Route("/profile/edit")
     * @Template()
     */
    public function editAction(Request $request){

        if(!$this->checkLogin()){
            return $this->redirect($this->generateUrl("fos_user_security_login"));
        }

        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        //Formulář pro profil
        $profileFormType = new ProfileFormType();
        $form = $this->createForm($profileFormType, $user);
        $form->handleRequest($request);
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "Profil byl uložen.");
            return $this->redirect($this->generateUrl("app_default_index"));
        }

        //Formulář pro profilový obrázek
        $profileImageFormType = new ProfileImageFormType();
        $profileImageForm = $this->createForm($profileImageFormType, $user);
        $profileImageForm->handleRequest($request);
        if($profileImageForm->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "Profilový obrázek byl uložen.");
            return $this->redirect($this->generateUrl("app_default_index"));
        }

        return array(
            "form" => $form->createView(),
            "profileImageForm" => $profileImageForm->createView(),
            "user" => $user,
        );

    }

    /**
     * @Route("/profile/change-email")
     * @Template()
     */
    public function changeEmailAction(Request $request){

        if(!$this->checkLogin()){
            return $this->redirect($this->generateUrl("fos_user_security_login"));
        }

        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $formType = new ProfileChangeEmailFormType(true);

        $form = $this->createForm($formType, $user);

        $form->handleRequest($request);

        if($form->isValid()){

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "E-mail byl upraven.");
            return $this->redirect($this->generateUrl("app_default_index"));

        }

        return array(
            "form" => $form->createView(),
        );

    }

}
