<?php
namespace AppBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RoleExtension extends \Twig_Extension
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'role';
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('role_list', [$this, 'listRoles']),
        ];
    }

    public function listRoles(array $roles)
    {
        return implode(', ', $roles);
    }
}
