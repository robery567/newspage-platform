<?php
namespace AppBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TagExtension extends \Twig_Extension
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'tag';
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_Function('article_related', [$this, 'getSimilarArticlesByTag']),
        ];
    }

    public function getSimilarArticlesByTag($tagCollections)
    {
        $tags = [];
        foreach ($tagCollections as $tag) {
            $tags[] = $tag->getName();
        }

        $result = $this->container->get('doctrine.orm.entity_manager')
                                  ->getRepository('AppBundle:Article')
                                  ->findAllBySimilarTags($tagCollections);

        return $result;
    }

    public function getTagCloud()
    {
        return [];
    }
}
