<?php

namespace Yust\PlayItBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Content
 *
 * @ORM\Table(name="content")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type_disc", type="string")
 * @ORM\DiscriminatorMap({ "content" = "Content", "image" = "Image", "video" = "Video", "web" = "Web", "presentation" = "Presentation" })
 */
class Content
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="length", type="integer")
     */
    protected $length;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="type_id", type="integer")
     */
    protected $type_id;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=1024)
     */
    protected $path;
    
    
    /**
     * @Assert\File(maxSize="60M")
     */
    protected $file;
    
    
    /**
     *
     * Copied from the tutorial, dunno know
     */
    protected $temp;
    
    protected $scale;
    
    /**
     * @ORM\OneToMany(targetEntity="ActiveContents", mappedBy="content")
     */
    protected $contents;
    
    /**
     * @ORM\ManyToOne(targetEntity="ContentType", inversedBy="conts_array")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    protected $type;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="user_contents")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    


    public function __construct()
    {
        $this->contents = new ArrayCollection();
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
     * @return Content
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
     * Set length
     *
     * @param integer $length
     * @return Content
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length
     *
     * @return integer 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Content
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get contents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContents()
    {
        return $this->contents;
    }
    
    /**
     * Set scale
     *
     * @return boolean
     */
    public function setScale($scale)
    {
        $this->scale = $scale;
        
        return $this;
    }
    
    /**
     * Get scale
     *
     * @return boolean
     */
    public function getScale()
    {
        return $this->scale;
    }
    
    /**
     * Add contents
     *
     * @param \Yust\PlayItBundle\Entity\ActiveContents $contents
     * @return Content
     */
    public function addContent(\Yust\PlayItBundle\Entity\ActiveContents $contents)
    {
        $this->contents[] = $contents;

        return $this;
    }

    /**
     * Remove contents
     *
     * @param \Yust\PlayItBundle\Entity\ActiveContents $contents
     */
    public function removeContent(\Yust\PlayItBundle\Entity\ActiveContents $contents)
    {
        $this->contents->removeElement($contents);
    }
    
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'. $this->getUser()->getUsername().'/'.$this->path;
    }
    
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }
    
    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }
    
    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            // TODO: Revisar esto, no me mola mucho.
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename;
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }
    }
    
    /**
     * Remove Thumbnail.
     */
    protected function removeThumbnail()
    {
        $thumb = $this->getThumbAbsolutePath();
        if ($thumb) {
            unlink($thumb);
        }  
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if($this->getType()->getName() === "image" || $this->getType()->getName() === "video" ) {
            $file = $this->getAbsolutePath();
            if ($file) {
                unlink($file);
            }
            $this->removeThumbnail();
        } elseif ($this->getType()->getName() === "presentation") {
            $files = glob($this->getAbsolutePath().'/*');
            foreach($files as $file) {
                unlink($file);
            }
            rmdir($this->getAbsolutePath());
            $this->removeThumbnail();
        } 
    }
    
    /**
     * Set type_id
     *
     * @param integer $typeId
     * @return Content
     */
    public function setTypeId($typeId)
    {
        $this->type_id = $typeId;

        return $this;
    }

    /**
     * Get type_id
     *
     * @return integer 
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * Set type
     *
     * @param \Yust\PlayItBundle\Entity\ContentType $type
     * @return Content
     */
    public function setType(\Yust\PlayItBundle\Entity\ContentType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Yust\PlayItBundle\Entity\ContentType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set user
     *
     * @param \Yust\PlayItBundle\Entity\User $user
     * @return Content
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
