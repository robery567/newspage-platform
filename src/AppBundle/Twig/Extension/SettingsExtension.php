<?php
namespace AppBundle\Twig\Extension;

use AppBundle\Util\Settings as AppSettings;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SettingsExtension extends \Twig_Extension
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    private function getAppSettings(): AppSettings
    {
        return $this->container->get(AppSettings::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'settings';
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('config', [$this, 'getSettings']),
            new \Twig_Function('config_raw', [$this, 'getRawSettings']),
        ];
    }

    public function getSettings(string $name)
    {
        return $this->getAppSettings()->get($name);
    }

    public function getRawSettings(string $name)
    {
        return $this->getAppSettings()->getRaw($name);
    }
}
