<?php
namespace AppBundle\Controller\Panel;

use AppBundle\Entity\Ad;
use AppBundle\Repository\AdRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdController
 * @package AppBundle\Controller\Panel
 * @Route("/reclame")
 */
class AdController extends Controller
{
    private function getAdRepository(): AdRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Ad');
    }

    private function createDeleteForm(Ad $ad): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('panel_ad_delete', array('id' => $ad->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Lists all article entities.
     * @Route("/", defaults={"page": "1"}, name="panel_ad_index")
     * @Route("/pagina/{page}", requirements={"page": "[1-9]\d*"}, name="panel_ad_index_paginated")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function indexAction($page): Response
    {
        $ads = $this->getAdRepository()->findAllPaginated($page);

        return $this->render('Panel/ad_index.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * @Route("/adauga", name="panel_ad_new")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request): Response
    {
        $ad = new Ad();
        $form = $this->createForm('AppBundle\Form\AdType', $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ad);
            $em->flush();

            return $this->redirectToRoute('panel_ad_view', ['uuid' => $ad->getUuid()]);
        }

        return $this->render('Panel/ad_new.html.twig', [
            'ad'   => $ad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/informatii/{uuid}", name="panel_ad_view")
     * @Security("is_granted('ROLE_USER')")
     */
    public function viewAction(Ad $ad): Response
    {
        $deleteForm = $this->createDeleteForm($ad);

        return $this->render('Panel/ad_show.html.twig', [
            'ad'          => $ad,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/actualizare/{uuid}", name="panel_ad_edit")
     * @Security("is_granted('ROLE_USER')")
     */
    public function editAction(Request $request, Ad $ad): Response
    {
        $deleteForm = $this->createDeleteForm($ad);
        $editForm = $this->createForm('AppBundle\Form\AdType', $ad);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('panel_ad_index');
        }

        return $this->render(':Panel:ad_edit.html.twig', [
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'ad'          => $ad,
        ]);
    }

    /**
     * Deletes a article entity.
     *
     * @Route("/sterge/{id}", name="panel_ad_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Ad $ad): Response
    {
        $form = $this->createDeleteForm($ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ad);
            $em->flush();
        }

        return $this->redirectToRoute('panel_ad_index');
    }
}