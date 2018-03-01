<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FavouriteActivity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * FavouriteActivity controller.
 */
class FavouriteActivityController extends Controller
{

    /**
     * @Route("/favourite/activities/", name="app_favourite_activity_index")
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
        $activities = $em->getRepository('AppBundle:FavouriteActivity')->findBy($criteria, $orderBy);

        $activitiesChosen = [];
        foreach($activities as $activity){

            $key = $activity->getActivity()->getSlug();
            $value = $activity->getActivity()->getName();

            if(!array_key_exists($key, $activitiesChosen)){
                $activitiesChosen[$key] = $value;
            }

        }

        //Záznamy k výběru
        $companies = $user->getCompaniesEnabled();
        $activitiesToChoose = [];
        foreach($companies as $company){

            foreach($company->getActivities() as $activity){

                $key = $activity->getSlug();
                $value = $activity->getName();

                if(!array_key_exists($key, $activitiesToChoose) and !array_key_exists($key, $activitiesChosen)){
                    $activitiesToChoose[$key] = $value;
                }

            }

        }
        asort($activitiesToChoose); //keep keys

        return array(
            'activitiesChosen' => $activitiesChosen,
            'activitiesToChoose' => $activitiesToChoose,
        );
    }

    /**
     * @Route("/favourite/activities/save/", name="app_favourite_activity_save")
     */
    public function saveAction(Request $request){

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        //Odstranění všech uložených záznamů uživatele
        $activities = $user->getFavouriteActivities();
        foreach($activities as $activity){
            $em->remove($activity);
            $user->removeFavouriteActivity($activity);
        }

        //Uložení nových aktivit
        if($request->get('activity')){
            $i = 1;
            foreach( $request->get( 'activity' ) as $slug ){
                $criteria   = [
                    'slug' => $slug,
                ];
                $activities = $em->getRepository( 'AppBundle:Activity' )->findBy( $criteria );
                foreach( $activities as $activity ){
                    $fa = new FavouriteActivity();
                    $fa->setSort( $i );
                    $fa->setUser( $user );
                    $fa->setActivity( $activity );
                    $user->addFavouriteActivity( $fa );
                    $em->persist( $fa );
                    $em->persist( $user );
                }
                $i++;
            }
        }

        $em->flush();

        $data = [
            'flash' => [
                'type' => 'success',
                'message' => 'Oblíbené aktivity byly úspěšně uloženy.'
            ]
        ];
        $status = 200;

        return new JsonResponse($data, $status);

    }

}
