<?php
namespace AppBundle\Controller\Panel;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * User controller.
 * @Route("/utilizatori")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     * @Route("/", name="panel_user_index")
     * @Route("/pagina/{page}", name="panel_user_index_paginated", requirements={"page": "[1-9]\d+"})
     * @Method("GET")
     */
    public function indexAction($page = 1): Response
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAllPaginated($page);

        return $this->render('Panel/user_index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Lists all team members.
     * @Route("/echipa", name="panel_user_team")
     * @Route("/echipa/pagina/{page}", name="panel_user_team_paginated", requirements={"page": "[1-9]\d+"})
     * @Method("GET")
     */
    public function teamAction($page = 1): Response
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findTeamPaginated($page);

        return $this->render('Panel/user_index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Creates a new user entity.
     * @Route("/adauga", name="panel_user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\ProfileFormType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('panel_user_show', array('id' => $user->getId()));
        }

        return $this->render('Panel/user_new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a user entity.
     * @Route("/info/{id}", name="panel_user_show")
     * @Method("GET")
     */
    public function showAction(User $user): Response
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('Panel/user_show.html.twig', array(
            'user'        => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     * @Route("/actualizeaza/{id}", name="panel_user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user): Response
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AppBundle\Form\ProfileFormType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('panel_user_show', array('id' => $user->getId()));
        }

        return $this->render('Panel/user_edit.html.twig', array(
            'user'        => $user,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a user entity.
     * @Route("/stergere/{id}", name="panel_user_delete")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Request $request, User $user): Response
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('panel_user_index');
    }

    /**
     * Creates a form to delete a user entity.
     * @param User $user The user entity
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(User $user): FormInterface
    {
        return $this->createFormBuilder()->setAction($this->generateUrl('panel_user_delete',
                array('id' => $user->getId())))->setMethod('DELETE')->getForm();
    }
}
