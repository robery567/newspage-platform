<?php
namespace AppBundle\Controller;

use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdController extends Controller
{
    /**
     * @param string $uuid
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/reclama/{uuid}", name="ad_redirect")
     */
    public function adRedirectAction(string $uuid = Uuid::NIL): RedirectResponse
    {
        $nilUuid = Uuid::NIL;

        if ($uuid == $nilUuid) {
            return $this->redirectToRoute('main_index');
        }

        $em = $this->getDoctrine()->getManager();

        $ad = $em->getRepository('AppBundle:Ad')->findOneByUuid($uuid);

        if (empty($ad)) {
            return $this->redirectToRoute('main_index');
        }

        $ad->setClicks($ad->getClicks() + 1);
        $em->persist($ad);
        $em->flush();

        return $this->redirect($ad->getLink());
    }
}