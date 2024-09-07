<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email Address'
            ])
            ->add('firt_name', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('date_of_birth', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text', 
                'html5' => true, 
                'format' => 'yyyy-MM-dd',
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('phone', TextType::class, [
                'label' => 'Numéro de téléphone'
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true, // Active la protection CSRF
            'csrf_field_name' => '_token', // Nom du champ 
            'csrf_token_id' => 'user_item', // Identifiant unique pour le jeton CSRF
        ]);
    }
}

