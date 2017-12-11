<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Zend\Json\Json;

/**
 * Class ApiController
 * @package AppBundle\Controller
 *
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/exchange-rate", name="api_exchange_rate")
     */
    public function exchangeRateAction(): JsonResponse
    {
        return new JsonResponse(file_get_contents(dirname($this->getParameter('kernel.cache_dir'), 2) . '/nbr_exchange_rate.json'));
    }

    /**
     * @Route("/active-visitors", name="api_active_visitors")
     */
    public function activeVisitorsAction(): JsonResponse
    {
        $finder = new Finder();
        $directory = $this->getParameter('kernel.project_dir') . '/var/sessions/' . $this->getParameter('kernel.environment');
        $finder->in($directory)
               ->files()
               ->name('sess_*')
               ->date('since 5 minutes ago')
        ;

        return new JsonResponse(Json::encode(['active' => count($finder)]));
    }
}