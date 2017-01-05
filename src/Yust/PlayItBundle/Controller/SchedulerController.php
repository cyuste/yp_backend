<?php

namespace Yust\PlayItBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yust\PlayItBundle\Entity\Content;
use Yust\PlayItBundle\Entity\ActiveContents;
use Yust\PlayItBundle\Form\Model\EditContent;
use Yust\PlayItBundle\Form\Type\EditContentType;
use Yust\PlayItBundle\Form\Model\AddContent;
use Yust\PlayItBundle\Form\Type\AddContentType;
use Yust\PlayItBundle\Form\Type\AddWebType;

class SchedulerController extends Controller
{
    /**
     * Populates user field and persists object in db
     */
    private function persistContent(Content $content) {
        $content->setUser($this->getUser());
        $em = $this->getDoctrine()->getManager();     
        $em->persist($content);
        $em->flush();
    }
    /**
     * @Route("/reOrderList/{id}", name="reOrderList", options={"expose"=true})
     * 
     * Used by the main view to update the contents order for a group, defined by {id}
     */
    public function reOrderListAction(Request $request, $id)
    {
        $newOrder = $request->request->get('orderList');
        $activeContents = $this->getDoctrine()->getRepository('YustPlayItBundle:ActiveContents');
        // Updates each element with its new order. Id is unique, it's not necessary 
        // that double condition in the findOneBy.
        foreach ($newOrder as $elem){
            $cont = $activeContents->findOneBy(array('groupId'=>$id,'id'=>$elem[0]));
            $cont->setContOrder($elem[1]);
        }
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        // Return
        $response = '{"code":"OK","message":"OK"}';
        return new Response($response);
    }
    
    /**
     * @Route("/delete/{id}", name="delete", options={"expose"=true})
     * @Method({"POST"})
     * 
     * Delete a content from the assets. Returns the list of available assets.
     */
    public function deleteContentAction(Request $request, $id)
    {
        $groupId = $request->request->get('groupId');
        // The content is deleted from the contents table
        $contents = $this->getDoctrine()->getRepository('YustPlayItBundle:Content');
        $content = $contents->find($id); 
        $em = $this->getDoctrine()->getManager();
        $em->remove($content);

        // Remove the content from the playlists
        $activeContents = $this->getDoctrine()->getRepository('YustPlayItBundle:ActiveContents');
        $contents_on = $activeContents->findBy(array('contentId'=>$id,));
        foreach ($contents_on as $cont) {
            $em->remove($cont);
        }
        
        // Flush
        $em->flush();  
        // The file is automatically removed thanks to the callback functions
        
        // Return     
        $all_conts = $contents->findBy(array('user' => $this->getUser()));
        $act_conts = $activeContents->findBy(array('groupId' => $groupId));
        return $this->render('YustPlayItBundle:Client:assetsTables.html.twig',array(
            'contents_all' => $all_conts,
            'contents_on' => $act_conts,
            'current_group_id' => $groupId            
        ));
    }
    
