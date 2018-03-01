<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class FragmentController extends Controller
{
    /**
     * @Template()
     */
    public function navbarprofileAction()
    {

        $data = array(
            'user' => $this->getUser(),
        );

        return $data;

    }

}
