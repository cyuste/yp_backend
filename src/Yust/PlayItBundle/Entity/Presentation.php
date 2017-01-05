<?php

namespace Yust\PlayItBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Yust\PlayItBundle\Entity\Content;

/**
 * Presentation
 *
 * @ORM\Table(name="content")
 * @ORM\Entity
 */
class Presentation extends Content {
    
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
        $this->unzip();
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
     * Unzip the presentation bundle to its final destination
     */
    public function unzip()
    {
        $destination = $this->getUploadRootDir() . '/' . $this->getUser()->getUsername() . '/' . $this->path;
        $type = $this->getFile()->guessExtension();
        switch ($type) {
            case 'zip':
                $command = 'unzip ' . $this->getFile()->getPathname() . ' -d ' . $destination;
                break;
            #case 'rar':
            #    $command = 'unrar';
            #    break;
            case 'pdf':
                mkdir($destination);
                $command = "convert " . $this->getFile()->getPathname() . " $destination/slide.jpg";
                return; 
        }
        // This is only required in compressed folder situations. TODO: Improve
        // this loop to get the files no matter the exact folder architecture.
        exec($command);  
        $results = scandir($destination);
        foreach ($results as $result) { //Hopefully there's only one
            if ($result === '.' or $result === '..') continue;

            if (is_dir($destination . '/' . $result)) {
                $command = "mv " . escapeshellarg("$destination/$result") . "/* " . escapeshellarg($destination);
                exec($command);
                $command = "rm -rf " . escapeshellarg("$destination/$result");
                exec($command);
            }
        }      
    }
    
    /*
     * Creates a Thumbnail to display in the Edit view
     */
    public function createThumbnail()
    {
        $results = scandir($this->getAbsolutePath());
        foreach ($results as $result) { //Get the first slide, or anyone
            if ($result != '.' and $result != '..') break;
        }
        $command = "ffmpeg -i " . $this->getAbsolutePath() . '/' . $result . " -vf scale=120:-1 " . $this->getThumbAbsolutePath(); 
        exec($command);

        return 0;
    }
}
