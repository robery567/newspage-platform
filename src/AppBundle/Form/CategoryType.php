<?php
namespace AppBundle\Form;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nume categorie'
            ])
            ->add('position', ChoiceType::class, [
                'label' => 'Poziție',
                'choices' => [
                    'Primul'        => 0,
                    'Pe la început' => 15,
                    'La mijloc'     => 30,
                    'Pe la final'   => 45,
                    'Ultimul'       => 60,
                ]
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Categoria este publică?',
                'label_attr' => ['checked'],
                'required' => false
            ])
            ->add('parent', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Categoria părinte',
                'placeholder' => 'Alege categoria părinte...',
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')->where('c.parent IS NULL')->andWhere('c.isActive = 1')->orderBy('c.position', 'ASC');
                }
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Category'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_category';
    }


}
