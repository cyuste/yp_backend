<?php
namespace Yust\PlayItBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword
{
    /**
    * @SecurityAssert\UserPassword(
    *     message = "Contraseña actual incorrecta"
    * )
    */
    protected $oldPassword;

    /**
    * @Assert\Length(
    *     min = 6,
    *     minMessage = "Password should by at least 6 chars long"
    * )
    */
    protected $newPassword;
    
    public function setOldPassword($password)
    {
        $this->oldPassword = $password;
    }

    public function getOldPassword()
    {
        return $this->oldPassword;
    }
    
    public function setNewPassword($password)
    {
        $this->newPassword = $password;
    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }
}