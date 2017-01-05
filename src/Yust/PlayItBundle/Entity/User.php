<?php

namespace Yust\PlayItBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Yust\PlayItBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     *
     */
    private $roles;
    
 
    /**
     * @Assert\Length(
     *     min = 6,
     *     minMessage = "Password should by at least 6 chars long"
     * )
     */
     protected $plainPassword;
    
    
    /**
     * @ORM\OneToMany(targetEntity="Content", mappedBy="user")
     */
    protected $user_contents;

    /**
     * @ORM\OneToMany(targetEntity="GroupTable", mappedBy="user")
     */
    protected $user_groups;
    
    public function __construct()
    {
        $this->isActive = true;
        $this->roles = new ArrayCollection();
        $this->user_contents = new ArrayCollection();
        $this->user_groups = new ArrayCollection();
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return $this->roles->toArray();
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }
      
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add roles
     *
     * @param \Yust\PlayItBundle\Entity\Role $roles
     * @return User
     */
    public function addRole(\Yust\PlayItBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }
    
    /**
     * Set roles
     *
     * @param \Yust\PlayItBundle\Entity\Role $roles
     * @return User
     */
    public function setRoles(\Yust\PlayItBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Yust\PlayItBundle\Entity\Role $roles
     */
    public function removeRole(\Yust\PlayItBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Add user_contents
     *
     * @param \Yust\PlayItBundle\Entity\Content $userContents
     * @return User
     */
    public function addUserContent(\Yust\PlayItBundle\Entity\Content $userContents)
    {
        $this->user_contents[] = $userContents;

        return $this;
    }

    /**
     * Remove user_contents
     *
     * @param \Yust\PlayItBundle\Entity\Content $userContents
     */
    public function removeUserContent(\Yust\PlayItBundle\Entity\Content $userContents)
    {
        $this->user_contents->removeElement($userContents);
    }

    /**
     * Get user_contents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserContents()
    {
        return $this->user_contents;
    }

    /**
     * Add user_groups
     *
     * @param \Yust\PlayItBundle\Entity\GroupTable $userGroups
     * @return User
     */
    public function addUserGroup(\Yust\PlayItBundle\Entity\GroupTable $userGroups)
    {
        $this->user_groups[] = $userGroups;

        return $this;
    }

    /**
     * Remove user_groups
     *
     * @param \Yust\PlayItBundle\Entity\GroupTable $userGroups
     */
    public function removeUserGroup(\Yust\PlayItBundle\Entity\GroupTable $userGroups)
    {
        $this->user_groups->removeElement($userGroups);
    }

    /**
     * Get user_groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserGroups()
    {
        return $this->user_groups;
    }
}
