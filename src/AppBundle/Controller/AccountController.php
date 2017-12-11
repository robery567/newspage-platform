<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccountController
 * @package AppBundle\Controller
 * @Route("/panou/cont")
 * @Security("is_granted('ROLE_USER')")
 */
class AccountController extends Controller
{

    /**
     * @Route("/reclamele-mele", name="panel_account_ads")
     */
    public function listAdsAction(): Response
    {
        $ads = $this->getDoctrine()->getRepository('AppBundle:Ad')->findAllPaginatedFromUser($this->getUser());

        return $this->render('Platform/user_ads.html.twig', [
            'ads' => $ads,
        ]);
    }
}