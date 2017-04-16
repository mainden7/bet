<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Created by PhpStorm.
 * User: denis
 * Date: 16.04.17
 * Time: 11:59
 */

class UserController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $title = 'Sure Bets';
        $em = $this->getDoctrine()->getManager();
        $sureBets = $em->getRepository('AppBundle:SureBet')->findBy(array(), array('profitPercent' => 'DESC'), null, null);
//        print '<pre>' . print_r($sureBets, true) . '</pre>'; die();
        return $this->render('user/home.html.twig', array('title' => $title, 'sureBets' => $sureBets));
    }
}