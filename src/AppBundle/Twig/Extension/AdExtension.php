<?php
namespace AppBundle\Twig\Extension;

use AppBundle\Entity\Ad;
use AppBundle\Util\AdChoiceConverter;
use AppBundle\Util\Settings;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdExtension extends \Twig_Extension
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function getRepository()
    {
        return $this->container->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Ad');
    }

    public function getDisplayCount()
    {
        return $this->container->get(AdChoiceConverter::class)->getDisplayCount();
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('ad_position', [$this, 'getAdPosition']),
            new \Twig_Function('ad_status', [$this, 'getAdExpired']),
            new \Twig_Function('ad', [$this, 'loadAd']),
        ];
    }

    public function loadAd($position)
    {
        $results = $this->getRepository()->findByPosition($position);
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var Ad $result */
        foreach ($results as $result) {
            $result->setViews($result->getViews() + 1);
            $em->persist($result);
            $em->flush();
        }

        return $results;
    }

    public function getAdPosition($position)
    {
        return array_search($position, AdChoiceConverter::$positions);
    }

    public function getAdExpired(\DateTime $expire)
    {
        $format = $this->container->get(Settings::class)->get('platform.default_date_format');
        $now = new \DateTime();

        return ($now->format($format) < $expire->format($format)) ? 'Da' : 'Nu';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ad';
    }
}
