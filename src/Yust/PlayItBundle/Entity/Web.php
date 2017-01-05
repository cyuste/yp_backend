<?php

namespace Yust\PlayItBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Yust\PlayItBundle\Entity\Content;

/**
 * Web
 *
 * @ORM\Table(name="content")
 * @ORM\Entity
 */
class Web extends Content{  
    public function getThumbWebPath()
    {
        return $this->getUploadDir().'/web.jpg';   
    }
    
    public function getThumbAbsolutePath()
    {
        $ext = '.jpg';
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->getUser()->getUsername().'/thumbnails/' . $this->path . $ext;
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // Don't do anything regarding the file
        return 0;
    }
    
    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        // Don't do anything regarding the file
        return 0;
    }
    

    

}
