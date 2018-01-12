<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Administration controller.
 *
 * @Route("admin")
 */
class AdministrationController extends Controller
{
    /**
     * Lists all account entities.
     *
     * @Route("/accounts", name="account_list")
     * @Method("GET")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $accounts = $em->getRepository('AppBundle:Account')->findAll();

        return $this->render('account/index.html.twig', array(
            'accounts' => $accounts,
        ));
    }
}
