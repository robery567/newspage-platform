<?php
/**
 * Created by PhpStorm.
 * User: hktr92
 * Date: 6/14/17
 * Time: 7:19 PM
 */

namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\TagArrayToStringTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class TagsInputType extends AbstractType
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CollectionToArrayTransformer(), true)
                ->addModelTransformer(new TagArrayToStringTransformer($this->manager), true);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['tags'] = $this->manager->getRepository('AppBundle:Tag')->findAll();
    }

    public function getParent()
    {
        return TextType::class;
    }
}