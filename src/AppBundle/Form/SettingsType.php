<?php
namespace AppBundle\Form;

use AppBundle\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
                'label' => 'Numele setării',
            ])->add('value', TextType::class, [
                'label' => 'Valoarea setării',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Settings::class]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_settings_form';
    }
}
