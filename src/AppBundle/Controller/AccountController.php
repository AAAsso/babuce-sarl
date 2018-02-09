<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\FormError;

/**
 * Account controller.
 *
 * @Route("account")
 */
class AccountController extends Controller
{
    /**
     * For the page listing all account entities,
     * Please see in the AdministrationController
     */

    /**
     * Creates a new account entity.
     *
     * @Route("/new", name="account_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $session = $request->getSession();

        $account = new Account();
        $form = $this->createForm('AppBundle\Form\AccountType', $account);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form['password']->getData();
            // Hashing password with bcrypt for storage
            $password = password_hash ($plainPassword , PASSWORD_BCRYPT, ['cost'=>15]);
            $account->setPassword($password);

            // Registering a creation date for the account
            $account->setRegisterDate(new \DateTime("now"));

            $em = $this->getDoctrine()->getManager();


            $em->persist($account);
            $em->flush();

            $response = $this->forward('AppBundle:Security:firstlogin', array('account' => $account));

            return $response;
        }

        return $this->render('account/new.html.twig', array(
                'account' => $account,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a account entity.
     *
     * @Route("/{slug}", name="account_show")
     * @Method("GET")
     */
    public function showAction(Account $account)
    {
        $deleteForm = $this->createDeleteForm($account);

        return $this->render('account/show.html.twig', array(
                'shown_account' => $account,
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing account entity.
     *
     * @Route("/edit/{slug}", name="account_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Account $account)
    {
        $session = $request->getSession();

        if (is_null($session->get('account')) || !($session->get('account')->getId() === $account->getId() || $session->get('account')->isAdmin()))
        {
            $session->getFlashBag()->add('danger', 'Access denied.');
            return $this->redirectToRoute('succubesarl');
        }

        /**
         * Form to edit general information
         */
        $editAccountGeneralInformationForm = $this->createForm('AppBundle\Form\AccountGeneralDataType', $account);
        $editAccountGeneralInformationForm->handleRequest($request);

        /**
         * Form to edit password
         */
        $editAccountPasswordForm = $this->createForm('AppBundle\Form\AccountPasswordType', []);
        $editAccountPasswordForm->handleRequest($request);

        /**
         * Form to delete the account
         */
        $deleteForm = $this->createDeleteForm($account);

        // Handle the account form
        if ($editAccountGeneralInformationForm->isSubmitted())
        {
            $this->handleGeneralInformationForm($request, $editAccountGeneralInformationForm);
        }

        // Handle the password form
        if ($editAccountPasswordForm->isSubmitted())
        {
            $this->handleChangePasswordForm($request, $account, $editAccountPasswordForm);
        }

        return $this->render('account/edit.html.twig', [
                'account' => $account,
                'edit_form' => $editAccountGeneralInformationForm->createView(),
                'edit_password_form' => $editAccountPasswordForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a account entity.
     *
     * @Route("/{slug}", name="account_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Account $account)
    {
        $form = $this->createDeleteForm($account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($account);
            $em->flush();

            $session = $request->getSession();
            $session->clear();
            $session->getFlashBag()->add('success', 'Your account have been deleted.');

            return $this->redirectToRoute('succubesarl');
        }

        return $this->redirectToRoute('account_edit');
    }

    private function handleGeneralInformationForm(Request $request, $editAccountGeneralInformationForm)
    {
        $session = $request->getSession();
        if (!$editAccountGeneralInformationForm->isValid())
        {
            $session->getFlashBag()->add('danger', 'Your iinformation have not been updated, please retry.');
        }
        else
        {
            $this->getDoctrine()->getManager()->flush();

            $session->getFlashBag()->add('success', 'Your information have been updated.');
        }
    }

    private function handleChangePasswordForm(Request $request, Account $account, $editAccountPasswordForm)
    {
        $session = $request->getSession();
        if (!$editAccountPasswordForm->isValid())
        {
            $session->getFlashBag()->add('danger', 'Your password have not been updated, please retry.');
        }
        else
        {
            $oldUserPassword = $account->getPassword();
            $oldPasswordSubmitted = $editAccountPasswordForm['old-password']->getData();
            $newPasswordSubmitted = $editAccountPasswordForm['new-password']->getData();

            if (password_verify($oldPasswordSubmitted, $oldUserPassword))
            {
                $newPassword = password_hash($newPasswordSubmitted, PASSWORD_BCRYPT, ['cost' => 15]);
                $account->setPassword($newPassword);
                $this->getDoctrine()->getManager()->flush();

                $session->getFlashBag()->add('success', 'Your password have been updated.');
            }
            else
            {
                $editAccountPasswordForm->get('old-password')->addError(new FormError('Incorrect password.'));
                $session->getFlashBag()->add('danger', 'Your password have not been updated, please retry.');
            }
        }
    }

    /**
     * Creates a form to delete a account entity.
     *
     * @param Account $account The account entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Account $account)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('account_delete', array('slug' => $account->getSlug())))
                ->setMethod('DELETE')
                ->getForm()
        ;
    }
}
