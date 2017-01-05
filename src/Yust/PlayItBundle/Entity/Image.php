<?php

namespace Yust\PlayItBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Yust\PlayItBundle\Entity\Content;

/**
 * Image
 *
 * @ORM\Table(name="content")
 * @ORM\Entity
 */
class Image extends Content {
    
    public function getThumbWebPath()
    {
        $ext = '.jpg';
        
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'. $this->getUser()->getUsername().'/thumbnails/' . $this->path . $ext;
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
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        if ($this->getScale()){
            $this->expandImage(); // Automatically moves the file
        }
        
        $this->createThumbnail();
        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }
    
    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
        $this->removeThumbnail();
    }
    
    /**
     * Scale the image and saves in assets folder.
     * Scales the image to 1920x1080 (keeping ratio)
     */
    public function expandImage()
    {
        $fileOld = $this->getFile();
        // Help ffmpeg adding a image extension to the file, now in the form
        // xxxx.php
        $file = $fileOld->move($fileOld->getPath(), $fileOld->getFilename() . "." . $fileOld->guessExtension());
        $destination = $this->getUploadRootDir() . '/' . $this->getUser()->getUsername() . '/' . $this->path;
        $command = 'ffmpeg -i ' . $file->getPathName() . ' -vf scale="\'if(gt(a,16/9),1920,-1)\':\'if(gt(a,16/9),-1,1080)\'" -f image2 ' . $destination;
        exec($command);
        return 0;
    }
    
    public function createThumbnail()
    {   
        $command = "ffmpeg -i " . $this->getAbsolutePath() . " -vf scale=120:-1 " . $this->getThumbAbsolutePath(); 
        exec($command);
        return 0;
    }
    
}
