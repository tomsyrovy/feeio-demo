<?php

namespace AppBundle\Controller;
use AppBundle\DependencyInjection\Widget\WidgetManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Widget controller.
 *
 */
class WidgetController extends Controller
{

    /**
     * @Template()
     */
    public function showAction(array $params){

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $widgetManager = new WidgetManager($em);
        $widgetDOs = $widgetManager->getWidgetDOs($user, $params);

        $data = [
            'widgetDOs' => $widgetDOs,
        ];

        return $data;

    }

}
