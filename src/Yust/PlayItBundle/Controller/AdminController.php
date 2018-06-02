<?php

namespace Yust\PlayItBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Yust\PlayItBundle\Entity\User;
use Yust\PlayItBundle\Entity\GroupTable;
use Yust\PlayItBundle\Form\Type\RegistrationType;
use Yust\PlayItBundle\Form\Model\Registration;
use Yust\PlayItBundle\Form\Type\AddGroupType;
use Yust\PlayItBundle\Form\Model\AddGroup;
use Yust\PlayItBundle\Form\Type\AddDeviceType;
use Yust\PlayItBundle\Form\Model\AddDevice;

class AdminController extends Controller
{  
    /**
     * @Route("/admin/users",  name="users_route", options={"expose"=true})
     * 
     * Main view for admin user
     */
    public function userAdminAction()
    {
        $users_rep = $this->getDoctrine()->getRepository('YustPlayItBundle:User');
        $users = $users_rep->findAll();
        return $this->render('YustPlayItBundle:Admin:main.html.twig', array(
            'users'  => $users,            
        ));
    }
   
    /**
     * @Route("/admin/groups/{userId}", defaults={"userId"="0"}, name="groups_route", options={"expose"=true})
     * 
     * Main view for admin user
     */
    public function groupsAdminAction($userId, $error = null)
    {
        $groups_rep = $this->getDoctrine()->getRepository('YustPlayItBundle:GroupTable');
        if($userId == 0 && $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $groups = $groups_rep->findAll();
        } else {
            if ($userId != $this->getUser()->getId() && false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) { 
                $userId = $this->getUser()->getId();
            }
            $users_rep = $this->getDoctrine()->getRepository('YustPlayItBundle:User');
            $user = $users_rep->find($userId);
            $groups = $groups_rep->findBy(array('user' => $user));
        }
        return $this->render('YustPlayItBundle:Admin:groups.html.twig', array(
            'groups'  => $groups,
            'error' => $error,
        ));
    }
    
    /**
     * @Route("/admin/devices/{groupId}", defaults={"groupId"="0"},  name="devices_route", options={"expose"=true})
     * 
     * Main view for admin user
     */
    public function devicesAdminAction($groupId)
    {
        $devices_rep = $this->getDoctrine()->getRepository('YustPlayItBundle:Device');
        $groups_rep = $this->getDoctrine()->getRepository('YustPlayItBundle:GroupTable');
        if ($groupId == 0){
            if(false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $groups = $groups_rep->findBy(array('user' => $this->getUser()));
                // Apparently, this is correct
                $devices = $devices_rep->findBy(array('group' => $groups));
            } else {
                $devices = $devices_rep->findAll();
            }
        } else {
            $group = $groups_rep->find($groupId);
            if ($group->getUser()->getId() != $this->getUser()->getId()){ 
                $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
            }
            $devices = $devices_rep->findBy(array('group' => $group));
        }
        return $this->render('YustPlayItBundle:Admin:devices.html.twig', array(
            'devices'  => $devices,            
        ));
    }
           
    /**
     * @Route("admin/newUser",  name="newUser_route", options={"expose"=true})
     * 
     * Main view for admin user
     */
    public function newUserAdminAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(RegistrationType::class, new Registration());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $registration = $form->getData();
            $newUser = $registration->getUser();
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($newUser, $newUser->getPlainPassword());
            $newUser->setPassword($encoded);        
            $em->persist($newUser);
            $em->flush();

