<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExecuteController extends Controller
{
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
        
        return new JsonResponse($all_odds);

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

        return new JsonResponse($all_odds);
    }

    public function bothToScoreOddsExecute($json_data){
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

        return new JsonResponse($all_odds);
    }

    public function twoOutcomesOddsExecute($json_data, $bet_type)
    {

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
            $result = $all_odds;
        } else {
            $result = NULL;
        }

        return new JsonResponse($result);


    }
}