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
class AdministrationController extends Controller
{
    /*
     * ----------------------------------
     * Methods for Account administration
     * ----------------------------------
     */

    /**
     * Lists all account entities.
     *
     * @Route("/accounts", name="account_list")
     * @Method("GET")
     */
    public function listAccountAction(Request $request)
    {
        $session = $request->getSession();

        if ($session->get('account') != null && $session->get('account')->isAdmin() === true)
        {
            $em = $this->getDoctrine()->getManager();

            $accounts = $em->getRepository('AppBundle:Account')->findAll();

            return $this->render('account/list.html.twig', [
                    'accounts' => $accounts,
            ]);
        }
        else
        {
            $session->getFlashBag()->add('danger', 'Access denied');
            return $this->redirectToRoute('succubesarl');
        }
    }

    /*
     * -------------------------------------------
     * Methods for Content Warnings Administration
     * -------------------------------------------
     */

    /**
     * Lists all contentWarning entities.
     *
     * @Route("/contentwarnings", name="contentwarning_list")
     * @Method("GET")
     */
    public function listContentWarningAction(Request $request)
    {
        $session = $request->getSession();

        if ($session->get('account') != null && $session->get('account')->isAdmin() === true)
        {
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
        }
        else
        {
            $session->getFlashBag()->add('danger', 'Access denied');
            return $this->redirectToRoute('succubesarl');
        }
    }

    /**
     * Finds and displays a contentWarning entity.
     *
     * @Route("/contentwarning/{slug}", name="contentwarning_administration_show")
     * @Method("GET")
     */
    public function showContentWarningAction(Request $request, ContentWarning $contentWarning)
    {
        $session = $request->getSession();

        if (is_null($session->get('account')) || $session->get('account')->isAdmin() === false)
        {
            $session->getFlashBag()->add('danger', 'Access denied');
            return $this->redirectToRoute('succubesarl');
        }

        return $this->render('contentwarning/administration-show.html.twig', [
                'contentWarning' => $contentWarning,
        ]);
    }

