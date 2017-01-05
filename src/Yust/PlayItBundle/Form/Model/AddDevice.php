<?php

namespace Yust\PlayItBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use Yust\PlayItBundle\Entity\Device;

class AddDevice
{
    /**
     * @Assert\Type(type="Yust\PlayItBundle\Entity\Device")
     * 
     */
    protected $device;
    
    public function setDevice(Device $device)
    {
        $this->device = $device;
        $this->device->setStatus(1); // Matar a Pedro
    }

    public function getDevice()
    {
        return $this->device;
    }
}

