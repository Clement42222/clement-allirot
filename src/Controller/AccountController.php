<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')]
    public function password(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManagerInterface): Response
    {
        // Get the currently authenticated user
        $user = $this->getUser();

        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher
        ]); // deuxieme param $user pour associer le form data à l'user

        $form->handleRequest($request); // ecouter la requete

        if($form->isSubmitted() && $form->isValid()){
            //$data = $form->getData();
            //persist uniquement pour nouvel objet --> new User()
            $entityManagerInterface->flush(); // mettre a jour bDD

            //notification
            $this->addFlash(
                'success',
                'Votre mot de passe a correctement été mis à jour'
            );
        }

        return $this->render('account/password.html.twig', [
            'modifyPwd' => $form->createView()
        ]);
    }
}
