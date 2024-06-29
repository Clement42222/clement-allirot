<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit') //nom titre crud en haut page au singulier
            ->setEntityLabelInPlural('Produits') //nom titre crud en haut page au pluriel
            // ...
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $required = true;
        if ($pageName == 'edit') {
            $required = false;
        }

        return [
            TextField::new('name')
                ->setLabel('Nom')
                ->setHelp('Nom de votre produit'),
            BooleanField::new('isHomePage')
                ->setLabel('Produit à la une ?')
                ->setHelp('Vous permet d\'afficher un produit sur la homepage'),
            SlugField::new('slug')
                ->setTargetFieldName('name')
                ->setLabel('URL')
                ->setHelp('Url de votre produit'),
            TextEditorField::new('description')
                ->setLabel('Description')
                ->setHelp('Description de votre produit'),
            ImageField::new('illustration')
                ->setLabel('Image')
                ->setHelp('Image du produit en 600x600px')
                ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]') // schema renommage pour les fichiers téléchargés
                ->setBasePath('/uploads') // dossier (a partir /public/) où seront affichées les images dans la liste des produits
                ->setUploadDir('/public/uploads') // dossier où seront téléchargées images
                ->setRequired($required),
            NumberField::new('price')
                ->setLabel('Prix H.T')
                ->setHelp('Prix H.T de votre produit sans le sigle euro.'),
            ChoiceField::new('tva')
                ->setLabel('Taux de TVA')
                ->setChoices([
                    '5,5%' => '5.5',
                    '10%' => '10',
                    '20%' => '20',
                ]),
            AssociationField::new('category', 'Catégorie associée') // relation avec autre entity
        ];
    }
}
