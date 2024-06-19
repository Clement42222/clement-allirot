<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Carrier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addresses', EntityType::class, [
                'label' => "Choisissez votre adresse de livraison",
                'required' => true,
                'class' => Address::class,
                'expanded' => true, // transformer une liste en case à cocher
                'choices' => $options['addresses'],
                'label_html' => true // pouvoir mettre un label en HTML
            ])
            ->add('carriers', EntityType::class, [
                'label' => "Choisissez votre transporteur",
                'required' => true,
                'class' => Carrier::class,
                'expanded' => true, // transformer une liste en case à cocher
                'label_html' => true // pouvoir mettre un label en HTML
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-success w-100'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'addresses' => null
        ]);
    }
}
