<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nume de utilizator',
            ])
            ->add('fullName', TextType::class, [
                'label' => 'Nume complet',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresa de e-mail',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Parola',
                ],
                'second_options' => [
                    'label' => 'Repetă parola',
                ],
            ])
            ->add('about', TextareaType::class, [
                'label' => 'Despre utilizator',
                'required' => false,
            ])
            ->add('superAdmin', CheckboxType::class, [
                'label' => 'Administrator site',
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Cont activ?',
                'attr' => [
                    'checked' => true,
                ],
            ])
            ->add('position', TextType::class, [
                'label' => 'Poziția',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_profile';
    }
}
