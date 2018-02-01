<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\ContentWarning;

/**
 * Administration controller.
 *
 * @Route("admin")
 */
class AdministrationController extends Controller {

    /**
     * Lists all account entities.
     *
     * @Route("/accounts", name="account_list")
     * @Method("GET")
     */
    public function listAccountAction() {
        $session = new Session();

        if ($session->get('account') != Null && $session->get('account')->isAdmin() === True) {
            $em = $this->getDoctrine()->getManager();

            $accounts = $em->getRepository('AppBundle:Account')->findAll();

            return $this->render('account/list.html.twig', array(
                        'accounts' => $accounts,
            ));
        } else {
            $session->getFlashBag()->add('danger', 'Access denied');
            return $this->redirectToRoute('succubesarl');
        }
    }

    /**
     * Lists all contentWarning entities.
     *
     * @Route("/contentwarnings", name="contentwarning_list")
     * @Method("GET")
     */
    public function listContentWarningAction() {
        $session = new Session();

        if ($session->get('account') != Null && $session->get('account')->isAdmin() === True) {
            $em = $this->getDoctrine()->getManager();

            $contentWarnings = $em->getRepository('AppBundle:ContentWarning')->findAll();

            $deleteForms = [];

            foreach ($contentWarnings as $contentWarning)
            {
                $deleteForm = $this->createContentWarningDeleteForm($contentWarning);
                $deleteForms[$contentWarning->getId()] = $deleteForm->createView();
            }

            return $this->render('contentwarning/list.html.twig', [
                'contentWarnings' => $contentWarnings,
                'delete_forms' => $deleteForms,
            ]);
        } else {
            $session->getFlashBag()->add('danger', 'Access denied');
            return $this->redirectToRoute('succubesarl');
        }
    }

    /**
     * Creates a new contentWarning entity.
     *
     * @Route("/contentwarnings/new", name="contentwarning_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $session = new Session();

        if ($session->get('account') != Null && $session->get('account')->isAdmin() === True) {
            $contentWarning = new \AppBundle\Entity\ContentWarning();
            $form = $this->createForm('AppBundle\Form\ContentWarningType', $contentWarning);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($contentWarning);
                $em->flush();

                return $this->redirectToRoute('contentwarning_show', array('label' => $contentWarning->getLabel()));
            }

            return $this->render('contentwarning/new.html.twig', array(
                        'contentWarning' => $contentWarning,
                        'form' => $form->createView(),
            ));
        } else {
            $session->getFlashBag()->add('danger', 'Access denied');
            return $this->redirectToRoute('succubesarl');
        }
    }

    /**
     * Displays a form to edit an existing contentWarning entity.
     *
     * @Route("/contentwarnings/{label}/edit", name="contentwarning_edit")
     * @Method({"GET", "POST"})
     */
    public function editContentWarningAction(Request $request, ContentWarning $contentWarning) {
        $session = new Session();

        if ($session->get('account') != Null && $session->get('account')->isAdmin() === True) {
            $deleteForm = $this->createContentWarningDeleteForm($contentWarning);
            $editForm = $this->createForm('AppBundle\Form\ContentWarningType', $contentWarning);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('contentwarning_edit', array('label' => $contentWarning->getLabel()));
            }

            return $this->render('contentwarning/edit.html.twig', array(
                        'contentWarning' => $contentWarning,
                        'edit_form' => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
            ));
        } else {
            $session->getFlashBag()->add('danger', 'Access denied');
            return $this->redirectToRoute('succubesarl');
        }
    }

    /**
     * Deletes a contentWarning entity.
     *
     * @Route("/{id}", name="contentwarning_delete")
     * @Method("DELETE")
     */
    public function deleteContentWarningAction(Request $request, ContentWarning $contentWarning) {
        $session = new Session();

        if ($session->get('account') != Null && $session->get('account')->isAdmin() === True) {
            $form = $this->createContentWarningDeleteForm($contentWarning);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($contentWarning);
                $em->flush();
            }

            return $this->redirectToRoute('contentwarning_list');
        } else {
            $session->getFlashBag()->add('danger', 'Access denied');
            return $this->redirectToRoute('succubesarl');
        }
    }

    /**
     * Creates a form to delete a contentWarning entity.
     *
     * @param ContentWarning $contentWarning The contentWarning entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createContentWarningDeleteForm(ContentWarning $contentWarning) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('contentwarning_delete', array('id' => $contentWarning->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
