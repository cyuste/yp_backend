<?php

namespace Yust\PlayItBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * DevGroup
 *
 * @ORM\Table(name="GroupTable")
 * @ORM\Entity
 */
class GroupTable
{   
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
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    
        
    /**
     * @ORM\OneToMany(targetEntity="ActiveContents", mappedBy="group")
     */
    protected $activecontents;
    
    /**
     * @ORM\OneToMany(targetEntity="Device", mappedBy="group")
     */
    protected $devices;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="user_groups")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    public function __construct()
    {
        $this->activecontents = new ArrayCollection();
        $this->devices = new ArrayCollection();
    }

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
     * @param string $name
     * @return DevGroup
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
     * Set status
     *
     * @param integer $status
     * @return DevGroup
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add activecontents
     *
     * @param \Yust\PlayItBundle\Entity\ActiveContents $activecontents
     * @return Group
     */
    public function addActivecontent(\Yust\PlayItBundle\Entity\ActiveContents $activecontents)
    {
        $this->activecontents[] = $activecontents;

        return $this;
    }

    /**
     * Remove activecontents
     *
     * @param \Yust\PlayItBundle\Entity\ActiveContents $activecontents
     */
    public function removeActivecontent(\Yust\PlayItBundle\Entity\ActiveContents $activecontents)
    {
        $this->activecontents->removeElement($activecontents);
    }

    /**
     * Get activecontents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivecontents()
    {
        return $this->activecontents;
    }

    /**
     * Add devices
     *
     * @param \Yust\PlayItBundle\Entity\Device $devices
     * @return Group
     */
    public function addDevice(\Yust\PlayItBundle\Entity\Device $devices)
    {
        $this->devices[] = $devices;

        return $this;
    }

    /**
     * Remove devices
     *
     * @param \Yust\PlayItBundle\Entity\Device $devices
     */
    public function removeDevice(\Yust\PlayItBundle\Entity\Device $devices)
    {
        $this->devices->removeElement($devices);
    }

    /**
     * Get devices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * Set user
     *
     * @param \Yust\PlayItBundle\Entity\User $user
     * @return GroupTable
     */
    public function setUser(\Yust\PlayItBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Yust\PlayItBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
