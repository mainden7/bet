<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 27.03.17
 * Time: 14:55
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="sport")
 * @ORM\Entity
 */

class Sport
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
     * @ORM\Column(name="sport_id", type="integer")
     */
    private $sportId;

   

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
     * @return Sport
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
     * @return Sport
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
     * @return Sport
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
     * @return Sport
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
