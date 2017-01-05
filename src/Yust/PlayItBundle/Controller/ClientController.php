<?php

namespace Yust\PlayItBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ClientController extends Controller
{
    /**
     * @Route("/getContentList/{id}", name="getContentList")
     * 
     * Used by the viewer to get the active contents for a group, defined by the device {id}
     */
    public function getContentListAction($id)
    {
        $response = new Response();
        $result = array();
        $devices = $this->getDoctrine()->getRepository('YustPlayItBundle:Device');
        $device = $devices->find($id);
        $groupId = $device->getGroup()->getId();
        $contentsRep = $this->getDoctrine()->getRepository('YustPlayItBundle:ActiveContents');
        $contentList = $contentsRep->findBy(array('groupId'=>$groupId), array('contOrder'=>'ASC'));
        foreach ($contentList as $cont) {
            $content = $cont->getContent();
            array_push($result, array(
                'order'     => $cont->getContOrder(),
                'uri'       => '/home/pi/yustplayit_assets/'.$content->getPath(),
                'name'      => $content->getPath(),
                'mimetype'  => $content->getType()->getName(),
                'duration'  => $content->getLength()
            ));
        }
        $response->setContent(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     * @Route("/getConfig", name="getConfg")
     * @Method({"POST","})
     * Used by the viewer to get the config file
     */
    public function getConfigAction(Request $request)
    {
        $response = new Response();
        $publicId = $request->request->get('publicId');
        $devices = $this->getDoctrine()->getRepository('YustPlayItBundle:Device');
        $device = $devices->findOneBy(array('sshKey'=>''));
        if(!$device) {
            $response = new Response();
            $response->setStatusCode(500, "No device found");
            return $response;
        }
        $em = $this->getDoctrine()->getManager();
        $username = $device->getGroup()->getUser()->getUsername();
        $device->setSshKey($publicId);
        $em->flush();
        
        $response->headers->set('Content-Type', 'application/x-sh');
        $response->setContent( $this->render(
                    'YustPlayItBundle:Config:viewer.conf.txt.twig',
                    array('username' => $username,
                          'deviceId' => $device->getId())
                ));   
        return $response;
    }
}

