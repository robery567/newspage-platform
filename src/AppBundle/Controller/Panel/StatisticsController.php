<?php
namespace AppBundle\Controller\Panel;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StatisticsController
 * @package AppBundle\Controller\Panel
 * @Route("/statistici")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class StatisticsController extends Controller
{
    /**
     * @Route("/articole", name="panel_statistics_articles")
     */
    public function articleAction(): Response
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');

        $articles_by_year = [];
        foreach ($repository->findAllCountedArticlesByYear() as $result) {
            $articles_by_year[] = $result;
        }

        $tmp = $articles_by_year;
        for ($i = 0; $i < count($tmp); $i++) {
            if (preg_match('/(' . date('Y') . ')/i', $tmp[$i]['label'])) {
                unset($tmp[$i]);
            }
        }

        usort($tmp, function ($a, $b) {
            return $a['value'] <=> $b['value'];
        });

        $minimum_articles_count = $tmp[0];
        $maximum_articles_count = $tmp[(count($tmp) - 1)];
        $current_articles_count = $repository->findByCurrentCountedArticles();

        return $this->render('Panel/statistics_article.html.twig', [
            'morrisjs_articles' => json_encode($articles_by_year),
            'data_min_articles' => $minimum_articles_count,
            'data_max_articles' => $maximum_articles_count,
            'data_cur_articles' => $current_articles_count,
            'articles_by_years' => $articles_by_year,
        ]);
    }
}