    /**
     * Creates a new contentWarning entity.
     *
     * @Route("/contentwarnings/new", name="contentwarning_new")
     * @Method({"GET", "POST"})
     */
    public function newContentWarningAction(Request $request)
    {
        $session = $request->getSession();

        if ($session->get('account') != null && $session->get('account')->isAdmin() === true)
        {
            $contentWarning = new \AppBundle\Entity\ContentWarning();
            $form = $this->createForm('AppBundle\Form\ContentWarningType', $contentWarning);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $contentWarning->setCreationDate(new \DateTime("now"));

                $em = $this->getDoctrine()->getManager();
                $em->persist($contentWarning);
                $em->flush();

                return $this->redirectToRoute('contentwarning_show', ['slug' => $contentWarning->getSlug()]);
            }

            return $this->render('contentwarning/new.html.twig', [
                    'contentWarning' => $contentWarning,
                    'form' => $form->createView(),
            ]);
        }
        else
        {
            $session->getFlashBag()->add('danger', 'Access denied');
            return $this->redirectToRoute('succubesarl');
        }
    }

    /**
     * Displays a form to edit an existing contentWarning entity.
     *
     * @Route("/contentwarnings/{slug}/edit", name="contentwarning_edit")
     * @Method({"GET", "POST"})
     */
    public function editContentWarningAction(Request $request, ContentWarning $contentWarning)
    {
        $session = $request->getSession();

        if ($session->get('account') != null && $session->get('account')->isAdmin() === true)
        {
            $deleteForm = $this->createContentWarningDeleteForm($contentWarning);
            $editForm = $this->createForm('AppBundle\Form\ContentWarningType', $contentWarning);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid())
            {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('contentwarning_edit', ['slug' => $contentWarning->getSlug()]);
            }

            return $this->render('contentwarning/edit.html.twig', [
                    'contentWarning' => $contentWarning,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
            ]);
        }
        else
        {
            $session->getFlashBag()->add('danger', 'Access denied');
            return $this->redirectToRoute('succubesarl');
        }
    }

    /**
     * Deletes a contentWarning entity.
     *
     * @Route("/contentwarnings/{slug}", name="contentwarning_delete")
     * @Method("DELETE")
     */
    public function deleteContentWarningAction(Request $request, ContentWarning $contentWarning)
    {
        $session = $request->getSession();

        if ($session->get('account') != null && $session->get('account')->isAdmin() === true)
        {
            $form = $this->createContentWarningDeleteForm($contentWarning);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->remove($contentWarning);
                $em->flush();

                $session->getFlashBag()->add('success', 'Content warning successfully removed.');
            }

            return $this->redirectToRoute('contentwarning_list');
        }
        else
        {
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
    private function createContentWarningDeleteForm(ContentWarning $contentWarning)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('contentwarning_delete', ['slug' => $contentWarning->getSlug()]))
                ->setMethod('DELETE')
                ->getForm()
        ;
    }

    /*
     * --------------------------------
     * Methods for Stips administration
     * --------------------------------
     */

    /**
     * Lists all strip entities.
     *
     * @Route("/strips", name="strip_list")
     * @Method("GET")
     */
    public function listStripAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $strips = $em->getRepository('AppBundle:Strip')->findAll();

        return $this->render('strip/list.html.twig', [
                'strips' => $strips,
        ]);
    }

    /**
     * Creates a new strip entity.
     *
     * @Route("/strips/new", name="strip_new")
     * @Method({"GET", "POST"})
     */
    public function newStripAction(Request $request)
    {
        $session = $request->getSession();
        
        $strip = new Strip();
        $form = $this->createForm('AppBundle\Form\StripType', $strip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /**
             * Process to gather images uploaded for the strip,
             * save them with uniques names, and save those strings
             * in strip attribute stripElements
             */
            $images = $strip->getStripElements();
            $filenames = [];
            foreach ($images as $image)
            {
                $name = md5(uniqid()) . '.' . $image->guessExtension();
                array_push($filenames, $name);
                // Is moving image in loop going to break the loop ?
                $image->move(
                    $this->getParameter('strips_directory'), $name
                );
            }
            $strip->setStripElements($filenames);
            
            $idConnected = $session->get('account')->getId();
            $em = $this->getDoctrine()->getManager();
            $account = $em->getRepository('AppBundle:Account')->find($idConnected);
            
            $account->getStrips()->add($strip);
            $strip->setAuthor($account);
            
            $em->persist($strip);
            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute('strip_show', ['id' => $strip->getId()]);
        }

        return $this->render('strip/new.html.twig', [
                'strip' => $strip,
                'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a strip entity.
     *
     * @Route("strips/{id}", name="strip_show")
     * @Method("GET")
     */
    public function showStripAction(Request $request, Strip $strip)
    {
        $deleteForm = $this->createDeleteForm($strip);

        return $this->render('strip/show.html.twig', [
                'strip' => $strip,
                'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing strip entity.
     *
     * @Route("/strips/{id}/edit", name="strip_edit")
     * @Method({"GET", "POST"})
     */
    public function editStripAction(Request $request, Strip $strip)
    {
        $deleteForm = $this->createDeleteForm($strip);
        $editForm = $this->createForm('AppBundle\Form\StripType', $strip);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('strip_edit', ['id' => $strip->getId()]);
        }

        return $this->render('strip/edit.html.twig', [
                'strip' => $strip,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a strip entity.
     *
     * @Route("/strips/{id}", name="strip_delete")
     * @Method("DELETE")
     */
    public function deleteStripAction(Request $request, Strip $strip)
    {
        $form = $this->createDeleteForm($strip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
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
    private function createDeleteForm(Strip $strip)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('strip_delete', ['id' => $strip->getId()]))
                ->setMethod('DELETE')
                ->getForm()
        ;
    }

}
