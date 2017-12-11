<?php
namespace AppBundle\Controller\Panel;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminController
 * @package AppBundle\Controller\Panel
 * @Route("/administrator")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="panel_admin_index")
     */
    public function indexAction(): Response
    {
        return $this->render('Panel/panel_dashboard.html.twig', []);
    }
}