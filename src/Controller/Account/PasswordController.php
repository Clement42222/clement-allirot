<?php

namespace App\Controller\Account;

use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class PasswordController extends AbstractController
{
    private $entityManagerInterface;
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }
    
    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Get the currently authenticated user
        $user = $this->getUser();

        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher
        ]); // deuxieme param $user pour associer le form data à l'user

        $form->handleRequest($request); // ecouter la requete

        if ($form->isSubmitted() && $form->isValid()) {
            //$data = $form->getData();
            //persist uniquement pour nouvel objet --> new User()
            $this->entityManagerInterface->flush(); // mettre a jour bDD

            //notification
            $this->addFlash(
                'success',
                'Votre mot de passe a correctement été mis à jour'
            );
        }

        return $this->render('account/password/index.html.twig', [
            'modifyPwd' => $form->createView()
        ]);
    }
}
