<?php
namespace AppBundle\Form;

use AppBundle\Form\Type\TagsInputType;
use AppBundle\Util\ArticleChoiceConverter;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'      => 'Titlu articol',
            ])
            ->add('articleId', TextType::class, [
                'label' => 'ID Articol',
                'attr' => ['readonly' => true]
            ])
            ->add('category', EntityType::class, [
                'label'         => 'Categorie',
                'class'         => 'AppBundle\Entity\Category',
                'choice_label'  => 'name',
                'placeholder'   => 'Alege o categorie...',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')
                                      ->where('c.parent IS NOT NULL')
                                      ->andWhere('c.isActive = 1')
                                      ->orderBy('c.parent', 'ASC');
                },
            ])
            ->add('preview', TextareaType::class, [
                'label' => 'Previzualizare articol',
            ])
            ->add('tags', TagsInputType::class, [
                'label'    => 'Tag-uri (separate prin virgulă)',
                'required' => false,
            ])
            ->add('content', TextareaType::class, [
                'label'      => 'Corpul articolului',
            ])
            ->add('media', HiddenType::class, [
                'required'   => false,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => ArticleChoiceConverter::$types,
                'label'   => 'Alege tipul',
                'placeholder' => 'Alege tipul articolului...',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Salvează',
                'attr'  => [
                    'class' => 'btn btn-success btn-lg',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Article',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_article';
    }

}