            return $this->redirectToRoute('users_route');
        }

        return $this->render(
            'YustPlayItBundle:Admin:newUserForm.html.twig',
            array('form' => $form->createView())
        );     
    }
    
    /**
     * @Route("/admin/newGroup",  name="newGroup_route", options={"expose"=true})
     * 
     * Main view for admin user
     */
    public function newGroupAdminAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $addGroup = new AddGroup();
        $group = new GroupTable();
        $group->setUser($this->getUser());
        $addGroup->setGroup($group);
        $form = $this->createForm(new AddGroupType(), $addGroup);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $group = $form->getData()->getGroup();
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('groups_route');
        }

        return $this->render(
            'YustPlayItBundle:Admin:newGroupForm.html.twig',
            array('form' => $form->createView())
        );     
    }
    
    /**
     * @Route("/admin/deleteGroup/{groupId}",  name="deleteGroup_route", options={"expose"=true})
     * 
     * Main view for admin user
     */
    public function deleteGroupAdminAction($groupId)
    {
        $em = $this->getDoctrine()->getManager();
        $groupsRep = $this->getDoctrine()->getRepository('YustPlayItBundle:GroupTable');
        $group = $groupsRep->find($groupId);
        if($group->getUser() != $this->getUser()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        }
        $devices = $group->getDevices();
        if (count($devices) == 0){
            $em->remove($group);
            $em->flush();
            return $this->redirectToRoute('groups_route');
        } else {
            $userId = $group->getUser()->getId();
            return $this->groupsAdminAction($userId, "El grupo tiene dispositivos registrados");
        }
    }
    
    /**
     * @Route("/admin/updateGroup/{groupId}",  name="updateGroup_route", options={"expose"=true})
     * 
     * Main view for admin user
     */
    public function updateGroupAdminAction(Request $request, $groupId)
    {
        $em = $this->getDoctrine()->getManager();
        $groupsRep = $this->getDoctrine()->getRepository('YustPlayItBundle:GroupTable');
        $group = $groupsRep->find($groupId);
        if($group->getUser() != $this->getUser()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        }
        $addGroup = new AddGroup();
        $addGroup->setGroup($group);
        $form = $this->createForm(new AddGroupType(), $addGroup);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $group = $form->getData()->getGroup();
    
            $em->persist($group);
            $em->flush();

            return $this->redirectToRoute('groups_route');
        }

        return $this->render(
            'YustPlayItBundle:Admin:newGroupForm.html.twig',
            array('form' => $form->createView())
        );        
    }
    
    /**
     * @Route("/admin/newDevice",  name="newDevice_route", options={"expose"=true})
     * 
     * Main view for admin user
     */
    public function newDeviceAdminAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(AddDeviceType::class, new AddDevice());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $device = $form->getData()->getDevice();
            $device->setSshKey('');
            $em->persist($device);
            $em->flush();

            return $this->redirectToRoute('devices_route');
        }

        return $this->render(
            'YustPlayItBundle:Admin:newDeviceForm.html.twig',
            array('form' => $form->createView())
        );     
    }
    
    /**
     * @Route("/admin/deleteDevice/{deviceId}",  name="deleteDevice_route", options={"expose"=true})
     * 
     * Main view for admin user
     */
    public function deleteDeviceAdminAction($deviceId)
    {
        $em = $this->getDoctrine()->getManager();
        $devices_rep = $this->getDoctrine()->getRepository('YustPlayItBundle:Device');
        $device = $devices_rep->find($deviceId);
        $em->remove($device);
        $em->flush();
        return $this->redirectToRoute('devices_route');
    }
    
    /**
     * @Route("/admin/updateDevice/{deviceId}",  name="updateDevice_route", options={"expose"=true})
     * 
     * Edit device form
     */
    public function updateDeviceAdminAction($deviceId, Request $request)
    {   
        $em = $this->getDoctrine()->getManager();
        $devices_rep = $this->getDoctrine()->getRepository('YustPlayItBundle:Device');
        $device = $devices_rep->find($deviceId);
        if($device->getGroup()->getUser() != $this->getUser()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        }
        $addDev = new AddDevice();
        $addDev->setDevice($device);
        $form = $this->createForm(new AddDeviceType(), $addDev);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $device = $form->getData()->getDevice();

            $em->persist($device);
            $em->flush();

            return $this->redirectToRoute('devices_route');
        }

        return $this->render(
            'YustPlayItBundle:Admin:newDeviceForm.html.twig',
            array('form' => $form->createView())
        );        
    }
    
    /**
     * @Route("/createNewUser",  name="createNewUser_route", options={"expose"=true})
     * 
     * Main view for admin user
     *
    public function registerAction()
    {
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration, array(
            'action' => $this->generateUrl('newUser_route'),
        ));

        return $this->render(
            'YustPlayItBundle:Admin:newUserForm.html.twig',
            array('form' => $form->createView())
        );
    }
    */
}

