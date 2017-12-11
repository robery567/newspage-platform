<?php
namespace AppBundle\Twig\Extension;

use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Sylva\Builder\HtmlBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MenuCollectionExtension extends \Twig_Extension
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    private function getRepository(string $repository): EntityRepository
    {
        return $this->container->get('doctrine.orm.entity_manager')->getRepository($repository);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'menu_collection';
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_Function('platform_main_menu', [$this, 'getMainMenu']),
            new \Twig_Function('platform_footer_categories', [$this, 'getFooterCategories']),
        ];
    }

    public function getMainMenu(): string
    {
        $html = new HtmlBuilder();
        /** @var CategoryRepository $repository */
        $repository = $this->getRepository('AppBundle:Category');
        $router = $this->container->get('router');

        $html->add('nav')->add('ul');

        /** @var array $categoriesName */
        /** @var @todo $categoriesName should be fetched from Settings */
        $categoriesName = [
            'eveniment', '112', 'reportaj', 'politic', 'cultură', 'opinii', 'știrile nebune'
        ];

        /** @var Category $parent */
        foreach ($repository->findCategoriesIn($categoriesName) as $parent) {
            $html->add('li', ['data-content' => 'category-'.$parent->getId()])
                 ->addTag('a', mb_strtoupper($parent->getName()), [
                     'href' => $router->generate('main_category', ['category' => $parent->getSlug()])
                 ]);

            $html->add('/li');
        }

        $html->add('li')->addTag('a', 'REDACTIA', [
            'href' => $router->generate('main_redactors')
        ])->add('/li');

        $html->add('/ul')->add('/nav');

        return $html->get();
    }

    public function getFooterCategories(): string
    {
        /** @var CategoryRepository $repository */
        $repository = $this->getRepository('AppBundle:Category');
        $html = new HtmlBuilder();

        /** @var Category $parent */
        foreach ($repository->findAllParents() as $parent) {
            $html->add('div', ['class' => 'column'])
                 ->add('div', ['class' => 'block_footer_categories'])
                 ->addTag('h3', $parent->getName())
                 ->add('ul');

            /** @var Category $child */
            foreach ($repository->findAllRetardedChildren($parent->getId()) as $child) {
                $html->add('li')
                     ->addTag('a', $child->getName(), [
                         'href' => $this->container->get('router')
                                                   ->generate('main_category', ['category' => $child->getSlug()]),
                         'rel' => 'nofollow'
                     ])
                     ->add('/li');
            }

            $html->add('/ul')->add('/div')->add('/div');
        }

        return $html->get();
    }
}
