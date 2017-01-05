<?php
namespace Yust\PlayItBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Yust\PlayItBundle\Entity\User;

class ResetPassword
{
    protected $username;
    protected $email;
    protected $user;
    
    public function getUsername() {
        return $this->username;
    }
    
    public function setUsername($username) {
        $this->username = $username;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }

    public function resetPasswd(\Yust\PlayItBundle\Entity\User $user) {
        $length = 10;
        $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $randomString);
        $user->setPassword($encoded);        
        return $user;
    }

}

