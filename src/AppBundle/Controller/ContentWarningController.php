<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ContentWarning;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contentwarning controller.
 *
 * @Route("contentwarning")
 */
class ContentWarningController extends Controller
{

    /**
     * Finds and displays a contentWarning entity.
     *
     * @Route("/{label}", name="contentwarning_show")
     * @Method("GET")
     */
    public function showAction(ContentWarning $contentWarning)
    {
        return $this->render('contentwarning/show.html.twig', [
                'contentWarning' => $contentWarning,
        ]);
    }

}
