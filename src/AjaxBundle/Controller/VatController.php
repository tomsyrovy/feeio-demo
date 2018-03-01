<?php

namespace AjaxBundle\Controller;

use AppBundle\DependencyInjection\ContactInfoDownloader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class VatController extends Controller
{
    /**
     * @Route("/vat/load/")
     */
    public function loadAction(Request $request)
    {

        $ico = $request->get("ico");

        $a = ContactInfoDownloader::getInfoAboutContact($ico);

	    return new JsonResponse($a);

    }

}
