<?php
namespace AppBundle\Twig\Extension;

use AppBundle\Entity\Article;
use AppBundle\Util\DbConfig;
use AppBundle\Util\Settings;
use AppBundle\Util\Text;
use Doctrine\ORM\EntityRepository;
use Sylva\Builder\HtmlBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ArticleExtension extends \Twig_Extension
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    private function getRepository($repository): EntityRepository
    {
        return $this->container->get('doctrine.orm.entity_manager')->getRepository($repository);
    }

    private function renderMediaObjects($array)
    {
        $d = [];
        foreach ($array as $article) {
            $d[] = $this->renderMediaObject($article);
        }

        return implode('', $d);
    }

    private function renderMediaObject(Article $article)
    {
        $url = $this->container->get('router')->generate('main_article', [
            'category' => $article->getCategory()->getSlug(),
            'slug' => $article->getSlug(),
        ]);

        $heading = $article->getTitle();
        $articleId = $article->getArticleId();

        $datetime_iso = $article->getAddedAt()->format(\DateTime::ISO8601);
        $datetime_normal = $article->getAddedAt()->format($this->container->get(Settings::class)->get('platform.default_date_format'));

        $html = <<<HTML
<article>
    <div class="media">
        <div class="media-body" data-article-id="{$articleId}">
            <h4 class="media-heading"><a href="{$url}">{$heading}</a></h4>
            <p><i class="fa fa-clock-o"></i> <time class="timeago" datetime="{$datetime_iso}" title="{$datetime_normal}">{$datetime_normal}</time></p>
            <p><a href="{$url}">Citește mai mult <i class="fa fa-arrow-right"></i> </a></p>
        </div>
    </div>
</article>
HTML;

        return $html;
    }

    /**
     * @param Article $article
     * @return string
     */
    private function renderSimpleArticle($article)
    {
        $html = new HtmlBuilder();

        if (empty($article) || is_null($article)) {
            $html->add('article')->add('div', ['class' => 'alert alert-info'])->addTag('p', 'Momentan, nu există nicio știre!')->add('/div')->add('/article');
            return $html->get();
        }

        $format = $this->container->get(Settings::class)->get('platform.default_date_format');

        $cdnAddress = \GuzzleHttp\json_decode($this->container->get(Settings::class)->get('cdn.url'));
        $imgPath = "http://{$cdnAddress}/thumbnail/{$article->getArticleId()}.png";

        dump($cdnAddress, $imgPath);

        $html
            ->add('article', [
                'class' => 'article',
                'data-article-id' => $article->getArticleId()
            ])
            ->addTag('img', null, [
                'width' => 150,
                'height' => 100,
                'src' => $imgPath,
                'onerror' => 'this.src=\'https://placehold.it/512x256\''
            ])
            ->addTag('a',  '<h2 class="article__heading article__heading--recommended">' . $article->getTitle() . '</h2>', [
                'href' => $this->container->get('router')->generate('main_article', [
                    'category' => $article->getCategory()->getSlug(),
                    'slug' => $article->getSlug(),
                ]),
            ])
            ->addTag('time', $article->getAddedAt()->format($format), [
                'datetime' => $article->getAddedAt()->format(\DateTime::ISO8601),
                'title' => $article->getAddedAt()->format($format),
            ])
            ->add('/article');

        return $html->get();
    }

    private function renderArticle($article, $boxType = 'post_type_2', $withBox = false, $newBoxClass = false)
    {
        $boxBodyClass = $newBoxClass ? 'body' : 'panel-body';

        $html = new HtmlBuilder();

        if ($withBox) {
            $html->add('div', ['class' => $boxBodyClass]);
        }

        if (empty($article) || is_null($article)) {
            $html->add('article')->add('div', ['class' => 'alert alert-info'])->addTag('p', 'Momentan, nu există nicio știre!')->add('/div')->add('/article');

            if ($withBox) {
                $html->add('/div');
            }
            return $html->get();
        }

        $format = $this->container->get(Settings::class)->get('platform.default_date_format');

        $html
            ->add('div', ['class' => 'main_content'])
            ->add('div', ['class' => 'block_posts'])
            ->add('div', ['class' => 'posts'])
            ->add('article', ['class' => $boxType])
            ->add('div', ['class' => 'content']);


        $html
            ->add('div', ['class' => 'title'])
            ->addTag('a', Text::truncate($article->getTitle(), 32), [
                'href' => $this->container->get('router')->generate('main_article', [
                    'category' => $article->getCategory()->getSlug(),
                    'slug' => $article->getSlug(),
                ]),
            ])
            ->add('/div')
            ->add('/div')
            ->add('div', ['class' => 'info'])
            ->addTag('div', $article->getAddedAt()->format($format), ['class' => 'date']);

        if (null !== ($article->getTags())) {
            $html->add('div', ['class' => 'tags']);

            foreach ($article->getTags() as $tag) {
                $html->addTag('a', $tag->getName());
            }

            $html->add('/div');
        }

        $html->add('/div')->add('/article');

        $html->add('/div')->add('/div')->add('/div');

        if ($withBox) {
            $html->add('/div');
        }

        return $html->get();
    }

    private function renderArticleList($articles, $boxType = 'post_type_2', $withBox = false, $newBoxType = false)
    {
        $multihtml = [];
        foreach ($articles as $article) {
            $multihtml[] = $this->renderArticle($article, $boxType, $withBox, $newBoxType);
        }

        return implode('', $multihtml);
    }

    public function getName()
    {
        return 'article';
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('article_recommended', [$this, 'articleRecommended']),
            new \Twig_Function('article_latest', [$this, 'articleLatest']),
            new \Twig_Function('article_most_viewed', [$this, 'articleMostViewed']),
            new \Twig_Function('article_from_category', [$this, 'articleFromCategory']),
            new \Twig_Function('article_render_one_from_subcategories', [$this, 'displayArticleFromCategories'])
        ];
    }

    public function articleRecommended()
    {
        $result = $this->getRepository('AppBundle:Article')->findAllRecommended();

        return $this->renderArticleList($result);
    }

    public function articleLatest()
    {
        $result = $this->getRepository('AppBundle:Article')->findAllLatest();

        return $this->renderMediaObjects($result);
    }

    public function articleMostViewed()
    {
        $result = $this->getRepository('AppBundle:Article')->findAllMostViewed();

        return $this->renderArticleList($result);
    }

    public function articleFromCategory($category)
    {
        $result = $this->getRepository('AppBundle:Article')->findOneInSubcategory($category);

        return $this->renderSimpleArticle($result);
    }

    public function displayArticleFromCategories()
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $totalCategories = $entityManager->getRepository('AppBundle:Category')->findAllCountedActive();
        $elementsPerRow = 4;
        $maxRows = floor($totalCategories / $elementsPerRow);
        $rows = 1;
        $html = [];
        $articles = [];

        foreach ($entityManager->getRepository('AppBundle:Category')->findAllSubcategories() as $subcategory) {
            $articles[] = $entityManager->getRepository('AppBundle:Article')->findOneInSubcategory($subcategory->getId());
        }

        for ($i = 0; $i < $elementsPerRow; $i++) {
            if ($i == 0) {
                $html[] = '<div class="row box">';
            }

            foreach ($articles as $article) {
                if (!is_null($article)) {
                    $html[] = sprintf('<div class="col-md-%d">', $elementsPerRow);
                    $html[] = '<div class="panel panel-default">';
                    $html[] = '<div class="panel-heading">';
                    $html[] = sprintf(
                        '<a href="%s">%s</a>'
                        , $this->container->get('router')->generate('main_category', ['category' => $article->getCategory()->getSlug()])
                        , $article->getCategory()->getName()
                    );
                    $html[] = '</div>';
                    $html[] = '<div class="body">';
                    $html[] = $this->renderArticle($article, 'post_type_2', true);
                    $html[] = '</div>';
                    $html[] = '</div>';
                    $html[] = '</div>';
                }
            }

            if ($i % $elementsPerRow == 3) {
                $html[] = '</div>';
                $i = 0;
                ++$rows;
            }

            if ($rows == $maxRows) {
                break;
            }
        }

        return implode('', $html);
    }
}
