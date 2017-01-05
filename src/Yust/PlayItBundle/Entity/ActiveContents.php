<?php

namespace Yust\PlayItBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentsGroup
 *
 * @ORM\Table(name="activeContents")
 * @ORM\Entity
 */
class ActiveContents
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
     * @var integer
     *
     * @ORM\Column(name="contentId", type="integer")
     */
    private $contentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="groupId", type="integer")
     */
    private $groupId;
   
    /**
     * @var integer
     *
     * @ORM\Column(name="contOrder", type="integer")
     */
    private $contOrder;
    
    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="contents")
     * @ORM\JoinColumn(name="contentId", referencedColumnName="id")
     */
    protected $content;
    
    /**
     * @ORM\ManyToOne(targetEntity="GroupTable", inversedBy="activecontents")
     * @ORM\JoinColumn(name="groupId", referencedColumnName="id")
     */
    protected $group;

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
     * Set contentId
     *
     * @param integer $contentId
     * @return ContentsGroup
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;

        return $this;
    }

    /**
     * Get contentId
     *
     * @return integer 
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     * @return ContentsGroup
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }
    
    /**
     * Set order
     *
     * @param integer $contOrder
     * @return ContentsGroup
     */
    public function setContOrder($contOrder)
    {
        $this->contOrder = $contOrder;

        return $this;
    }

    /**
     * Get contOrder
     *
     * @return integer 
     */
    public function getContOrder()
    {
        return $this->contOrder;
    }

    /**
     * Set content
     *
     * @param \Yust\PlayItBundle\Entity\Content $content
     * @return ContentsGroup
     */
    public function setContent(\Yust\PlayItBundle\Entity\Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \Yust\PlayItBundle\Entity\Content 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set group
     *
     * @param \Yust\PlayItBundle\Entity\Group $group
     * @return ActiveContents
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
}
