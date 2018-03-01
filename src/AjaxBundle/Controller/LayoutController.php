<?php

namespace AjaxBundle\Controller;

use AppBundle\DependencyInjection\ContactInfoDownloader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LayoutController extends Controller
{
    /**
     * @Route("/changeLayout/")
     */
    public function changeAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $currentLayout = $request->get("currentLayout");

        $user = $this->getUser();

        $user->setCurrentLayout($currentLayout);

        $em->persist($user);
        $em->flush();

        $a = [
            'status' => 200
        ];

	    return new JsonResponse($a);

    }

}
