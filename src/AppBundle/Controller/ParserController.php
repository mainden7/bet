<?php

namespace AppBundle\Controller;

//require_once __DIR__.'/vendor/autoload.php';
use AppBundle\Entity\Bookmaker;
use AppBundle\Entity\Event;
use AppBundle\Entity\Parameter;
use AppBundle\Entity\Sport;
use AppBundle\Entity\Tournament;
use Doctrine\Orm\Query as Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector;
use Doctrine\ORM\Query\ResultSetMapping;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Validator\Constraints\DateTime;

class ParserController extends Controller
{
    public function test(){
        return new Response('123');
    }
    /**
     * @Route("/find_sports", name="find_sports")
     * @return Response
     */
    public function findSports()
    {
        //delete all from table first
        $em = $this->getDoctrine()->getManager();
        $em->createQuery('DELETE FROM AppBundle\Entity\Sport')->execute();

        $url = "http://www.oddsportal.com";
        $content = self::curl($url);

        $crawler = new Crawler($content);
        $crawler->filter('a.siconleft')->each(function (Crawler $node, $i){
            $link  = $node->attr('href');
            $name = $node->text();
            $class = $node->attr('class');
            $sport_id = substr($class, strrpos($class, 's'));
            $sport_id = substr($sport_id, 1);

            //save sport to database
            $sport = new Sport();
            $sport->setName($name)
                ->setUrl('http://oddsportal.com' . $link)
                ->setSportId($sport_id);

            $em = $this->getDoctrine()->getManager();
            $em->persist($sport);
            $em->flush();
        });
//        print '<xmp>' . print_r($cr, true) . '</xmp>'; die();

        return new Response('success');
    }


    /**
     * @Route("/find_tournaments")
     * @return Response
     */
    public function findTournaments()
    {
        $sports = $this->getDoctrine()
            ->getRepository('AppBundle:Sport')
            ->createQueryBuilder('e')
            ->select('e')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        foreach ($sports AS $sport){
            $url = $sport['url'];
            $data = self::curl($url);

            $crawler = new Crawler($data);

            $result = $crawler->filter('table.sportcount > tbody')->each(function(Crawler $node)  use ($sport){
                $links = $node->filter('a')->each(function(Crawler $a) use ($sport){
                   $href = $a->attr('href');
                   if($href !== $sport['url']){
                       $res = $href;
                   }
                   return $res;
               });

                $links_to_del = $node->filter('a.bfl')->each(function(Crawler $a) use ($sport){
                    $href_to_del = $a->attr('href');
                    if($href_to_del !== $sport['url']){
                        $res = $href_to_del;
                    }
                    return $res;
                });
                $all_single_sport_tournaments = array_diff($links, $links_to_del);
                foreach ($all_single_sport_tournaments as $single_tournament){
                    $name = $name = substr($single_tournament, strrpos($single_tournament, '/'));
                    $name = substr($name, 1);

                    $em = $this->getDoctrine()->getManager();
                    $tournament_exist = $em->getRepository('AppBundle:Tournament')->findOneByUrl('http://oddsportal.com' . $single_tournament);

                    if(!$tournament_exist){
                        $tournament = new Tournament();
                        $tournament->setName($name)
                            ->setUrl('http://oddsportal.com' . $single_tournament)
                            ->setSportId($sport['sportId'])
                            ->setSportName($sport['name']);

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($tournament);
                        $em->flush();
                    }
                }
            });
        }

        return new Response('success');

//        print '<pre>' . print_r($result, true) . '</pre>'; die();
    }

    /**
     * @Route("/find_events")
     * @return Response
     */

    public function findAllEvents($number = null)
    {
        if(is_numeric($number)){
            $limit = 100;
            $offset = $number * 100;
            $tournaments = $this->getDoctrine()->getManager()->getRepository('AppBundle:Tournament')->findBy(array(), array('id' => 'ASC'), $limit, $offset);
        }elseif (is_string($number)){
            $tournaments = $this->getDoctrine()->getManager()->getRepository('AppBundle:Tournament')->findBySportName($number);
        }

//        print '<pre>' . print_r($tournaments, true) . '</pre>'; die();
//        $i = 1;
        foreach ($tournaments as $tournament){
//            if($i > 1){
//                break;
//            }
//            $i++;
            if(!empty($tournament->getUrl())){

                $url = $tournament->getUrl();
                $data = self::curl($url);

                $crawler = new Crawler($data);
                $crawler->filter('.table-main tbody tr.odd')->each(function(Crawler $node) use($tournament) {
//                    echo '<xmp>' . $node->text() . '</xmp>';die();
                    if (!strpos($node->attr('class'), 'deactivate') AND !strpos($node->html(), 'live-score')) {
                        //find date
                        $event_date = $node->filter('td.datet')->each(function (Crawler $date) {
                            $date_str = $date->attr('class');
                            $substr = strrchr($date_str, 't');
                            preg_match('/t(.*?)-/', $substr, $event_date_unix);
                            $event_date = gmdate('Y-m-d H:i:s', $event_date_unix[1]);

                            return $event_date;
                        });
                        $ev_params = $node->filter('.table-participant a')->each(function (Crawler $a) use ($tournament) {
                            $event_link = $a->attr('href');
                            $st = substr($event_link, strpos($event_link, $tournament->getName()));
                            $st = substr($st, strpos($st, '/'));
                            $st = str_replace('/', '', $st);

                            $event_name = $a->text();

                            //event id
                            $event_id = substr($st, strrpos($st, '-'));
                            $event_id = substr($event_id, 1);
                            if (strpos($event_link, 'void') === FALSE) {
                                return array('event_link' => $event_link, 'event_name' => $event_name, 'event_id' => $event_id);
                            }
                        });
                        $event_params = array();
                        foreach ($ev_params AS $new_params){
                            if(!empty($new_params)){
                                $event_params[] = $new_params;
                            }
                        }
//                        print '<pre>' . print_r($ev_params, true) . '</pre>';die();
                        if(!empty($event_params)){
                            $event_date = $event_date[0];
                            $event_id = $event_params[0]['event_id'];
                            $event_name = $event_params[0]['event_name'];
                            $event_link = $event_params[0]['event_link'];
                            $event = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findByEventId($event_id);
//
                            if(empty($event)) {
                                $event = new Event();
                                $event->setSportId($tournament->getSportId())
                                    ->setSportName($tournament->getSportName())
                                    ->setTournamentName($tournament->getName())
                                    ->setTournamentUrl($tournament->getUrl())
                                    ->setName($event_name)
                                    ->setEventId($event_id)
                                    ->setEventTime(new \DateTime($event_date))
                                    ->setUrl('http://oddsportal.com' . $event_link);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($event);
                                $em->flush();
                            }
                        }
//                        print '<pre>' . print_r($event_params, true) . '</pre>'; die();
                    }

                });
            }
        }
        return new Response('success');
    }

