<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\ContentWarning;
use AppBundle\Entity\Strip;

/**
 * Administration controller.
 *
 * @Route("admin")
 */
class AdministrationController extends Controller {

// Methods for Account administration
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

// Methods for Content Warnings Administration
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
     * @Route("/contentwarnings/{id}", name="contentwarning_delete")
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
    
// Methods for Stips administration    
    /**
     * Lists all strip entities.
     *
     * @Route("/strips", name="strip_list")
     * @Method("GET")
     */
    public function listStripAction() {
        $em = $this->getDoctrine()->getManager();

        $strips = $em->getRepository('AppBundle:Strip')->findAll();

        return $this->render('strip/list.html.twig', array(
                    'strips' => $strips,
        ));
    }
    
    /**
     * Creates a new strip entity.
     *
     * @Route("/strips/new", name="strip_new")
     * @Method({"GET", "POST"})
     */
    public function newStripAction(Request $request) {
        $strip = new Strip();
        $form = $this->createForm('AppBundle\Form\StripType', $strip);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * Process to gather images uploaded for the strip,
             * save them with uniques names, and save those strings
             * in strip attribute stripElements
             */
            $images = $strip->getStripElements();
            $filenames = [];
            foreach ($images as $image) {
                $name = md5(uniqid()) . '.' . $image->guessExtension();
                array_push($filenames, $name);
                // Is moving image in loop going to break the loop ?
                $image->move(
                        $this->getParameter('strips_directory'), 
                        $name
                );
            }
            $strip->setStripElements($filenames);

            $em = $this->getDoctrine()->getManager();
            $em->persist($strip);
            $em->flush();

            return $this->redirectToRoute('strip_show', array('id' => $strip->getId()));
        }

        return $this->render('strip/new.html.twig', array(
                    'strip' => $strip,
                    'form' => $form->createView(),
        ));
    }
    
    /**
     * Finds and displays a strip entity.
     *
     * @Route("strips/{id}", name="strip_show")
     * @Method("GET")
     */
    public function showStripAction(Strip $strip) {
        $deleteForm = $this->createDeleteForm($strip);

        return $this->render('strip/show.html.twig', array(
                    'strip' => $strip,
                    'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Displays a form to edit an existing strip entity.
     *
     * @Route("/strips/{id}/edit", name="strip_edit")
     * @Method({"GET", "POST"})
     */
    public function editStripAction(Request $request, Strip $strip) {
        $deleteForm = $this->createDeleteForm($strip);
        $editForm = $this->createForm('AppBundle\Form\StripType', $strip);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('strip_edit', array('id' => $strip->getId()));
        }

        return $this->render('strip/edit.html.twig', array(
                    'strip' => $strip,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a strip entity.
     *
     * @Route("/strips/{id}", name="strip_delete")
     * @Method("DELETE")
     */
    public function deleteStripAction(Request $request, Strip $strip) {
        $form = $this->createDeleteForm($strip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($strip);
            $em->flush();
        }

        return $this->redirectToRoute('strip_index');
    }

    /**
     * Creates a form to delete a strip entity.
     *
     * @param Strip $strip The strip entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Strip $strip) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('strip_delete', array('id' => $strip->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