    /**
     * @Route("/webContent/{id}", name="webContent", options={"expose"=true})
     * 
     * Form manager for web contents
     */
    public function webContentAction(Request $request, $id)
    {
        $newWeb = new AddContent('3',$this->getDoctrine()); // 3 is the web code
        $form = $this->createForm(new AddWebType(), $newWeb);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $content = $form->getData()->getContent();
            $this->persistContent($content);
            
            // Return
            $contents = $this->getDoctrine()->getRepository('YustPlayItBundle:Content');
            $all_conts = $contents->findBy(array('user' => $this->getUser()));
            return $this->render('YustPlayItBundle:Client:allList.html.twig',array(
                'contents_all'  => $all_conts,
                'current_group_id' => $id            
            ));
        } else {
            return $this->render("YustPlayItBundle:Client:newWebContentForm.html.twig",array(
                'form' => $form->createView(),
            ));
        }       
    }
    
    /**
     * @Route("/getUpload/{id}", name="upload", options={"expose"=true})
     * 
     * Upload form creator.
     */
    public function uploadAction()
    {
        $newContent = new AddContent();
        //$newContent->getContent()->setUser($this->getUser());
        $form = $this->createForm(new AddContentType(), $newContent);
        return $this->render("YustPlayItBundle:Client:newContentForm.html.twig",array(
            'form' => $form->createView(),
        ));     
    }
    
    /**
     * @Route("/uploadContent/{id}", name="uploadContent", options={"expose"=true})
     * 
     * Upload presentation manager. Returns the list of available assets.
     */
    public function uploadContentAction(Request $request, $id)
    {
        $type = $request->request->get('newContent')['content']['type'];
        $newContent = new AddContent($type);
        $form = $this->createForm(new AddContentType(), $newContent);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $content = $form->getData()->getContent();
            $this->persistContent($content);
       
            // Return
            $contents = $this->getDoctrine()->getRepository('YustPlayItBundle:Content');
            $all_conts = $contents->findBy(array('user' => $this->getUser()));
            return $this->render('YustPlayItBundle:Client:allList.html.twig',array(
                'contents_all'  => $all_conts,
                'current_group_id' => $id            
            ));
        } else {
            return $this->render("YustPlayItBundle:Client:newContentForm.html.twig",array(
                'form' => $form->createView(),
            ));
        }       
    }
    
    
    /**
     * @Route("/edit/{id}", name="edit", options={"expose"=true})
     * 
     * Edit Content. Returns OK
     */
    public function editContentAction(Request $request, $id)
    {
        $conts = $this->getDoctrine()->getRepository('YustPlayItBundle:Content');
        $cont = $conts->find($id);
        if($cont && ($cont->getUser()->getId() == $this->getUser()->getId())) {
            $editContent = new EditContent();
            $editContent->setContent($cont);
            $form = $this->createForm(new EditContentType(),$editContent); 
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $content = $form->getData()->getContent();
                $em->persist($content);
                $em->flush();
                $response = new Response();
                $response->setStatusCode(200, "Done");
                return $response;           
            } else {
                return $this->render("YustPlayItBundle:Client:editContentForm.html.twig",array(
                    'form' => $form->createView(),
                    'thumbnail' => $cont->getThumbWebPath(),
                ));
            }
        } else {
            $response = new Response();
            $response->setStatusCode(500, "The user doesn't own the content");
            return $response;
        }
    }
    
    /**
     * @Route("/turnOffContent/{id}", name="turnOffContent", options={"expose"=true})
     * 
     * Deactivates a content for a specific group
     */
    public function turnOffContentAction($id)
    {    
        $em = $this->getDoctrine()->getManager();
        $activeContents = $this->getDoctrine()->getRepository('YustPlayItBundle:ActiveContents'); 
        $activeCont = $activeContents->find($id);
        $order = $activeCont->getContOrder();
        $groupId = $activeCont->getGroup()->getId();
        if ($activeCont->getGroup()->getUser()->getId() == $this->getUser()->getId()) {
            $em->remove($activeCont);  // Should be unique   
            // Re-order the following contents
            $query = $activeContents->createQueryBuilder('p')
                    ->where('p.contOrder > :contOrder')
                    ->setParameter('contOrder', $order)
                    ->orderBy('p.contOrder','ASC')
                    ->getQuery();
            $next_conts = $query->getResult();
            foreach ($next_conts as $ncont){
                $ncont->setContOrder($ncont->getContOrder() - 1);
            }
            $em->flush();     
            $contents_on = $activeContents->findBy(array('groupId'=>$groupId,),array('contOrder'=>'ASC'));
            return $this->render('YustPlayItBundle:Client:activeList.html.twig',array(
                'contents_on'  => $contents_on,
                'current_group_id' => $groupId            
            ));
        } else {
            // 500 forbidden
            $response = new Response();
            $response->setStatusCode(500, "The user doesn't own the content");
            return $response;
        }    
    }
    
    /**
     * @Route("/turnOnContent/{id}/{groupId}", name="turnOnContent", options={"expose"=true})
     * 
     * Activates a content for a specific group
     */
    public function turnOnContentAction($id, $groupId)
    {    
        $em = $this->getDoctrine()->getManager();
        $activeContents = $this->getDoctrine()->getRepository('YustPlayItBundle:ActiveContents'); 
        $contents = $this->getDoctrine()->getRepository('YustPlayItBundle:Content'); 
        $groupsRep = $this->getDoctrine()->getRepository('YustPlayItBundle:GroupTable');
        $group = $groupsRep->find($groupId);
        $cont = $contents->find($id);
        if ($cont->getUser()->getId() == $this->getUser()->getId()) {        
            $last_content = $activeContents->findOneBy(array('groupId'=>$groupId,), array('contOrder'=>'DESC'));
            if ($last_content){
                $last_order = $last_content->getContOrder();
                $order = $last_order + 1;
            } else {
                // Active list was empty
                $order = 1;
            }
            $newActive = new ActiveContents();
            $newActive->setContent($cont);
            $newActive->setGroup($group);
            $newActive->setContOrder($order);
            $em->persist($newActive);
            $em->flush();     
            $contents_on = $activeContents->findBy(array('groupId'=>$groupId,),array('contOrder'=>'ASC'));
            return $this->render('YustPlayItBundle:Client:activeList.html.twig',array(
                'contents_on'  => $contents_on,
                'current_group_id' => $groupId            
            ));
        } else {
            // 500 forbidden
            $response = new Response();
            $response->setStatusCode(500, "The user doesn't own the content");
            return $response;
        }    
    }
    
    /**
     * @Route("/{groupId}", defaults={"groupId"="0"}, name="homepage", options={"expose"=true})
     * 
     * Main view
     */
    public function indexAction($groupId)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
             return $this->redirectToRoute('users_route');
        }
            
        $groupsRep = $this->getDoctrine()->getRepository('YustPlayItBundle:GroupTable');
        
        if ($groupId == 0){
            $default_group = $groupsRep->findOneBy(array('user'=>$user));
            $group = $default_group;
        } else {
            // Check if the group belong to the user
           $group = $groupsRep->find($groupId);
           // TODO: Check group != null?
           if ($group->getUser() != $user) {
               throw $this->createAccessDeniedException();
           }
        }
        
        $activeContentsRep = $this->getDoctrine()->getRepository('YustPlayItBundle:ActiveContents');
        $contentsOn = $activeContentsRep->findBy(array('group'=>$group,),array('contOrder'=>'ASC'));
        $contentsRep = $this->getDoctrine()->getRepository('YustPlayItBundle:Content');
        $contents = $contentsRep->findBy(array('user'=>$user));      

        $group = $groupsRep->find($group->getId());
        
        // Sacar los nombre de todos los grupos
        $groups = $groupsRep->findBy(array('user'=>$user)); 

        return $this->render('YustPlayItBundle:Client:overview.html.twig', array(
            'contents_on'  => $contentsOn,
            'contents_all' => $contents,
            'groups'       => $groups,
            'current_group_id'     => $group->getId(),
            'current_group_name'   => $group->getName()             
        ));
    }
}
