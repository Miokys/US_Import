<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class User1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class)
        ->add('name', TextType::class, [
            'label' => 'Nom',
            'required' => false,
        ])
        ->add('firstname', TextType::class, [
            'label' => 'Prenom',
            'required' => false,
        ])
        ->add('adress', TextType::class, [
            'label' => 'adresse',
            'required' => false,
        ])
        ->add('city', TextType::class, [
            'label' => 'Ville',
            'required' => false,
        ])
        ->add('zip', IntegerType::class, [
            'label' => 'Code Postal',
            'required' => false,
        ])
        ->add('birthdate', BirthdayType::class, [
            'label' => 'Date de naissance',
            'required' => false,
            'html5' => false,
            'format' => 'dd-MM-yyyy',
            'attr' => ['class' => 'd-flex col-3'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
