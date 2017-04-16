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
use Symfony\Component\Config\Definition\Exception\Exception;
use AppBundle\Entity\SureBet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class SoccerController extends Controller
{
    /**
     * @Route("/odds_execute", name="odds_execute")
     * @param null $number
     */
    public function findOdds($number = NULL)
    {

        if($number !== NULL) {
            $limit = 100;
            $offset = $number * 100;
            $soccer_params = $this->getDoctrine()->getManager()->getRepository('AppBundle:Parameter')->findBy(array('sportId' => 1), array('id' => 'ASC'), $limit, $offset);
//            print '<pre>' . print_r($soccer_params, true) . '</pre>';die();
        }else{
            $soccer_params = $this->getDoctrine()->getManager()->getRepository('AppBundle:Parameter')->findBySportId(1);
//            print '<pre>' . print_r(123, true) . '</pre>';die();
        }

        if (!empty($soccer_params)) {

            foreach ($soccer_params AS $parameter) {
                $eventDate = $parameter->getEventTime();
                if ($eventDate >= new \DateTime()) {

                    //1x2 full time
                    $json_data = self::get_data($parameter, 2, 1);
                    if (!empty($json_data)) {
                        self::event_outcome_single_url($parameter, $json_data, 1, 'full time', 'home_draw_away');
                    }
                    //1x2 first half
                    $json_data = self::get_data($parameter, 3, 1);
                    if (!empty($json_data)) {
                        self::event_outcome_single_url($parameter, $json_data, 1, 'first half', 'home_draw_away');
                    }
                    //1x2 second half
                    $json_data = self::get_data($parameter, 4, 1);
                    if (!empty($json_data)) {
                        self::event_outcome_single_url($parameter, $json_data, 1, 'second half', 'home_draw_away');
                    }
//                    over/under full time
                    $json_data = self::get_data($parameter, 2, 2);
                    if(!empty($json_data)) {
                        self::event_outcome_single_url($parameter, $json_data, 2, 'full time', 'over_under');
                    }
                    //over/under first half
                    $json_data = self::get_data($parameter, 2, 2);
                    if(!empty($json_data)) {
                        self::event_outcome_single_url($parameter, $json_data, 3, 'first half', 'over_under');
                    }
                    //over/under second half
                    $json_data = self::get_data($parameter, 2, 2);
                    if(!empty($json_data)) {
                        self::event_outcome_single_url($parameter, $json_data, 4, 'second half', 'over_under');
                    }
//                    both team to score
//                    full time
                    $json_data = self::get_data($parameter, 2, 13);
                    if(!empty($json_data)) {
                        self::event_outcome_single_url($parameter, $json_data, 3, 'full time', 'both_score');
                    }
                    //first half
                    $json_data = self::get_data($parameter, 3, 13);
                    if(!empty($json_data)) {
                        self::event_outcome_single_url($parameter, $json_data, 3, 'first half', 'both_score');
                    }
                    //second half
                    $json_data = self::get_data($parameter, 4, 13);
                    if(!empty($json_data)) {
                        self::event_outcome_single_url($parameter, $json_data, 3, 'second half', 'both_score');
                    }

                    //1x2 & Double chance full time
                    //json data for 2 urls
                    $json_data_1x2 = self::get_data($parameter, 2, 1);
                    $json_data_dc = self::get_data($parameter, 2, 4);
                    if(!empty($json_data_1x2) AND !empty($json_data_dc)) {
                        self::event_outcome_two_urls($parameter, $json_data_1x2, $json_data_dc, 'full time', 'double_chance');
                    }
                    //1x2 & Double chance first half
                    //json data for 2 urls
                    $json_data_1x2 = self::get_data($parameter, 3, 1);
                    $json_data_dc = self::get_data($parameter, 3, 4);
                    if(!empty($json_data_1x2) AND !empty($json_data_dc)) {
                        self::event_outcome_two_urls($parameter, $json_data_1x2, $json_data_dc, 'first half', 'double_chance');
                    }
                    //1x2 & Double chance second half
                    //json data for 2 urls
                    $json_data_1x2 = self::get_data($parameter, 4, 1);
                    $json_data_dc = self::get_data($parameter, 4, 4);
                    if(!empty($json_data_1x2) AND !empty($json_data_dc)) {
                        self::event_outcome_two_urls($parameter, $json_data_1x2, $json_data_dc, 'second half', 'double_chance');
                    }
                } else {

                    $em = $this->getDoctrine()->getManager();
                    $event = $em->getRepository('AppBundle:Event')->findOneByEventId($parameter->getEventId());
                    $em->remove($event);
                    $em->flush();
                    $param = $em->getRepository('AppBundle:Parameter')->findOneByEventId($parameter->getEventId());
                    $em->remove($param);
                    $em->flush();

                }
            }

            return new Response('success');
        } else {
            return new Response('No soccer events');
        }

    }


    public function get_data(Parameter & $single_event_params, $bet_type, $type)
    {


        $url_xhash = self::make_url($single_event_params, $bet_type, $single_event_params->getXhash(), $type);
        $url_xhashf = self::make_url($single_event_params, $bet_type, $single_event_params->getXhashf(), $type);

        try {
            $json_data = self::get_contents($url_xhash, $single_event_params->getEventUrl());
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        if (isset($error)) {
            try {
                $json_data = self::get_contents($url_xhashf, $single_event_params->getEventUrl());
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if (isset($error)) {
                $parameters = self::update_event_params($single_event_params->getEventId());
                $json_data = self::get_data($parameters, $bet_type, $type);
            }
        }

        return $json_data;
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
    public function make_url(Parameter & $single_event_params, $i, $hash, $type)
    {
        //current unix time
        $time = microtime(TRUE);
        $time = str_replace('.', '', $time);
        $time = substr($time, 0, -1);
        return 'http://fb.oddsportal.com/feed/match/' . $single_event_params->getVersionId() . '-' . $single_event_params->getSportId() . '-' . $single_event_params->getEventId() . '-' . $type . '-' . $i . '-' . $hash . '.dat?_=' . $time;
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
        $content = shell_exec('curl -H "Referer: ' . $referrer . '" ' . $url);
        if (strpos($content, 'invalidRequest') OR strpos($content, 'notAllowed')) {
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

    public function update_event_params($event_id)
    {

        $em = $this->getDoctrine()->getManager();
        $single_event = $em->getRepository('AppBundle:Event')->findOneByEventId($event_id);
        $event_url = $single_event->getUrl();
        $event_name = $single_event->getName();
        $data = self::curl($event_url);


        //step 1
        $st = strpos($data, 'PageEvent');
        $html = substr($data, $st);
        //step 2
        $st = strpos($html, '"');
        $html = substr($html, $st);
        //step 3
        $st = strrchr($html, '}');
        $html = stristr($html, '}', true);

        $params = array();
        $arr = explode(',', $html);

        foreach ($arr as $key => $value) {
            $at = str_replace('"', '', $value);
            preg_match_all("#([^,\s]+):([^,\s]+)#s", $at, $out);
            unset($out[0]);
            $out = array_combine($out[1], $out[2]);
            $params += $out;
        }
        $home_away = explode('-', $event_name);

        if (!empty($home_away[0])) {
            $home = $home_away[0];
        } else {
            $home = NULL;
        }
        if (!empty($home_away[1])) {
            $away = $home_away[1];
        } else {
            $away = NULL;
        }
        $xhash = urldecode($params['xhash']);
        $xhashf = urldecode($params['xhashf']);
//        print '<pre>' . print_r($xhashf, true) . '</pre>'; die();
        if (($params['isStarted']) == 'false') {
            $parameter = $em->getRepository('AppBundle:Parameter')->findOneByEventId($event_id);
            $parameter->setXhash($xhash)
                ->setXhashf($xhashf);
            $em->flush();
        }
        return $parameter;
    }

    private function event_outcome_two_urls(Parameter & $single_event_params, $json_data_1x2, $json_data_dc, $bet_id, $bet_type)
    {
        //all odds 1x2
        $all_odds_1x2 = self::threeOutomesOddsExecute($json_data_1x2);
        $home_win = (array)$all_odds_1x2[0];
        $away_win = (array)$all_odds_1x2[1];
        $draw = (array)$all_odds_1x2[2];

        //all odds double chanses
        $all_odds_dc = self::threeOutomesOddsExecute($json_data_dc);
        $home_win_draw = (array)$all_odds_dc[0];
        $away_win_draw = (array)$all_odds_dc[1];
        $no_draw = (array)$all_odds_dc[2];

        if (!empty($home_win) AND !empty($away_win) AND !empty($draw) AND !empty($home_win_draw) AND !empty($away_win_draw) AND !empty($no_draw)) {
            arsort($home_win);
            arsort($draw);
            arsort($away_win);
            arsort($home_win_draw);
            arsort($away_win_draw);
            arsort($no_draw);
            $home_max = reset($home_win);
            $away_max = reset($away_win);
            $draw_max = reset($draw);
            $home_win_draw_max = reset($home_win_draw);
            $away_win_draw_max = reset($away_win_draw);
            $no_draw_max = reset($no_draw);

            //home win vs  away draw win
            $sure_cf_max = 1 / $home_max + 1 / $away_win_draw_max;

            if ($sure_cf_max < 1.5) {
                self::sure_bet_calculation_two_results($single_event_params, $home_win, $away_win_draw, $bet_id, 'home win', 'away win/draw', $bet_type);
            }

            //draw vs no draw
            $sure_cf_max = 1 / $draw_max + 1 / $no_draw_max;
            if ($sure_cf_max < 1.5) {
                self::sure_bet_calculation_two_results($single_event_params, $draw, $no_draw, $bet_id, 'draw', 'no draw', $bet_type);
            }
            //away win vs home draw win
            $sure_cf_max = 1 / $away_max + 1 / $home_win_draw_max;
            if ($sure_cf_max < 1.5) {
                self::sure_bet_calculation_two_results($single_event_params, $home_win_draw, $away_win, $bet_id, 'home win/draw', 'away win', $bet_type);
            }

        }

    }


    private function event_outcome_single_url(Parameter & $single_event_params, $json_data, $calc_type, $bet_id, $bet_type)
    {

        //if 1x2
        if ($calc_type == 1) {

            $all_odds = self::threeOutomesOddsExecute($json_data);
            if ($all_odds != NULL) {

                $home_win = (array)$all_odds[0];
                $away_win = (array)$all_odds[1];
                $draw = (array)$all_odds[2];
                if(!empty($home_win) AND !empty($away_win) AND !empty($draw)){
                    arsort($home_win);
                    arsort($draw);
                    arsort($away_win);
                    $home_max = reset($home_win);
                    $away_max = reset($away_win);
                    $draw_max = reset($draw);

                    $sure_cf_max = 1 / $home_max + 1 / $away_max + 1 / $draw_max;
                    if ($sure_cf_max < 1.5) {
                        self::sure_bet_calculation_three_results($single_event_params, $home_win, $draw, $away_win, $bet_id, $bet_type);
                    }
                }
            }
        }

        //if Over/Under
        if ($calc_type == 2) {
            $all_odds = self::overUnderOddsExecute($json_data, array(0.5, 1.5, 2.5, 3.5, 4.5, 5.5, 6.5));
            if ($all_odds != NULL) {
                foreach ($all_odds as $key => $value) {
                    $value = (array)$value;

                    $over = (array)$value['over'];
                    $under = (array)$value['under'];

                    arsort($over);
                    arsort($under);

                    $over_max = reset($over);
                    $under_max = reset($under);

                    if ($over_max != 0 AND $under_max != 0) {
                        $sure_cf_max = 1 / $over_max + 1 / $under_max;

                        if ($sure_cf_max < 1) {
                            self::sure_bet_calculation_two_results($single_event_params, $over, $under, $bet_id, 'over ' . $key, 'under ' . $key, $bet_type);
                        }
                    }

//

                }
            }
        }
        //if both team to score
        if ($calc_type == 3) {
            $all_odds = self::bothToScoreOddsExecute($json_data);

            if ($all_odds != NULL) {

                $yes = (array)$all_odds[0];
                $no = (array)$all_odds[1];

                if(!empty($yes) AND !empty($no)){
                    arsort($yes);
                    arsort($no);

                    $yes_max = reset($yes);
                    $no_max = reset($no);

                    $sure_cf_max = 1 / $yes_max + 1 / $no_max;
                    if ($sure_cf_max < 1) {
                        self::sure_bet_calculation_two_results($single_event_params, $yes, $no, $bet_id, 'both team to score yes ', 'both team to score no ', $bet_type);
                    }
                }
            }

        }
    }


    public function sure_bet_calculation_two_results(Parameter & $event_params, $first_results, $second_results, $bet_id, $var, $var2, $bet_type)
    {

        $em = $this->getDoctrine()->getManager();
        $single_event = $em->getRepository('AppBundle:Event')->findOneByEventId($event_params->getEventId());


        $odd_type_1 = $var . ' ' . $bet_id;
        $odd_type_2 = $var2 . ' ' . $bet_id;

        foreach ($first_results as $first_key => $first_value) {
            foreach ($second_results as $second_key => $second_value) {
                $sure_cf = 1 / $first_value + 1 / $second_value;

                if ($sure_cf < 1) {
                    $percent_profit = (1 - $sure_cf) * 100;
                    if ($percent_profit > 1) {
                        $bet_id = hash('md5', $event_params->getEventId() . $first_key . $second_key . $bet_type);
                        $sure_bet_exist = $em->getRepository('AppBundle:SureBet')->findOneByBetId($bet_id);
                        if ($sure_bet_exist) {
                            //update
                            $sure_bet_exist->setHomeName($event_params->getHome())
                                ->setProfitPercent($percent_profit)
                                ->setFirstOddCf($first_value)
                                ->setSecondOddCf(NULL)
                                ->setThirdOddCf($second_value)
                                ->setEventTime($event_params->getEventTime());
                        } else {
                            $sureBet = new SureBet();
                            $sureBet->setHomeName($event_params->getHome())
                                ->setAwayName($event_params->getAway())
                                ->setEventName($single_event->getName())
                                ->setProfitPercent($percent_profit)
                                ->setBookmakerFirst($first_key)
                                ->setBookmakerSecond($second_key)
                                ->setBookmakerThird(null)
                                ->setTournamentName($single_event->getTournamentName())
                                ->setSportName($event_params->getSportName())
                                ->setFirstOddCf($first_value)
                                ->setSecondOddCf($second_value)
                                ->setThirdOddCf(null)
                                ->setFirstOddType($odd_type_1)
                                ->setSecondOddType($odd_type_2)
                                ->setThirdOddType(NULL)
                                ->setEventTime($event_params->getEventTime())
                                ->setEventId($event_params->getEventId())
                                ->setBetId($bet_id)
                                ->setBetType($bet_type);

                            $em->persist($sureBet);
                        }

                        $em->flush();
                    } else {
                        $bet_id = hash('md5', $event_params->getEventId() . $first_key . $second_key . $bet_type);
                        $sure_bet_exist = $em->getRepository('AppBundle:SureBet')->findOneByBetId($bet_id);
                        if ($sure_bet_exist) {
                            $em->remove($sure_bet_exist);
                            $em->flush();
                        }
                    }
                }
            }
        }

    }

    private function sure_bet_calculation_three_results(Parameter & $single_event_params, $home_win, $draw, $away_win, $bet_id, $bet_type)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('AppBundle:Event')->findOneByEventId($single_event_params->getEventId());

        $odd_type_1 = 'home win ' . $bet_id;
        $odd_type_2 = 'draw  ' . $bet_id;
        $odd_type_3 = 'away win ' . $bet_id;


        //home odds
        foreach ($home_win as $home_key => $home_value) {
            //draw odds
            foreach ($draw as $draw_key => $draw_value) {
                //away odds
                foreach ($away_win as $away_key => $away_value) {
                    $sure_cf = 1 / $home_value + 1 / $draw_value + 1 / $away_value;

                    if ($sure_cf < 1) {
                        $percent_profit = (1 - $sure_cf) * 100;

                        if ($percent_profit > 1) {

                            /*$bet1 = (1/$home_max/$sure_cf_max)*100;
                            $bet2 = (1/$draw_max/$sure_cf_max)*100;
                            $bet3 = (1/$away_max/$sure_cf_max)*100;*/

                            $bet_id = hash('md5', $single_event_params->getEventId() . $home_key . $draw_key . $away_key . $bet_type);
                            $sure_bet_exist = $em->getRepository('AppBundle:SureBet')->findOneByBetId($bet_id);
                            if ($sure_bet_exist) {
                                //update
                                $sure_bet_exist->setHomeName($single_event_params->getHome())
                                    ->setProfitPercent($percent_profit)
                                    ->setFirstOddCf($home_value)
                                    ->setSecondOddCf($draw_value)
                                    ->setThirdOddCf($away_value)
                                    ->setEventTime($single_event_params->getEventTime());
                            } else {
                                //save
                                $sureBet = new SureBet();
                                $sureBet->setHomeName($single_event_params->getHome())
                                    ->setAwayName($single_event_params->getAway())
                                    ->setEventName($event->getName())
                                    ->setProfitPercent($percent_profit)
                                    ->setBookmakerFirst($home_key)
                                    ->setBookmakerSecond($draw_key)
                                    ->setBookmakerThird($away_key)
                                    ->setTournamentName($event->getTournamentName())
                                    ->setSportName($single_event_params->getSportName())
                                    ->setFirstOddCf($home_value)
                                    ->setSecondOddCf($draw_value)
                                    ->setThirdOddCf($away_value)
                                    ->setFirstOddType($odd_type_1)
                                    ->setSecondOddType($odd_type_2)
                                    ->setThirdOddType($odd_type_3)
                                    ->setEventTime($single_event_params->getEventTime())
                                    ->setEventId($single_event_params->getEventId())
                                    ->setBetId($bet_id)
                                    ->setBetType($bet_type);

                                $em->persist($sureBet);

                            }

                            $em->flush();

                        } else {
                            $bet_id = hash('md5', $single_event_params->getEventId() . $home_key . $draw_key . $away_key . $bet_type);
                            $sure_bet_exist = $em->getRepository('AppBundle:SureBet')->findOneByBetId($bet_id);
                            if ($sure_bet_exist) {
                                $em->remove($sure_bet_exist);
                                $em->flush();
                            }
                        }
                    }
                }
            }
        }
    }

    public function threeOutomesOddsExecute($json_data)
    {
        $sub = strpos($json_data, '{');
        $json_string = substr($json_data, $sub, -2);
        $json_string = json_decode($json_string);

        if(isset($json_string->d->oddsdata->back)) {
            $odds = $json_string->d->oddsdata->back;
            $all_odds = array();

            $first_result = array();
            $second_result = array();
            $third_result = array();

            if(!empty($odds)){
                foreach ($odds as $od_value) {
                    $all_odds_array = (array)$od_value->odds;

                    //search error odds
                    $act = (array)$od_value->act;
                    $act_int = array();
                    foreach ($act as $key => $value) {
                        $act_int[(int)$key] = $value;
                    }


                    foreach ($all_odds_array as $key => $value) {
                        if ($act_int[$key] == 1) {

                            foreach ($value as $k => $odd) {
                                (int)$i = $k;

                                if ($i == 0) {
                                    $first_result[$key] = $odd;
                                } elseif ($i == 1) {
                                    $second_result[$key] = $odd;
                                } else {
                                    $third_result[$key] = $odd;
                                }
                            }
                        }
                    }
                }
            }

            $all_odds[] = $first_result;
            $all_odds[] = $third_result;
            $all_odds[] = $second_result;
        }else{
            $all_odds = NULL;
        }

        return $all_odds;

    }

    public function overUnderOddsExecute($json_data, $handicap_value){
//        print '<pre>' . print_r($handicap_value, true) . '</pre>';die();
        $sub = strpos($json_data, '{');
        $json_string = substr($json_data, $sub, -2);
        $json_string = json_decode($json_string);

        $odds = $json_string->d->oddsdata->back;

        $all_odds = array();
        $handicap_all_odds = array();

        if(isset($odds)){
            foreach ($odds as $od_value) {

                if (in_array($od_value->handicapValue, $handicap_value)) {
                    $first_result = array();
                    $second_result = array();

                    $all_odds_array = (array)$od_value->odds;
                    $act = (array)$od_value->act;
                    $act_int = array();
                    foreach ($act as $key => $value) {
                        $act_int[(int)$key] = $value;
                    }

                    foreach ($all_odds_array as $key => $value) {
                        if ($act_int[$key] == 1) {

                            foreach ($value as $k => $odd) {
                                (int)$i = $k;

                                if ($i == 0) {
                                    $first_result[$key] = $odd;
                                } elseif ($i == 1) {
                                    $second_result[$key] = $odd;
                                }
                            }
                        }
                    }

                    $handicap_all_odds['over'] = $first_result;
                    $handicap_all_odds['under'] = $second_result;
                    $all_odds[$od_value->handicapValue] = $handicap_all_odds;
                }

            }
        }

        return $all_odds;
    }

    public function bothToScoreOddsExecute($json_data){
        $sub = strpos($json_data, '{');
        $json_string = substr($json_data, $sub, -2);
        $json_string = json_decode($json_string);



        $all_odds = array();
        if(isset($json_string->d->oddsdata->back)){
            $odds = $json_string->d->oddsdata->back;
            if(!empty($odds)){
                foreach ($odds as $od_value) {
                    $first_result = array();
                    $second_result = array();

                    $all_odds_array = (array)$od_value->odds;
                    $act = (array)$od_value->act;
                    $act_int = array();
                    foreach ($act as $key => $value) {
                        $act_int[(int)$key] = $value;
                    }

                    foreach ($all_odds_array as $key => $value) {
                        if ($act_int[$key] == 1) {

                            foreach ($value as $k => $odd) {
                                (int)$i = $k;

                                if ($i == 0) {
                                    $first_result[$key] = $odd;
                                } elseif ($i == 1) {
                                    $second_result[$key] = $odd;
                                }
                            }
                        }
                    }
                    $all_odds[] = $first_result;
                    $all_odds[] = $second_result;

                }
            }
        }

        return $all_odds;
    }

    public function twoOutcomesOddsExecute($json_data, $bet_type)
    {


        //if both team to score
        if ($bet_type == 'both_score' OR $bet_type == 'home_away') {

            $sub = strpos($json_data, '{');
            $json_string = substr($json_data, $sub, -2);
            $json_string = json_decode($json_string);


            $odds = $json_string->d->oddsdata->back;
            $all_odds = array();

            foreach ($odds as $od_value) {
                $first_result = array();
                $second_result = array();

                $all_odds_array = (array)$od_value->odds;
                $act = (array)$od_value->act;
                $act_int = array();
                foreach ($act as $key => $value) {
                    $act_int[(int)$key] = $value;
                }

                foreach ($all_odds_array as $key => $value) {
                    if ($act_int[$key] == 1) {

                        foreach ($value as $k => $odd) {
                            (int)$i = $k;

                            if ($i == 0) {
                                $first_result[$key] = $odd;
                            } elseif ($i == 1) {
                                $second_result[$key] = $odd;
                            }
                        }
                    }
                }
                $all_odds[] = $first_result;
                $all_odds[] = $second_result;

            }
        }



        if (isset($all_odds)) {
            $result = $all_odds;
        } else {
            $result = NULL;
        }

        return $result;


    }

}