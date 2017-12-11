<?php
namespace AppBundle\Controller\Panel;

use AppBundle\Entity\Settings;
use AppBundle\Form\SettingsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SettingController
 * @package AppBundle\Controller\Panel
 * @Route("/setari")
 */
class SettingsController extends Controller
{
    /**
     * @Route("/", name="panel_setting_index")
     * @Route("/pagina/{page}", name="panel_setting_index_paginated", requirements={"page": "[1-9]\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function indexAction($page = 1): Response
    {
        $settings = $this->getDoctrine()->getRepository('AppBundle:Settings')->findAllPaginated($page);

        return $this->render('Panel/settings_index.html.twig', [
            'settings' => $settings,
        ]);
    }

    /**
     * @Route("/adauga", name="panel_setting_new")
     * @Security("is_granted('ROLE_SYSOP')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request): Response
    {
        $settings = new Settings();
        $form = $this->createForm('AppBundle\Form\SettingsType', $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appSettings = $this->get(\AppBundle\Util\Settings::class);
            $em = $this->getDoctrine()->getManager();

            $settingsName = $form->get('name')->getData();
            $settingsValue = $form->get('value')->getData();

            $appSettings->set($settingsName, $settingsValue);
            $settings->setValue($appSettings->getRaw($settingsName));

            $em->persist($settings);
            $em->flush();

            return $this->redirectToRoute('panel_setting_index');
        }

        return $this->render('Panel/settings_new.html.twig', [
            'setting' => $settings,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/info/{id}", name="panel_setting_view")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function viewAction(Settings $settings): Response
    {
        $deleteForm = $this->createDeleteForm($settings);

        return $this->render('Panel/settings_show.html.twig', [
            'settings'     => $settings,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/actualizare/{id}", name="panel_setting_edit")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Settings $settings): Response
    {
        $deleteForm = $this->createDeleteForm($settings);
        $editForm = $this->createForm(SettingsType::class, $settings);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $settingName = $editForm->get('name');

            if (!$this->isGranted('ROLE_SYSOP') && $settingName !== $settings->getName()) {
                $settingName->addError(new FormError('Nu aveți permisiunea de a actualiza numele setării, întrucât este vitală pentru buna funcționare a site-ului.'));

                return $this->redirectToRoute('panel_setting_edit', ['id' => $settings->getId()]);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($settings);
            $em->flush();

            return $this->redirectToRoute('panel_setting_index');
        }

        return $this->render('Panel/settings_edit.html.twig', [
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'settings'     => $settings,
        ]);
    }

    /**
     * @Route("/stergere/{id}", name="panel_setting_delete")
     * @Security("is_granted('ROLE_SYSOP')")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Request $request, Settings $settings): Response
    {
        $form = $this->createDeleteForm($settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($settings);
            $em->flush();
        }

        return $this->redirectToRoute('panel_setting_index');
    }

    private function createDeleteForm(Settings $settings)
    {
        return $this->createFormBuilder()->setAction($this->generateUrl('panel_setting_delete',
                ['id' => $settings->getId()]))->setMethod('DELETE')->getForm();
    }
}
