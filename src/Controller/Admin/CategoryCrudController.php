<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Catégorie') //nom titre crud en haut page au singulier
            ->setEntityLabelInPlural('Catégories') //nom titre crud en haut page au pluriel
            // ...
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')
                ->setLabel('Titre')
                ->setHelp('Titre de la catégorie'),
            SlugField::new('slug')
                ->setLabel('URL')
                ->setTargetFieldName('name')
                ->setHelp('Url de la catégorie automatiquement générée'),
                // setTargetFieldName --> champ sur lequel le slug va se baser pour se créer
                
        ];
    }
}
