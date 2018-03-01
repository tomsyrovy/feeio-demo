<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AdminController extends Controller
{
    /**
     * @Route("/admin/users/")
     * @Template()
     *
     * @return array
     */
    public function userlistAction()
    {

        $session = new Session();

        if(!$session->has('admin')){

            $user = $this->getUser();

            if(!$user->getAdmin()){

                return $this->redirectToRoute('app_default_index');

            }else{

                $session->set('admin', $user->getId());

            }

        }

        if(!$session->has('admin')){
            return $this->redirectToRoute('app_default_index');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($session->get('admin'));
        if(!$user OR !$user->getAdmin()){

            return $this->redirectToRoute('app_default_index');

        }


        $users = $em->getRepository('UserBundle:User')->findBy([], ['lastname' => 'ASC']);

        $data = [
            'users' => $users
        ];

        return $data;
    }

    /**
     * @Route("/admin/user/{user_id}/switch/")
     *
     * @return array
     */
    public function userswitchAction($user_id)
    {

        $session = new Session();

        if(!$session->has('admin')){
            return $this->redirectToRoute('app_default_index');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($session->get('admin'));
        if(!$user OR !$user->getAdmin()){

            return $this->redirectToRoute('app_default_index');

        }

        $criteria = array(
            'id' => $user_id,
        );
        $user = $em->getRepository('UserBundle:User')->findOneBy($criteria);

        if(!$user){

            $this->addFlash('danger', 'Tento uživatel neexistuje.');
            return $this->redirectToRoute('app_admin_userlist');

        }

        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $this->get("security.context")->setToken($token); //now the user is logged in

        //now dispatch the login event
        $request = $this->get("request");
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

        $this->addFlash('success', 'Nyní jste přihlášen jako '.$user->getFullName().'.');
        return $this->redirectToRoute('app_default_index');
    }

    /**
     * @Route("/admin/logout/")
     *
     * @return array
     */
    public function logoutAction()
    {

        $session = new Session();

        if(!$session->has('admin')){
            return $this->redirectToRoute('app_default_index');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($session->get('admin'));
        if(!$user OR !$user->getAdmin()){

            return $this->redirectToRoute('app_default_index');

        }

        $session->remove('admin');

        return $this->redirectToRoute('fos_user_security_logout');

    }

}
