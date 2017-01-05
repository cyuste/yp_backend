<?php

namespace Yust\PlayItBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentType
 *
 * @ORM\Table(name="contentType")
 * @ORM\Entity
 */
class ContentType
{
    /**
     * @ORM\OneToMany(targetEntity="Content", mappedBy="type")
     */
    protected $conts_array;
    
    public function __construct()
    {
        $this->conts_array = new ArrayCollection();
    }
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
     * Set name
     *
     * @param string $ty
     * @return ContentType
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
     * Add conts_array
     *
     * @param \Yust\PlayItBundle\Entity\Content $contsArray
     * @return ContentType
     */
    public function addContsArray(\Yust\PlayItBundle\Entity\Content $contsArray)
    {
        $this->conts_array[] = $contsArray;

        return $this;
    }

    /**
     * Remove conts_array
     *
     * @param \Yust\PlayItBundle\Entity\Content $contsArray
     */
    public function removeContsArray(\Yust\PlayItBundle\Entity\Content $contsArray)
    {
        $this->conts_array->removeElement($contsArray);
    }

    /**
     * Get conts_array
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContsArray()
    {
        return $this->conts_array;
    }
}
