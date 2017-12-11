<?php
namespace AppBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StatisticsExtension extends \Twig_Extension
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
        return 'panel_statistics';
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_Function('panel_statistics_article', [$this, 'getStatistics']),
            new \Twig_Function('panel_statistics_redactors', [$this, 'getRedactors']),
        ];
    }

    public function getStatistics(): int
    {
        return $this->getArticleCount();
    }

    public function getRedactors(): int
    {
        return $this->getRedactorsCount();
    }

    private function getArticleCount(): int
    {
        return $this->getRepository('AppBundle:Article')->findAllCountedArticles();
    }

    private function getRedactorsCount(): int
    {
        return $this->getRepository('AppBundle:User')->findAllCountedRedactors();
    }

    private function getRepository($repository)
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository($repository);
    }

    private function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
