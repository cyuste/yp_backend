<?php

namespace Yust\PlayItBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use Yust\PlayItBundle\Entity\User;

class Registration
{
    /**
     * @Assert\Type(type="Yust\PlayItBundle\Entity\User")
     * 
     */
    protected $user;
    
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
    

}