    /**
     * @Route("/params_execute", name="params_execute")
     * @return Response
     */
    public function paramsExecute($number = null)
    {
        $limit = 100;
        $offset = $number * 100;

        //load events
        $all_events = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findBy(array(), array('id' => 'ASC'), $limit, $offset);

        foreach ($all_events as $single_event) {

            if ($single_event->getEventTime() >= new \DateTime()) {

                $sport_id = $single_event->getSportId();
                $sport_name = $single_event->getSportName();
                $event_url = $single_event->getUrl();
                $event_id = $single_event->getEventId();
                $version_id = 1;
                $event_date = $single_event->getEventTime();

                $data = self::curl($event_url);

//            print '<pre>' . print_r(($event_date[0] <= $date_future->format('Y-m-d H:i:s') ? 'true' : 'false'), true) . '</pre>'; die();
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

                $home_away = explode('-', $single_event->getName());

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
                if (($params['isStarted']) == 'false') {
                    $em = $this->getDoctrine()->getManager();
                    $params_exist = $em->getRepository('AppBundle:Parameter')->findOneByEventId($event_id);
                    if($params_exist){
                        $params_exist->setXhash($xhash)
                            ->setXhashf($xhashf)
                        ;
                    }else{
                        $parameter = new Parameter();
                        $parameter->setSportId($sport_id)
                            ->setSportName($sport_name)
                            ->setEventUrl($event_url)
                            ->setEventId($event_id)
                            ->setVersionId($version_id)
                            ->setHome($home)
                            ->setAway($away)
                            ->setXhash($xhash)
                            ->setXhashf($xhashf)
                            ->setEventTime($event_date);

                        $em->persist($parameter);
                    }



                    $em->flush();

                }
            }else{
                $em = $this->getDoctrine()->getManager();
                $event = $em->getRepository('AppBundle:Event')->findOneByEventId($single_event->getEventId());
                if($event){
                    $em->remove($single_event);
                    $em->flush();
                }

                $param = $em->getRepository('AppBundle:Parameter')->findOneByEventId($single_event->getEventId());
                if($param) {
                    $em->remove($param);
                    $em->flush();
                }

            }
        }


        return new Response('success');
    }

    /**
     * @Route("/save_bookmakers", name="save_bookmakers")
     * @return Response
     */

    public function saveBookmaker()
    {

        $url = 'http://www.oddsportal.com/res/x/bookies-160602144944-1464932634.js';
        $str = file_get_contents($url);
        $str1 = substr($str, 20, -164);
        $arr = explode('}', $str1);
//        print '<pre>' . print_r($arr, true) . '</pre>'; die();
        foreach ($arr as $array) {
            $strpos = strpos($array, '{');
            $substr = substr($array, $strpos);
            $substr = substr($substr, 1);
            stripslashes($substr);
            $ar = explode(',', $substr);
            $params = array();
            foreach ($ar as $key => $value) {
                # code...

                $at = str_replace('"', '', $value);
                preg_match_all("#([^,\s]+):([^,\s]+)#s", $at, $out);
                unset($out[0]);
                $out = array_combine($out[1], $out[2]);
                $params += $out;
            }
            if (!empty($params['Url:http'])) {
                $book_url = $params['Url:http'];
                $s = stripslashes($book_url);
                $s = substr($s, strpos($s, '.'));
                $s = substr($s, 1);
                $s = strstr($s, '/', TRUE);
            }
            if (!empty($params['idProvider']) AND !empty($params['WebName'])) {
                if (strlen($s) <= 3) {
                    $s = NULL;
                }
                $bookmaker = new Bookmaker();
                $bookmaker->setUrl($s);
                $bookmaker->setName($params['WebName']);
                $bookmaker->setBookmakerId($params['idProvider']);

                $em = $this->getDoctrine()->getManager();
                $em->persist($bookmaker);
                $em->flush();
            }
        }

        return new Response('success');
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
}