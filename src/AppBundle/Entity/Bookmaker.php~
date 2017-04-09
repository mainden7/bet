<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 27.03.17
 * Time: 17:46
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="bookmaker")
 * @ORM\Entity
 */

class Bookmaker
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $name;

    /**
     * @ORM\Column(name="bookmaker_id", type="string", length=256)
     */
    private $bookmakerId;

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
     * @return Bookmaker
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
     * @return Bookmaker
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
     * Set bookmakerId
     *
     * @param string $bookmakerId
     *
     * @return Bookmaker
     */
    public function setBookmakerId($bookmakerId)
    {
        $this->bookmakerId = $bookmakerId;

        return $this;
    }

    /**
     * Get bookmakerId
     *
     * @return string
     */
    public function getBookmakerId()
    {
        return $this->bookmakerId;
    }
}
