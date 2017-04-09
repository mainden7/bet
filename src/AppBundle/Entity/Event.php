<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 27.03.17
 * Time: 17:47
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="event")
 * @ORM\Entity
 */

class Event
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $name;

    /**
     * @ORM\Column(name="sport_name", type="string", length=256)
     */
    private $sportName;

    /**
     * @ORM\Column(name="sport_id", type="integer")
     */
    private $sportId;

    /**
     * @ORM\Column(name="tournament_name", type="string", length=256)
     */
    private $tournamentName;

    /**
     * @ORM\Column(name="tournament_url", type="string", length=256)
     */
    private $tournamentUrl;

    /**
     * @ORM\Column(name="event_id", type="string", length=256)
     */
    private $eventId;

    /**
     * @ORM\Column(name="event_time", type="datetime", length=256, nullable=true)
     */
    private $eventTime;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Event
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sportName
     *
     * @param string $sportName
     *
     * @return Event
     */
    public function setSportName($sportName)
    {
        $this->sportName = $sportName;

        return $this;
    }

    /**
     * Get sportName
     *
     * @return string
     */
    public function getSportName()
    {
        return $this->sportName;
    }

    /**
     * Set sportId
     *
     * @param integer $sportId
     *
     * @return Event
     */
    public function setSportId($sportId)
    {
        $this->sportId = $sportId;

        return $this;
    }

    /**
     * Get sportId
     *
     * @return integer
     */
    public function getSportId()
    {
        return $this->sportId;
    }

    /**
     * Set tournamentName
     *
     * @param string $tournamentName
     *
     * @return Event
     */
    public function setTournamentName($tournamentName)
    {
        $this->tournamentName = $tournamentName;

        return $this;
    }

    /**
     * Get tournamentName
     *
     * @return string
     */
    public function getTournamentName()
    {
        return $this->tournamentName;
    }

    /**
     * Set tournamentUrl
     *
     * @param string $tournamentUrl
     *
     * @return Event
     */
    public function setTournamentUrl($tournamentUrl)
    {
        $this->tournamentUrl = $tournamentUrl;

        return $this;
    }

    /**
     * Get tournamentUrl
     *
     * @return string
     */
    public function getTournamentUrl()
    {
        return $this->tournamentUrl;
    }

    /**
     * Set eventId
     *
     * @param string $eventId
     *
     * @return Event
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * Get eventId
     *
     * @return string
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * Set eventTime
     *
     * @param \DateTime $eventTime
     *
     * @return Event
     */
    public function setEventTime($eventTime)
    {
        $this->eventTime = $eventTime;

        return $this;
    }

    /**
     * Get eventTime
     *
     * @return \DateTime
     */
    public function getEventTime()
    {
        return $this->eventTime;
    }
}
