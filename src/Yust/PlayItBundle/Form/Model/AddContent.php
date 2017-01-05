<?php

namespace Yust\PlayItBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use Yust\PlayItBundle\Entity\Content;
use Yust\PlayItBundle\Entity\Image;
use Yust\PlayItBundle\Entity\Video;
use Yust\PlayItBundle\Entity\Web;
use Yust\PlayItBundle\Entity\Presentation;

class AddContent
{
    /**
     * @Assert\Type(type="Yust\PlayItBundle\Entity\Content")
     * 
     */
    protected $content;
    
    protected $scale;
    
    public function __construct($type=null, $doctrine=null)
    {
        switch ($type) {
            case '1': // image
                $this->content = new Image();
                break;
            case '2': // video
                $this->content = new Video();         
                break;
            case '3': // web
                $this->content = new Web();
                $webType = $doctrine->getRepository('YustPlayItBundle:ContentType')->find(3); // Web Type
                $this->content->setType($webType);
                break;
            case '4': // presentation
                $this->content = new Presentation();    
                break;
            default:
                $this->content = new Content();
                break;
        }
    }

    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
    
    public function setScale($scale)
    {
        $this->scale = $scale;
    }

    public function getScale()
    {
        return $this->scale;
    }
   
}

