<?php

namespace Yust\PlayItBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use Yust\PlayItBundle\Entity\GroupTable;

class AddGroup
{
    /**
     * @Assert\Type(type="Yust\PlayItBundle\Entity\GroupTable")
     * 
     */
    protected $deviceGroup;
    
    public function setGroup(GroupTable $groupTable)
    {
        $this->deviceGroup = $groupTable;
        $this->deviceGroup->setStatus(1); // Matar a Pedro
    }

    public function getGroup()
    {
        return $this->deviceGroup;
    }
}
