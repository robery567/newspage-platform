<?php
/**
 * Created by PhpStorm.
 * User: hktr92
 * Date: 6/14/17
 * Time: 7:23 PM
 */

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Tag;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;

class TagArrayToStringTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function transform($value)
    {
        return implode(',', $value);
    }

    public function reverseTransform($value)
    {
        if (('' === $value) || (null === $value)) {
            return [];
        }

        $names = array_filter(array_unique(array_map('trim', explode(',', $value))));

        $tags = $this->manager->getRepository('AppBundle:Tag')->findBy(['name' => $names]);
        $newNames = array_diff($names, $tags);

        foreach ($newNames as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $tags[] = $tag;
        }

        return $tags;
    }

}