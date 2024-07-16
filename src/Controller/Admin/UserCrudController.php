<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur') //nom titre crud en haut page au singulier
            ->setEntityLabelInPlural('Utilisateurs') //nom titre crud en haut page au pluriel
            // ...
        ;
    }

    // Gérer champs visibles / configurables (Créer / Modifier / Affichage)
    public function configureFields(string $pageName): iterable
    {
        //onlyOnIndex() --> uniquement visible sur page Index liste
        //onlyOnForms() --> uniquement visible dans les formulaires
        return [
            TextField::new('firstname')->setLabel('Prénom'),
            TextField::new('lastname')->setLabel('Nom'),
            TextField::new('email')->setLabel('Email'),
            ChoiceField::new('roles')
                ->setLabel('Rôle')
                ->setHelp('Vous pouvez attribuer un rôle à cet utilisateur')
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN'
                ])
                ->allowMultipleChoices()
        ];
    }
}
