<?php
namespace AppBundle\Form;

use AppBundle\Form\Type\DateTimePickerType;
use AppBundle\Util\AdChoiceConverter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', ChoiceType::class, [
                'choices' => AdChoiceConverter::$positions,
                'label'   => 'Alege poziția',
                'placeholder' => 'Alege o poziție...',
            ])
            ->add('title', TextType::class, [
                'label' => 'Titlul reclamei',
            ])
            ->add('message', TextareaType::class, [
                'label'    => 'Informații (intern) (opțional)',
                'required' => false,
            ])
            ->add('advertiser', EntityType::class, [
                'label'        => 'Autorul reclamei',
                'class'        => 'AppBundle\Entity\User',
                'choice_label' => 'fullName',
                'placeholder'  => 'Alege un utilizator...',
            ])
            ->add('link', UrlType::class, [
                'label' => 'Adresa site',
            ])
            ->add('image', UrlType::class, [
                'label'    => 'Adresa imagine',
                'required' => false,
            ])
            ->add('expiredAt', DateTimePickerType::class, [
                'label' => 'Data expirării',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'data_class' => 'AppBundle\Entity\Ad',
            ]);
    }

    public function getBlockPrefix()
    {
        return 'appbundle_ad';
    }

}