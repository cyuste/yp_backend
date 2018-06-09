<?php

namespace Yust\PlayItBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Yust\PlayItBundle\Entity\Content;

/**
 * Video
 *
 * @ORM\Table(name="content")
 * @ORM\Entity
 */
class Video extends Content{
    
    public function getThumbWebPath()
    {
        $ext = '.gif';

        return null === $this->path
            ? null
            : $this->getUploadDir().'/'. $this->getUser()->getUsername().'/thumbnails/' . $this->path . $ext;
    }
    
    public function getThumbAbsolutePath()
    {
        $ext = '.gif';
        
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

        $this->getFile()->move($this->getUploadRootDir() .'/'. $this->getUser()->getUsername(), $this->path . '.mp4');
        //$this->recodeVideo(); // Automatically moves the file
        
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
        $file = $this->getAbsolutePath() .'.mp4';
        if ($file) {
            unlink($file);
        }
        $this->removeThumbnail();
    }
    
    public function createThumbnail()
    {
        $command = 'ffmpeg -i ' . $this->getAbsolutePath() .'.mp4' . ' -vf scale=120:-1 -r 1  ' . $this->getThumbAbsolutePath();
        exec($command);
        return 0;
    }
    
    /**
     * TODO: Recode video to ensure is compatible with omxplayer.
     */
    public function recodeVideo()
    {
         $fileOld = $this->getFile();
        // Help ffmpeg adding extension extension to the file. The problem is that
        // guess extension doesn't work very well with video, so by now just check
        // is 'bin' and replace it with 'avi'. Fingers crossed
        $ext =  $fileOld->guessExtension();
        if ($ext === 'bin') {
            $ext = 'avi'; //Else leave it as is. Will it work?
        } 
        $file = $fileOld->move($fileOld->getPath(), $fileOld->getFilename() . "." . $ext);
        $destination = $this->getUploadRootDir() . '/' . $this->getUser()->getUsername() . '/' . $this->path;
        $command = 'ffmpeg -i ' . $file->getPathName() . '  -c:v libx264 -preset slow -crf 22 -c:a copy' . $destination;
        exec($command);
        return 0;
    }
}
