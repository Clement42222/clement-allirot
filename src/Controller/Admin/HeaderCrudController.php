<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $required = true;
        if ($pageName == 'edit') {
            $required = false;
        }

        return [
            TextField::new('title', 'Titre'),
            TextEditorField::new('content', 'Description'),
            TextField::new('buttonTitle', 'Titre du bouton'),
            TextField::new('buttonLink', 'URL du bouton'),
            ImageField::new('illustration')
                ->setLabel('Image')
                ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]') // schema renommage pour les fichiers téléchargés
                ->setBasePath('/uploads') // dossier (a partir /public/) où seront affichées les images dans la liste des produits
                ->setUploadDir('/public/uploads') // dossier où seront téléchargées images
                ->setRequired($required),

            ImageField::new('illustrationMobile')
                ->setLabel('Image pour version mobile')
                ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]') // schema renommage pour les fichiers téléchargés
                ->setBasePath('/uploads') // dossier (a partir /public/) où seront affichées les images dans la liste des produits
                ->setUploadDir('/public/uploads') // dossier où seront téléchargées images
                ->setRequired($required),
        ];
    }
}
