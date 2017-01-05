<?php

namespace Yust\PlayItBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Device
 *
 * @ORM\Table(name="device")
 * @ORM\Entity
 */
class Device
{
    /**
     * @ORM\ManyToOne(targetEntity="GroupTable", inversedBy="devices")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    protected $group;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ssh_key", type="string", length=512)
     */
    private $sshKey;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32)
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
     * Set status
     *
     * @param string $status
     * @return Device
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set group
     *
     * @param \Yust\PlayItBundle\Entity\GroupTable $group
     * @return Device
     */
    public function setGroup(\Yust\PlayItBundle\Entity\GroupTable $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Yust\PlayItBundle\Entity\GroupTable
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set sshKey
     *
     * @param string $sshKey
     * @return Device
     */
    public function setSshKey($sshKey)
    {
        $this->sshKey = $sshKey;

        return $this;
    }

    /**
     * Get sshKey
     *
     * @return string 
     */
    public function getSshKey()
    {
        return $this->sshKey;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Device
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
}
