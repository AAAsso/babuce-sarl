<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class SecurityController extends Controller
{
    
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $username = $request->get('username');
        
        $account = $em->getRepository('AppBundle:Account')->accountExists($username);
        
        $session = new Session();
        
        if (!is_null($account))
        {
            $password = $request->get('password');
            
            if ($password === $account->getPassword())
            {
                // Connection success message
                $session->getFlashBag()->add('success', 'Welcome '.$account->getUsername());
                
                $session->set('isConnected', true);
                $session->set('account', $account);
            }
            else
            {
                // Connection wrong password message - CHANGE MESSAGE AFTER TESTS !
                $session->getFlashBag()->add('danger', 'Incorrect password ');
            }
        }
        else
        {
            // Connection wrong username message - - CHANGE MESSAGE AFTER TESTS !
            $session->getFlashBag()->add('danger', 'Incorrect username ');
        }
        
        return $this->redirectToRoute('succubesarl');
    }
    
    /**
     * @Route("/firstlogin", name="firstlogin")
     */
    public function firstloginAction($account)
    {
        $em = $this->getDoctrine()->getManager();
        
        $session = new Session();
        
        if (!is_null($account))
        {
            $session->getFlashBag()->add('success', 'Welcome '.$account->getUsername().', your account is created and you are now logged in.' );
            $session->set('isConnected', true);
            $session->set('account', $account);
        }

        return $this->redirectToRoute('succubesarl');
        
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        $session = new Session();
        $session->clear();
        
        return $this->redirectToRoute('succubesarl');
    }

}
