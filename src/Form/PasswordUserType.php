<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actualPassword', PasswordType::class, [
                'label' => 'Votre mot de passe actuel',
                'attr' => [
                    'placeholder' => 'Indiquez votre mot de passe actuel'
                ],
                'mapped' => false
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 30
                    ])
                ],
                'first_options'  => [
                    'label' => 'Votre nouveau mot de passe',
                    'attr' => [
                        'placeholder' => "Choisissez votre nouveau mot de passe"
                    ],
                    'hash_property_path' => 'password' // permet de crypter le mot de passe dans la requête
                ],
                'second_options' => [
                    'label' => 'Confirmez votre nouveau mot de passe',
                    'attr' => [
                        'placeholder' => "Confirmez votre nouveau mot de passe"
                    ],
                ],
                'mapped' => false, // dit de pas faire le lien entre entité et champ que je donne
            ])
            ->add('sumit', SubmitType::class, [
                'label' => 'Mettre à jour mon mot de passe',
                'attr' => [
                    'class' => 'btn btn-success',
                ]
            ])
            ->addEventListener(
                FormEvents::SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm(); // recupération formulaire
                    $user = $form->getConfig()->getOptions()['data']; // recup current user infos in BDD
                    $passwordHasher = $form->getConfig()->getOptions()['passwordHasher']; // recup passwordHasher

                    //Recup mot passe actuel saisi par utilisateur et comparer au PWD en BDD
                    $actualPwd = $form->get('actualPassword')->getData();
                    $isValid = $passwordHasher->isPasswordValid(
                        $user,
                        $actualPwd
                    );

                    //comparer
                    if(!$isValid){
                        //ajoute erreur au form
                        $form->get('actualPassword')->addError(new FormError("Votre mot de passe actuel n'est pas conforme."));
                    }
                }
            ); //ecouteur des qui se passe qq chose dans le form --> une action
        //1er param (quand est ce que je veux ecouteur --> la c'est quand on submit)
        //2eme param (qu'est ce que je veux faire)
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // lien entre formulaire et une entité --> donc transforme les data en objet User
            'passwordHasher' => null
        ]);
    }
}
