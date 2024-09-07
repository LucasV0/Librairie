<?php



namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Transporter;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('user', EntityType::class, [
            'class' => User::class,
            'choices' => [$options['user']], 
            'choice_label' => function (User $user) {
                return $user->getAddress(); 
            },
            'label' => false,
            'required' => true,
            'multiple' => false,
            'expanded' => true,
        ])
            ->add('transporter', EntityType::class, [
                'class' => Transporter::class,
                'label' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'attr' => ['class' => 'form-radio-group'],
                'label_attr' => ['class' => 'form-radio-label']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => [],
        ]);
    }
}


