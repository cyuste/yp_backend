<?php
namespace Yust\PlayItBundle\Form\Model;
use Symfony\Component\Validator\Constraints as Assert;
use Yust\PlayItBundle\Entity\Content;
class EditContent
{
    /**
     * @Assert\Type(type="Yust\PlayItBundle\Entity\Content")
     *  
     */
    protected $content;
    public function setContent(Content $content)
    {
        $this->content = $content;
    }
    public function getContent()
    {
        return $this->content;
    }  
}

