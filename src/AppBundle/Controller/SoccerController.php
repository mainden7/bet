<?php

/**
 * Created by PhpStorm.
 * User: denis
 * Date: 08.04.17
 * Time: 19:17
 */
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Event;
use AppBundle\Entity\Parameter;
use AppBundle\Entity\SureBet;

class SoccerController extends Controller
{
    /**
     * @Route("/odds_execute", name="odds_execute")
     * @param null $number
     */
    public function findOdds(){
        $soccer_params = $this->getDoctrine()->getManager()->getRepository('AppBundle:Parameter')->findBySportId(1);

        if(!empty($soccer_params)){
            //1x2 full time
            foreach ($soccer_params AS $parameter){
//                print '<pre>' . print_r($parameter, true) . '</pre>'; die();
                $json_data =  $this->forward('app.helper_controller:get_data', array('single_event_params' => $parameter, 'bet_type' => 2, 'type' => 1))->getContent();
                print '<pre>' . print_r($json_data, true) . '</pre>'; die();
            }


        }else{
            echo 123;die();
        }
    }
}