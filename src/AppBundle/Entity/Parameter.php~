<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 28.03.17
 * Time: 17:08
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="parameter")
 * @ORM\Entity
 */

class Parameter
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="sport_id", type="integer")
     */
    private $sportId;

    /**
     * @ORM\Column(name="sport_name", type="string", length=256)
     */
    private $sportName;

    /**
     * @ORM\Column(name="event_url", type="string", length=256)
     */
    private $eventUrl;

    /**
     * @ORM\Column(name="event_id", type="string", length=256)
     */
    private $eventId;

    /**
     * @ORM\Column(name="version_id", type="integer")
     */
    private $versionId;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $home;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $away;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $xhash;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $xhashf;

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
     * Set sportId
     *
     * @param integer $sportId
     *
     * @return Parameter
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
     * Set sportName
     *
     * @param string $sportName
     *
     * @return Parameter
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
     * Set eventUrl
     *
     * @param string $eventUrl
     *
     * @return Parameter
     */
    public function setEventUrl($eventUrl)
    {
        $this->eventUrl = $eventUrl;

        return $this;
    }

    /**
     * Get eventUrl
     *
     * @return string
     */
    public function getEventUrl()
    {
        return $this->eventUrl;
    }

    /**
     * Set eventId
     *
     * @param string $eventId
     *
     * @return Parameter
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
     * Set versionId
     *
     * @param integer $versionId
     *
     * @return Parameter
     */
    public function setVersionId($versionId)
    {
        $this->versionId = $versionId;

        return $this;
    }

    /**
     * Get versionId
     *
     * @return integer
     */
    public function getVersionId()
    {
        return $this->versionId;
    }

    /**
     * Set home
     *
     * @param string $home
     *
     * @return Parameter
     */
    public function setHome($home)
    {
        $this->home = $home;

        return $this;
    }

    /**
     * Get home
     *
     * @return string
     */
    public function getHome()
    {
        return $this->home;
    }

    /**
     * Set away
     *
     * @param string $away
     *
     * @return Parameter
     */
    public function setAway($away)
    {
        $this->away = $away;

        return $this;
    }

    /**
     * Get away
     *
     * @return string
     */
    public function getAway()
    {
        return $this->away;
    }

    /**
     * Set xhash
     *
     * @param string $xhash
     *
     * @return Parameter
     */
    public function setXhash($xhash)
    {
        $this->xhash = $xhash;

        return $this;
    }

    /**
     * Get xhash
     *
     * @return string
     */
    public function getXhash()
    {
        return $this->xhash;
    }

    /**
     * Set xhashf
     *
     * @param string $xhashf
     *
     * @return Parameter
     */
    public function setXhashf($xhashf)
    {
        $this->xhashf = $xhashf;

        return $this;
    }

    /**
     * Get xhashf
     *
     * @return string
     */
    public function getXhashf()
    {
        return $this->xhashf;
    }

    /**
     * Set eventTime
     *
     * @param \DateTime $eventTime
     *
     * @return Parameter
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
