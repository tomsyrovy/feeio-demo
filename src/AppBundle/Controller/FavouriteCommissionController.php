<?php

namespace AppBundle\Controller;

use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
use AppBundle\Entity\FavouriteActivity;
use AppBundle\Entity\FavouriteCommission;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;

/**
 * FavouriteCommission controller.
 */
class FavouriteCommissionController extends Controller
{

    /**
     * @Route("/favourite/commissions/", name="app_favourite_commission_index")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        //Vybrané záznamy
        $criteria = [
            'user' => $user,
        ];
        $orderBy = [
            'sort' => 'ASC',
        ];
        $commissions = $em->getRepository('AppBundle:FavouriteCommission')->findBy($criteria, $orderBy);

        $commissionsChosen = [];
        foreach($commissions as $commission){

            $key = $commission->getCommission()->getId();
            $value = $commission->getCommission()->getName();

            if(!array_key_exists($key, $commissionsChosen)){
                $commissionsChosen[$key] = $value;
            }

        }

        //Záznamy k výběru
        $commissionManager = new CommissionManager($em);
        $commissions = $commissionManager->getCommissionsOfUserInAllCompaniesWhereIsEnabled($user);
        $commissionsToChoose = [];
        foreach($commissions as $commission){

            $key = $commission->getId();
            $value = $commission->getName();

            if(!array_key_exists($key, $commissionsToChoose) and !array_key_exists($key, $commissionsChosen)){
                $commissionsToChoose[$key] = $value;
            }

        }
        asort($commissionsToChoose); //keep keys

        return array(
            'commissionsChosen' => $commissionsChosen,
            'commissionsToChoose' => $commissionsToChoose,
        );
    }

    /**
     * @Route("/favourite/commissions/save/", name="app_favourite_commission_save")
     */
    public function saveAction(Request $request){

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        //Odstranění všech uložených záznamů uživatele
        $commissions = $user->getFavouriteCommissions();
        foreach($commissions as $commission){
            $em->remove($commission);
            $user->removeFavouriteCommission($commission);
        }

        //Uložení nových zakázek
        if($request->get('commission')){
            $i = 1;
            foreach( $request->get( 'commission' ) as $commission_id ){
                $criteria   = [
                    'id' => $commission_id,
                ];
                $commission = $em->getRepository( 'AppBundle:Commission' )->findOneBy( $criteria );
                if( $commission ){
                    $fc = new FavouriteCommission();
                    $fc->setSort( $i );
                    $fc->setUser( $user );
                    $fc->setCommission( $commission );
                    $user->addFavouriteCommission( $fc );
                    $em->persist( $fc );
                    $em->persist( $user );
                    $i++;
                }
            }
        }

        $em->flush();

        $data = [
            'flash' => [
                'type' => 'success',
                'message' => 'Oblíbené zakázky byly úspěšně uloženy.'
            ]
        ];
        $status = 200;

        return new JsonResponse($data, $status);

    }

}
