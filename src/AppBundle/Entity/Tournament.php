<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 27.03.17
 * Time: 16:18
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tournament")
 * @ORM\Entity
 */
class Tournament
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
     * @ORM\Column(name="sport_id", type="string", length=256)
     */
    private $sportId;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $sportName;

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
     * @return Tournament
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
     * @return Tournament
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
     * Set sportId
     *
     * @param string $sportId
     *
     * @return Tournament
     */
    public function setSportId($sportId)
    {
        $this->sportId = $sportId;

        return $this;
    }

    /**
     * Get sportId
     *
     * @return string
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
     * @return Tournament
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
}
