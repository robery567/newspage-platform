<?php
namespace AppBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MobileDetectExtension extends \Twig_Extension
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    private function getDetector()
    {
        return $this->container->get('lib.mobile_detect');
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('is_mobile', [$this, 'isMobile']),
            new \Twig_Function('is_tablet', [$this, 'isTablet']),
            new \Twig_Function('is_desktop', [$this, 'isDesktop']),
            new \Twig_Function('is_os', [$this, 'isOs']),
            new \Twig_Function('is_browser', [$this, 'isBrowser']),
        ];
    }

    public function isMobile()
    {
        return $this->getDetector()->isMobile();
    }

    public function isTablet()
    {
        return $this->getDetector()->isTablet();
    }

    public function isDesktop()
    {
        return (!$this->isMobile()) && (!$this->isTablet());
    }

    public function isOs($os)
    {
        $osName = $os;

        if (preg_match('/(ios)/i', $os)) {
            $osName = 'i';
        }

        return $this->getDetector()->{'is' . ucfirst($osName) . 'OS'}();
    }

    public function isBrowser($browser)
    {
        return $this->getDetector()->is($browser);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mobile_detect';
    }
}