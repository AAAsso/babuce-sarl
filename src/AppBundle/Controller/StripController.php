<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Strip;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Strip controller.
 *
 * @Route("strip")
 */
class StripController extends Controller {

    /**
     * Finds and displays a strip entity to the public.
     *
     * @Route("/{id}", name="strip_display")
     * @Method("GET")
     */
    public function displayStripAction(Request $request, Strip $strip) {

        return $this->render('strip/display.html.twig', [
                    'strip' => $strip,
        ]);
    }

}
