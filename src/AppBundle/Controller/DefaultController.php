<?php
namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Article;
use AppBundle\Entity\Category;
use AppBundle\Util\Settings;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", defaults={"_format"="html"}, name="main_index")
     * @Route("/rss-feed.xml", defaults={"_format"="xml"}, name="main_index_rss")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function frontpageAction($_format): Response
    {
        $articleRepository = $this->getDoctrine()->getRepository('AppBundle:Article');

        $detector = $this->get('lib.mobile_detect');
        $twigVars = [];

        if ($detector->isMobile() || $detector->isTablet()) {
            $template = 'main_news';
            if ($_format === 'xml') {
                $twigVars['feeds'] = $articleRepository->findAllRssFeed();
            } else {
                $twigVars['news_cols'] = AppBundle::NEWS_TWO_COLS;
                $twigVars['news_title'] = 'Prima pagină';

                $headlines = $this->get(Settings::class)->get('bistr.news_headline');

                if (!empty($headlines)) {
                    foreach (explode(',', $headlines) as $item) {
                        $twigVars['categories']['headline'][] = $this->getDoctrine()
                            ->getRepository('AppBundle:Category')
                            ->findOneSubcategory($item);
                    }
                }

                $twigVars['categories']['all'] = $this->getDoctrine()
                    ->getRepository('AppBundle:Category')
                    ->findOneArticleInSubcategories();

                $twigVars['articles'] = [
                    'middle' => $articleRepository->findAllHot(),
                    'right'  => $articleRepository->findAll(),
                ];
            }
        } else {
            $template = 'frontpage_index';
            $twigVars['news_title'] = 'Prima pagină';
            $twigVars['articles']['announcement'] = $articleRepository->findOneBy([
                'type' => Article::TYPE_FEAT
            ], [
                'updatedAt' => 'DESC',
                'addedAt' => 'DESC'
            ]);

            $twigVars['articles']['ad'] = $articleRepository->findAThree();
            $twigVars['articles']['hot'] = $articleRepository->findAIndependent();
            $twigVars['articles']['latest'] = $articleRepository->findAllLatest();
            $twigVars['articles']['video'] = $articleRepository->findVideos();

            $headlines = $this->get(Settings::class)->get('bistr.news_headline');

            if (!empty($headlines)) {
                foreach (explode(',', $headlines) as $item) {
                    $twigVars['categories']['headline'][] = $this->getDoctrine()
                        ->getRepository('AppBundle:Category')
                        ->findOneSubcategory($item);
                }
            }

            $recommended = $articleRepository->findNewRecommended();
            $twigVars['articles']['recommended'] = [
                'left' => $recommended[0],
                'right' => $recommended[1],
            ];
            $twigVars['categories']['all'] = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findOneArticleInSubcategories();
        }
        return $this->render("Platform/{$template}.{$_format}.twig", $twigVars);
    }

    /**
     * @Route("/toate-stirile", name="main_news")
     * @Route("/toate-stirile/pagina/{page}", name="main_news_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function newsAction($page = 1): Response
    {
        $articleRepository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $twig = [];

        $twig['news_cols'] = AppBundle::NEWS_ONE_COLS;
        $twig['news_title'] = 'Toate știrile';

        $twig['articles'] = $articleRepository->findAllPaginated($page);

        return $this->render(":Platform:main_news.html.twig", $twig);
    }

    /**
     * @Route("/redactia", name="main_redactors")
     */
    public function redactorsAction(): Response
    {
        $redactors = $this->getDoctrine()->getRepository('AppBundle:User');

        return $this->render('Platform/main_redactors.html.twig', [
            'redactors' => $redactors->findAllRedactors(),
        ]);
    }

    /**
     * Just for fun...
     * @Route("/404", name="main_404")
     * @Method("GET")
     */
    public function errorAction()
    {
        throw $this->createNotFoundException();
    }

    /**
     * @Route("/video/{videoId}", name="main_video")
     */
    public function videoAction(string $videoId): Response
    {
        /** @var Article $article */
        $article = $this->getDoctrine()->getRepository('AppBundle:Article')->findVideoBy($videoId);
        return $this->render('Platform/main_video.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route(
     *     "/{category}",
     *     name="main_category",
     *     defaults={"_format"="html"}
     * )
     * @Route(
     *     "/{category}/rss-feed.xml",
     *     name="main_category_rss",
     *     defaults={"_format"="xml"},
     *     requirements={"category": "^(?!panou|debug|404|platform|redactia|media|rss-feed\.xml).+"}
     * )
     * @Method("GET")
     */
    public function categoryAction(Request $request, $category, $_format): Response
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Article');
        $twig = [];

        if ($_format == 'xml') {
            $twig['feeds'] = $repo->findAllRssFeedInCategory($category);
        } else {
            $parentCategories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAllParents();
            $parents = [];
            $page = $request->query->get('page', 1);

            foreach ($parentCategories as $parentCategory) {
                $parents[] = $parentCategory->getSlug();
            }

            if (in_array($category, $parents)) {
                $parentId = $this->getDoctrine()->getRepository('AppBundle:Category')->findCategoryId($category);
                if ($parentId == 0) {
                    throw $this->createNotFoundException('Categoria solicitată este inexistentă!');
                }

                $articles = $repo->findAllInCategoryWithParent($parentId, $page);
            } else {
                $articles = $repo->findAllInCategory($category);
            }

            if (empty($articles)) {
                throw $this->createNotFoundException('Categoria solicitată este inexistentă!');
            }

            if (preg_match('/-/', $category)) {
                $categoryName = str_replace('-', ' ', $category);
            } else {
                $categoryName = $category;
            }


            $twig['news_type'] = 'category';
            $twig['news_cols'] = AppBundle::NEWS_ONE_COLS;
            $twig['news_title'] = "Categorie: {$categoryName}";
            $twig['articles'] = $articles;
        }

        return $this->render(":Platform:main_news.{$_format}.twig", $twig);
    }

    /**
     * @Route(
     *     "/{category}/{slug}",
     *     name="main_article",
     *     requirements={
     *         "category": "^(?!panou|debug|404|platform|redactia|media|rss-feed\.xml).+",
     *         "article": "^(?!rss-feed\.xml).+"
     *     }
     * )
     * @Method("GET")
     */
    public function articleAction($category, $slug): Response
    {
        $article = $this->getDoctrine()->getRepository('AppBundle:Article')->findOneBySlugIn($category, $slug);

        if (empty($article)) {
            throw $this->createNotFoundException('Articolul solicitat de tine nu poate fi gasit!');
        }

        $em = $this->getDoctrine()->getManager();
        $article->setViews($article->getViews() + 1);
        $em->persist($article);
        $em->flush();

        return $this->render(':Platform:main_article.html.twig', array(
            'article' => $article,
        ));
    }
}
