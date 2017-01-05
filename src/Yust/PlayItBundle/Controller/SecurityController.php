<?php

namespace Yust\PlayItBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Yust\PlayItBundle\Entity\User;
use Yust\PlayItBundle\Entity\Role;
use Yust\PlayItBundle\Form\Model\ChangePassword;
use Yust\PlayItBundle\Form\Type\ChangePasswordType;
use Yust\PlayItBundle\Form\Model\ResetPassword;
use Yust\PlayItBundle\Form\Type\ResetPasswordType;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    
    /**
     * @Route("/initialize", name="initialize_route")
     * 
     * Basically, a workaround to create users/roles when the service
     * is deployed in a new server. It should be after commented or hided
     */
    public function initializeAction()
    {    
        // ROLES CREATION
        $role_user = new Role();
        $role_user->setName("user");
        $role_user->setRole("ROLE_USER");
        $role_admin = new Role();
        $role_admin->setName("admin");
        $role_admin->setRole("ROLE_ADMIN");
        $em = $this->getDoctrine()->getManager();
        $em->persist($role_user);  
        $em->persist($role_admin);
        $em->flush();
             
        // BASIC USER CREATION: 1 admin. CHANGE PASSWORD AFTERWARDS
        $roles = $this->getDoctrine()->getRepository('YustPlayItBundle:Role');
        $adminRole = $roles->findOneBy(array('name'=>'admin'));
        
        $admin = new User();
        $plainPassword = 'admin';
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($admin, $plainPassword);
        $admin->setUsername("admin");
        $admin->setEmail("c.yuste@yustplayit.com");
        $admin->addRole($adminRole);
        $admin->setPassword($encoded);
        $em->persist($admin);

        $em->flush();      
        $response = '{"code":"OK","message":"OK"}';
        return new Response($response);
    }
    
    /**
     * @Route("/register", name="register_route")
     */
    public function registerAction()
    {
        // TODO     
        $response = '{"code":"OK","message":"OK"}';
        return new Response($response);
    }
    
    /**
     * @Route("/resetPasswd", name="reset_passwd")
     */
    public function resetPasswdAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new ResetPasswordType(), new ResetPassword());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $username = $form->getData()->getUsername();
            $email = $form->getData()->getEmail();
            $user_rep = $this->getDoctrine()->getRepository('YustPlayItBundle:User');
            $user = $user_rep->findOneBy(array('username' => $username, 'email' => $email));
            if($user) {
                $length = 10;
                $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
                $this->setNewPassword($user, $randomString);
                //Send email
                $mailer = $this->get('mailer');
                $message = $mailer->createMessage()
                    ->setSubject('Yustplayit: Cambio de contraseña')
                    ->setFrom('no-reply@yustplayit.com')
                    ->setTo('c.yuste@yustplayit.com')
                    //->setTo($email)
                    ->setBody(
                        $this->renderView(
                            'YustPlayItBundle:Emails:resetPassword.txt.twig',
                            array('password' => $randomString)
                        ),
                        'text/plain'
                    );
                $mailer->send($message);
                return $this->render(
                    'YustPlayItBundle:Security:resetPasswordSuccess.html.twig'
                );  
            } else {
                $error = "Usuario/email incorrectos";
                $form->addError($error);
                return $this->render(
                    'YustPlayItBundle:Security:resetPassword.html.twig',
                    array('form' => $form->createView())
                );          
            }      
        } 
        return $this->render(
            'YustPlayItBundle:Security:resetPassword.html.twig',
            array('form' => $form->createView())
        );
    }
    
    /**
     * @Route("/profile", name="profile_route"), options={"expose"=true})
     * 
     * Create new user action
     */
    public function profileAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('username','text')
            ->add('email','email')
            ->add('plainPassword','password')  
            ->add('submit','submit')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->setNewPassword($user, $user->getNewPassword());
            return $this->render('YustPlayItBundle:Security:profile.html.twig',array(
                'form' => $form->createView(),
            ));           
        } else {
            return $this->render('YustPlayItBundle:Security:profile.html.twig',array(
                'form' => $form->createView(),
            ));
        }       
    }
    
    /**
     * @Route("/changePassword", name="changePassword_route"), options={"expose"=true})
     * 
     */
    public function changePasswordAction(Request $request)
    {
        $changePasswordModel = new ChangePassword();
        $form = $this->createForm(new ChangePasswordType(),$changePasswordModel);  
        $form->handleRequest($request);
        if ($form->isValid()) {
            $chngPwd = $form->getData();
            $user = $this->getUser();
            $this->setNewPassword($user,$chngPwd->getNewPassword());    
            return $this->redirectToRoute('profile_route');          
        } else {
            return $this->render('YustPlayItBundle:Security:password.html.twig',array(
                'form' => $form->createView(),
            ));
        }       
    }
    
    public function setNewPassword (User $user, $password) {
        $em = $this->getDoctrine()->getManager();          
        $encoder = $this->get('security.password_encoder'); 
        $encoded = $encoder->encodePassword($user, $password);
        $user->setPassword($encoded);
        $em->flush();    
    }
    
    /**
     * @Route("/login", name="login_route")
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'YustPlayItBundle:Security:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }
}