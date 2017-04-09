<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 28.03.17
 * Time: 17:51
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\Parameter;
use AppBundle\Entity\SureBet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class CalculationController extends Controller
{
    
    public function oddsExecute($number = null)
    {
        //load all params
        $all_event_params = $this->getDoctrine()->getManager()->getRepository('AppBundle:Parameter')->findAll();

        foreach ($all_event_params as $single_event_params) {
            //check if event is not started in 30 minutes
            $time = new \DateTime();
            $time = $time->modify('+30 minutes');
            $event_time = new \DateTime($single_event_params->getEventTime()->format('Y-m-d H:i:s'));
            //execute odds if no
            if($event_time >= $time){
                $sport_id = $single_event_params->getSportId();
                switch ($sport_id) {
                    case 1:
                        self::soccer_sure_bet($single_event_params);
                        break;
                    case 2:
                        self::tennis_sure_bet($single_event_params);
                        break;
                    case 3:
                        self::basketball_sure_bet($single_event_params);
                        break;
                    case 4:
                        self::hockey_sure_bet($single_event_params);
                        break;
                    case 5:
                        self::american_fotball_sure_bet($single_event_params);
                        break;
                    case 6:
                        self::baseball_sure_bet($single_event_params);
                        break;
                    case 7:
                        self::handball_sure_bet($single_event_params);
                        break;
                    case 8:
                        self::rugby_union_sure_bet($single_event_params);
                        break;
                    case 11:
                        self::futsal_sure_bet($single_event_params);
                        break;
                    case 12:
                        self::vollyebal_sure_bet($single_event_params);
                        break;
                    case 13:
                        self::cricket_sure_bet($single_event_params);
                        break;
                    case 14:
                        self::darts_sure_bet($single_event_params);
                        break;
                    case 15:
                        self::snooker_sure_bet($single_event_params);
                        break;
                    case 16:
                        self::boxing_sure_bet($single_event_params);
                        break;
                    case 17:
                        self::beach_volleyball_sure_bet($single_event_params);
                        break;
                    case 18:
                        self::aussie_rules_sure_bet($single_event_params);
                        break;
                    case 21:
                        self::badminton_sure_bet($single_event_params);
                        break;
                    case 22:
                        self::water_polo_sure_bet($single_event_params);
                        break;
                    case 28:
                        self::mma_sure_bet($single_event_params);
                        break;
                    case 30:
                        self::pesapallo_sure_bet($single_event_params);
                        break;
                    case 36:
                        self::esports_sure_bet($single_event_params);
                        break;
                    default:
                        # code...
                        break;
                }
            }else{
                //delete if yes
                self::delete_event($single_event_params->getEventId());
            }

        }
die();
    }

    private function make_url(Parameter & $single_event_params, $i, $hash, $type){
        //current unix time
        $time = microtime(TRUE);
        $time = str_replace('.', '', $time);
        $time = substr($time, 0, -1);

        return 'http://fb.oddsportal.com/feed/match/'.$single_event_params->getVersionId().'-'.$single_event_params->getSportId().'-'.$single_event_params->getEventId().'-'.$type.'-'.$i.'-'.$hash.'.dat?_='.$time;
    }

    /**
     * @param $single_event_params
     */

    private function soccer_sure_bet(Parameter & $single_event_params){
        //1x2
        for ($bet_id = 2; $bet_id <=4; $bet_id++) {
            $json_data = self::get_data($single_event_params, $bet_id, 1);
            if($json_data != NULL) {
                self::event_outcome_single_url($single_event_params, $json_data, $bet_id, 'win_draw_loose');
            }else{
                continue;
            }
        }
        //1x2 & Double chance
        for ($bet_id = 2; $bet_id <=4; $bet_id++) {
            //json data for 2 urls
            $json_data_1x2 = self::get_data($single_event_params, $bet_id, 1);
            $json_data_dc = self::get_data($single_event_params, $bet_id, 4);
            self::event_outcome_two_urls($single_event_params, $json_data_1x2, $json_data_dc, $bet_id, 'double_chance');

        }
//        //Over/Under
        for($bet_id = 2; $bet_id <= 4; $bet_id++){
            $json_data = self::get_data($single_event_params, $bet_id, 2);
            self::event_outcome_single_url($single_event_params, $json_data, $bet_id, 'over_under');
        }
//        //both team to score
        for($bet_id = 2; $bet_id <= 4; $bet_id++){
            $json_data = self::get_data($single_event_params, $bet_id, 13);
            self::event_outcome_single_url($single_event_params, $json_data, $bet_id, 'both_score');
        }

    }

    private function tennis_sure_bet(Parameter & $single_event_params){
        //home_away
//        full time
        $json_data = self::get_data($single_event_params, 2, 3);
        self::event_outcome_single_url($single_event_params, $json_data, 2, 'home_away');
        //1st set
        $json_data = self::get_data($single_event_params, 12, 3);
        self::event_outcome_single_url($single_event_params, $json_data, 12, 'home_away');
        //2 set
        $json_data = self::get_data($single_event_params, 13, 3);
        self::event_outcome_single_url($single_event_params, $json_data, 13, 'home_away');

//        Asian Handicap
//        full time
        $json_data = self::get_data($single_event_params, 2, 5);
        self::event_outcome_single_url($single_event_params, $json_data, 2, 'asian_handicap');

        //over under full time
        $json_data = self::get_data($single_event_params, 2, 2);
        self::event_outcome_single_url($single_event_params, $json_data, 2, 'tennis_over_under');

    }

    private function basketball_sure_bet(Parameter & $single_event_params){
        //1x2
        //full time
//        $json_data = self::get_data($single_event_params, 2, 1);
//        if($json_data != null){
//            self::event_outcome_single_url($single_event_params, $json_data, 2, 'basket_1x2');
//        }
//        //1st half
//        $json_data = self::get_data($single_event_params, 3, 1);
//        if($json_data != null){
//            self::event_outcome_single_url($single_event_params, $json_data, 3, 'basket_1x2');
//        }
//        //2nd half
//        $json_data = self::get_data($single_event_params, 4, 1);
//        if($json_data != null){
//            self::event_outcome_single_url($single_event_params, $json_data, 4, 'basket_1x2');
//        }
//        //home away
//        //full time
//        $json_data = self::get_data($single_event_params, 1, 3);
//        if($json_data != null){
//            self::event_outcome_single_url($single_event_params, $json_data, 1, 'basket_home_away');
//        }
//        //first half
//        $json_data = self::get_data($single_event_params, 3, 3);
//        if($json_data != null){
//            self::event_outcome_single_url($single_event_params, $json_data, 3, 'basket_home_away');
//        }
//        //1Q
//        $json_data = self::get_data($single_event_params,8, 3);
//        if($json_data != null){
//            self::event_outcome_single_url($single_event_params, $json_data, 8, 'basket_home_away');
//        }
        //Over Under
        //Full time with OT
        $json_data = self::get_data($single_event_params, 1, 2);
        if($json_data != NULL){
            self::event_outcome_single_url($single_event_params, $json_data, 1, 'basket_over_under');
        }
        //FT without OT
        $json_data = self::get_data($single_event_params, 2, 2);
        if($json_data != NULL){
            self::event_outcome_single_url($single_event_params, $json_data, 1, 'basket_over_under');
        }
        //1st half
        $json_data = self::get_data($single_event_params, 3, 2);
        if($json_data != NULL){
            self::event_outcome_single_url($single_event_params, $json_data, 1, 'basket_over_under');
        }

    }

    /**
     * @param $single_event_params
     * @param $url_xhash
     * @param $url_xhashf
     * @param $type
     * @return string
     *
     * get data from url given for
     */

    private function get_data(Parameter & $single_event_params, $bet_type, $type)
    {
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

        return $json_data;
//
    }

    private function event_outcome_single_url(Parameter & $single_event_params, $json_data, $bet_id, $bet_type)
    {

        //if 1x2
        if($bet_type == 'win_draw_loose' or $bet_type == 'basket_1x2') {
            $all_odds = self::three_events_odds_execute($json_data);
            if($all_odds != NULL){
                $home_win = $all_odds[0];
                $away_win = $all_odds[1];
                $draw = $all_odds[2];

                arsort($home_win);
                arsort($draw);
                arsort($away_win);
                $home_max = reset($home_win);
                $away_max = reset($away_win);
                $draw_max = reset($draw);

                $sure_cf_max = 1/$home_max + 1/$away_max + 1/$draw_max;
                if($sure_cf_max < 1.5){
                    self::sure_bet_calculation_three_results($home_win, $draw, $away_win, $single_event_params, $bet_id, $bet_type);
                }
            }
        }

        //if Over/Under
        if($bet_type == 'over_under'  or $bet_type == 'asian_handicap' or $bet_type == 'tennis_over_under' or $bet_type == 'basket_over_under'){

            $all_odds = self::two_events_odds_execute($json_data, $bet_type);

            if($all_odds != NULL){
                foreach ($all_odds as $key => $value) {
                    $over = $value['over'];
                    $under = $value['under'];

                    arsort($over);
                    arsort($under);

                    $over_max = reset($over);
                    $under_max = reset($under);

                    if($over_max != 0 AND $under_max != 0){
                        $sure_cf_max = 1/$over_max + 1/$under_max;
                        print '<pre>' . print_r($sure_cf_max, true) . '</pre>';
                        if($sure_cf_max < 1){
                            self::sure_bet_calculation_two_results($over, $under, $single_event_params, $bet_id, 'over '.$key, 'under '.$key, $bet_type);
                        }
                    }

//

                }
            }
        }
        //if both team to score
        if($bet_type == 'both_score'){
            $all_odds = self::two_events_odds_execute($json_data, $bet_type);


            if($all_odds != NULL){
                $yes = $all_odds[0];
                $no = $all_odds[1];

                arsort($yes);
                arsort($no);

                $yes_max = reset($yes);
                $no_max = reset($no);

                $sure_cf_max = 1/$yes_max + 1/$no_max;
                if($sure_cf_max < 1){
                    self::sure_bet_calculation_two_results($yes, $no, $single_event_params, $bet_id, 'both team to score yes ', 'both team to score no ', $bet_type);
                }
            }

        }

        //
        if($bet_type == 'home_away'){
            $all_odds = self::two_events_odds_execute($json_data, $bet_type);
            if($all_odds != NULL){
                $yes = $all_odds[0];
                $no = $all_odds[1];

                arsort($yes);
                arsort($no);

                $yes_max = reset($yes);
                $no_max = reset($no);

                $sure_cf_max = 1/$yes_max + 1/$no_max;
                if($sure_cf_max < 1){
                    self::sure_bet_calculation_two_results($yes, $no, $single_event_params, $bet_id, 'home win ', 'away win ', $bet_type);
                }
            }
        }

    }

    private function three_events_odds_execute($json_data)
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

            $all_odds[] = $first_result;
            $all_odds[] = $third_result;
            $all_odds[] = $second_result;
        }else{
            $all_odds = NULL;
        }

        return $all_odds;

    }

    public function two_events_odds_execute($json_data, $bet_type)
    {


        //if over under
        if ($bet_type == 'over_under' ) {

            $sub = strpos($json_data, '{');
            $json_string = substr($json_data, $sub, -2);
            $json_string = json_decode($json_string);

            $odds = $json_string->d->oddsdata->back;

            $all_odds = array();
            $handicap_all_odds = array();


            foreach ($odds as $od_value) {

                if ($od_value->handicapValue == 0.5 || $od_value->handicapValue == 1.5 || $od_value->handicapValue == 2.5 || $od_value->handicapValue == 3.5 || $od_value->handicapValue == 4.5 || $od_value->handicapValue == 5.5 || $od_value->handicapValue == 6.5) {
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

        if($bet_type == 'basket_over_under'){
            $sub = strpos($json_data, '{');
            $json_string = substr($json_data, $sub, -2);
            $json_string = json_decode($json_string);

            $odds = $json_string->d->oddsdata->back;

            $all_odds = array();
            $handicap_all_odds = array();


            if(isset($odds)){
                foreach ($odds as $od_value) {

                    if ($od_value->handicapValue >= 200) {

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

        }

        if($bet_type == 'tennis_over_under'){
            $sub = strpos($json_data, '{');
            $json_string = substr($json_data, $sub, -2);
            $json_string = json_decode($json_string);

            $odds = $json_string->d->oddsdata->back;

            $all_odds = array();
            $handicap_all_odds = array();

            if(isset($odds)){
                foreach ($odds as $od_value) {

                    if ($od_value->handicapValue >= 20) {

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
        }

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

        if($bet_type == 'asian_handicap'){
            $sub = strpos($json_data, '{');
            $json_string = substr($json_data, $sub, -2);
            $json_string = json_decode($json_string);

            $odds = $json_string->d->oddsdata->back;

            $all_odds = array();
            $handicap_all_odds = array();


            foreach ($odds as $od_value) {

                if ($od_value->handicapValue == '+1.5' OR  $od_value->handicapValue == '-1.5') {
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

        if (isset($all_odds)) {
            return $all_odds;
        } else {
            return NULL;
        }


    }

    private function sure_bet_calculation_three_results($home_win, $draw, $away_win, Parameter & $single_event_params, $bet_id, $bet_type)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('AppBundle:Event')->findOneByEventId($single_event_params->getEventId());
        if($bet_id == 2){
            $odd_type_1 = 'home win full time';
            $odd_type_2 = 'draw full time';
            $odd_type_3 = 'away win full time';
        }elseif($bet_id == 3){
            $odd_type_1 = 'home win first half';
            $odd_type_2 = 'draw full first half';
            $odd_type_3 = 'away win first half';
        }elseif($bet_id == 4){
            $odd_type_1 = 'home win seconf half';
            $odd_type_2 = 'draw second half';
            $odd_type_3 = 'away win second half';
        }


        //home odds
        foreach ($home_win as $home_key=>$home_value) {
            //draw odds
            foreach ($draw as $draw_key => $draw_value) {
                //away odds
                foreach ($away_win as $away_key => $away_value) {
                    $sure_cf = 1/$home_value + 1/$draw_value + 1/$away_value;

                    if($sure_cf < 1){
                        $percent_profit = (1-$sure_cf)*100;

                        if($percent_profit > 1){

                            /*$bet1 = (1/$home_max/$sure_cf_max)*100;
                            $bet2 = (1/$draw_max/$sure_cf_max)*100;
                            $bet3 = (1/$away_max/$sure_cf_max)*100;*/

                            $bet_id = hash('md5', $single_event_params->getEventId() . $home_key . $draw_key . $away_key . $bet_type);
                            $sure_bet_exist = $em->getRepository('AppBundle:SureBet')->findOneByBetId($bet_id);
                            if($sure_bet_exist){
                                //update
                                $sure_bet_exist->setHomeName($single_event_params->getHome())
                                    ->setProfitPercent($percent_profit)
                                    ->setFirstOddCf($home_value)
                                    ->setSecondOddCf($draw_value)
                                    ->setThirdOddCf($away_value)
                                    ->setEventTime($single_event_params->getEventTime())
                                ;
                            }else{
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
                                    ->setBetType($bet_type)
                                ;

                                $em->persist($sureBet);

                            }

                            $em->flush();

                        }else{
                            $bet_id = hash('md5', $single_event_params->getEventId() . $home_key . $draw_key . $away_key . $bet_type);
                            $sure_bet_exist = $em->getRepository('AppBundle:SureBet')->findOneByBetId($bet_id);
                            if($sure_bet_exist){
                                $em->remove($sure_bet_exist);
                                $em->flush();
                            }
                        }
                    }
                }
            }
        }
    }

    public function sure_bet_calculation_two_results($first_results, $second_results, Parameter & $event_params, $bet_id, $var, $var2, $bet_type){

        $em = $this->getDoctrine()->getManager();
        $single_event = $em->getRepository('AppBundle:Event')->findOneByEventId($event_params->getEventId());

        if($bet_type == 'home_away'){
            if($bet_id == 2){
                $odd_type_1 = $var . 'full time';
                $odd_type_2 = $var2 . 'full time';
            }elseif($bet_type == 12){
                $odd_type_1 = $var . '1 set';
                $odd_type_2 = $var2 . '1 set';
            }elseif($bet_type == 13){
                $odd_type_1 = $var . '2 set';
                $odd_type_2 = $var2 . '2 set';
            }
        }else{
            if($bet_id == 2){
                $odd_type_1 = $var.' full time';
                $odd_type_2 = $var2.' full time';
            }elseif($bet_id == 3){
                $odd_type_1 = $var.' first half';
                $odd_type_2 = $var2.' first half';
            }elseif($bet_id == 4){
                $odd_type_1 = $var.' seconf half';
                $odd_type_2 = $var2.' second half';
            }else{
                $odd_type_1 = '';
                $odd_type_2 = '';
            }
        }
        foreach ($first_results as $first_key=>$first_value) {
            foreach ($second_results as $second_key => $second_value) {
                $sure_cf = 1/$first_value + 1/$second_value;

                if($sure_cf < 1){
                    $percent_profit = (1-$sure_cf)*100;
                    if($percent_profit > 1){
                        $bet_id = hash('md5', $event_params->getEventId() . $first_key . $second_key . $bet_type);
                        $sure_bet_exist = $em->getRepository('AppBundle:SureBet')->findOneByBetId($bet_id);
                        if($sure_bet_exist){
                            //update
                            $sure_bet_exist->setHomeName($event_params->getHome())
                                ->setProfitPercent($percent_profit)
                                ->setFirstOddCf($first_value)
                                ->setSecondOddCf(NULL)
                                ->setThirdOddCf($second_value)
                                ->setEventTime($event_params->getEventTime())
                            ;
                        }else{
                            $sureBet = new SureBet();
                            $sureBet->setHomeName($event_params->getHome())
                                ->setAwayName($event_params->getAway())
                                ->setEventName($single_event->getName())
                                ->setProfitPercent($percent_profit)
                                ->setBookmakerFirst($first_key)
                                ->setBookmakerSecond(NULL)
                                ->setBookmakerThird($second_key)
                                ->setTournamentName($single_event->getTournamentName())
                                ->setSportName($event_params->getSportName())
                                ->setFirstOddCf($first_value)
                                ->setSecondOddCf(NULL)
                                ->setThirdOddCf($second_value)
                                ->setFirstOddType($odd_type_1)
                                ->setSecondOddType($odd_type_2)
                                ->setThirdOddType(NULL)
                                ->setEventTime($event_params->getEventTime())
                                ->setEventId($event_params->getEventId())
                                ->setBetId($bet_id)
                                ->setBetType($bet_type)
                            ;

                            $em->persist($sureBet);
                        }

                        $em->flush();
                    }else{
                        $bet_id = hash('md5', $event_params->getEventId() . $first_key . $second_key . $bet_type);
                        $sure_bet_exist = $em->getRepository('AppBundle:SureBet')->findOneByBetId($bet_id);
                        if($sure_bet_exist){
                            $em->remove($sure_bet_exist);
                            $em->flush();
                        }
                    }
                }
            }
        }

    }


    private function event_outcome_two_urls(Parameter & $single_event_params, $json_data_1x2, $json_data_dc, $bet_id, $bet_type){
        //all odds 1x2
        $all_odds_1x2 = self::three_events_odds_execute($json_data_1x2);
//        print '<pre>' . print_r($all_odds_1x2, TRUE) . '</pre>';die();
        $home_win = $all_odds_1x2[0];
        $away_win = $all_odds_1x2[1];
        $draw = $all_odds_1x2[2];

        //all odds double chanses
        $all_odds_dc = self::three_events_odds_execute($json_data_dc);

        $home_win_draw = $all_odds_dc[0];
        $away_win_draw = $all_odds_dc[1];
        $no_draw = $all_odds_dc[2];

        if(!empty($all_odds_1x2) AND !empty($all_odds_dc)){
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
            $sure_cf_max = 1/$home_max + 1/$away_win_draw_max;
            if($sure_cf_max < 1.5){
                self::sure_bet_calculation_two_results($home_win, $away_win_draw, $single_event_params, $bet_id, 'home win', 'away win/draw', $bet_type);
            }

            //draw vs no draw
            $sure_cf_max = 1/$draw_max + 1/$no_draw_max;
            if($sure_cf_max < 1.5){
                self::sure_bet_calculation_two_results($draw, $no_draw, $single_event_params, $bet_id, 'draw', 'no draw', $bet_type);
            }
            //away win vs home draw win
            $sure_cf_max = 1/$away_max + 1/$home_win_draw_max;
            if($sure_cf_max < 1.5){
                self::sure_bet_calculation_two_results($home_win_draw, $away_win, $single_event_params, $bet_id, 'home win/draw', 'away win', $bet_type);
            }
        }

    }


    public function update_event_params($event_id){
        
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
            preg_match_all("#([^,\s]+):([^,\s]+)#s",$at,$out);
            unset($out[0]);
            $out = array_combine($out[1],$out[2]);
            $params += $out;
        }
        $home_away = explode('-', $event_name);

        if(!empty($home_away[0])){
            $home = $home_away[0];
        }else{
            $home = NULL;
        }
        if(!empty($home_away[1])){
            $away = $home_away[1];
        }else{
            $away = NULL;
        }
        $xhash = urldecode($params['xhash']);
        $xhashf = urldecode($params['xhashf']);
//        print '<pre>' . print_r($xhashf, true) . '</pre>'; die();
        if(($params['isStarted']) == 'false'){
            $parameter = $em->getRepository('AppBundle:Parameter')->findOneByEventId($event_id);
            $parameter->setXhash($xhash)
                    ->setXhashf($xhashf);
            $em->flush();
        }
        return $parameter;
    }

    /**
     * @Route("/delete_old_events", name="delte_old_events")
     */
    public function delete_old_events()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $time = new \DateTime();
        $time = $time->modify('+10 minutes');

        //removing from events
        $old_events = $qb->select('u')
            ->from('AppBundle:Event', 'u')
            ->where('u.eventTime <= ?1')
            ->setParameter(1, $time->format('Y-m-d H:i:s'))
            ->getQuery()
            ;
        foreach ($old_events->getResult() AS $old_event){
            $em->remove($old_event);
            $em->flush();
        }

        //removing from parameters
        $old_params = $qb->select('p')
            ->from('AppBundle:Parameter', 'p')
            ->where('u.eventTime <= ?1')
            ->setParameter(1, $time->format('Y-m-d H:i:s'))
            ->getQuery()
        ;
        foreach ($old_params->getResult() AS $old_parameter){
            $em->remove($old_parameter);
            $em->flush();
        }
        print '<pre>' . print_r($old_events->getResult(), TRUE) . '</pre>'; die();
    }

    private function get_contents($url, $referrer = NULL)
    {
        $content = shell_exec('curl -H "Referer: '.$referrer.'" ' . $url);
        if(strpos($content, 'invalidRequest') OR strpos($content, 'notAllowed')){
            throw new Exception('No Data');
        }

        return $content;
    }

    private function curl($url)
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

    private function delete_event($event_id){
        /**
         * Entity manager
         */
        $em = $this->getDoctrine()->getManager();
        //event
        $event = $em->getRepository('AppBundle:Event')->findOneByEventId($event_id);
        if(!empty($event)){
            $em->remove($event);
            $em->flush();
        }
        //parameter
        $parameter = $em->getRepository('AppBundle:Parameter')->findOneByEventId($event_id);
        if(!empty($parameter)){
            $em->remove($parameter);
            $em->flush();
        }
    }
}