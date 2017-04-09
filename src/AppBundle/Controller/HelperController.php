<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 08.04.17
 * Time: 19:23
 */

namespace AppBundle\Controller;


use Doctrine\ORM\Query\Parameter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HelperController extends Controller
{

    /**
     * @param Parameter $single_event_params
     * @param $bet_type
     * @param $type
     * @return string
     */



    public function get_data($single_event_params, $bet_type, $type)
    {
        print '<pre>' . print_r($single_event_params->getXhash(), true) . '</pre>'; die();

        $url_xhash = self::make_url($single_event_params, $bet_type, $single_event_params->getXhash(), $type);
        $url_xhashf = self::make_url($single_event_params, $bet_type, $single_event_params->getXhashf(), $type);
        try{
            $json_data = self::get_contents($url_xhash, $single_event_params->getEventUrl());
        }catch (Exception $e){
            $error =  $e->getMessage();
        }

        if(isset($error)){
            try{
                $json_data = self::get_contents($url_xhashf, $single_event_params->getEventUrl());
            }catch (Exception $e){
                $error =  $e->getMessage();
            }
            if(isset($error)){
                $parameters = self::update_event_params($single_event_params->getEventId());
                $json_data = self::get_data($parameters, $bet_type, $type);
            }
        }

        return new Response($json_data);
//
    }
    /**
     * @param Parameter $single_event_params
     * @param $i
     * @param $hash
     * @param $type
     * @return string
     *
     * make URL
     */
    public function make_url(Parameter & $single_event_params, $i, $hash, $type){
        //current unix time
        $time = microtime(TRUE);
        $time = str_replace('.', '', $time);
        $time = substr($time, 0, -1);
        return 'http://fb.oddsportal.com/feed/match/'.$single_event_params->getVersionId().'-'.$single_event_params->getSportId().'-'.$single_event_params->getEventId().'-'.$type.'-'.$i.'-'.$hash.'.dat?_='.$time;
    }

    /**
     * @param $url
     * @param null $referrer
     * @return string
     * @throws Exception
     * get html code from url
     */
    public function get_contents($url, $referrer = NULL)
    {
        $content = shell_exec('curl -H "Referer: '.$referrer.'" ' . $url);
        if(strpos($content, 'invalidRequest') OR strpos($content, 'notAllowed')){
            throw new Exception('No Data');
        }

        return $content;
    }

    /**
     * @param $url
     * @return mixed
     * cUrl
     */
    public function curl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);

        return $content;
    }


}