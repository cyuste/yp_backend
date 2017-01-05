<?php
namespace Yust\PlayItBundle\Utils;

class fileManager
{   
    var $contents;
    var $em;
    var $controller;
    
    function __construct ()
    {
        $this->$contents = $this->$doctrine->getRepository('YustPlayItBundle:Content');
        $this->$em = get('doctrine')->getManager();
    }
   
    public function remove($id)
    {
        // TODO: just an example
        $content = findBy(array('id'=>$id,)); // Must be unique
        foreach ($content as $cnt){
            remove($cnt);
        }     
    }
    
    public function add($form)
    {
        // New object and persist it
        return "OK";
    }
}


