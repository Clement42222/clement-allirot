<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $user = new User();

        $form = $this->createForm(RegisterUserType::class, $user);

        $form->handleRequest($request); // ça signifie que le Form doit Ecouter la request

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManagerInterface->persist($data); //figer data
            $entityManagerInterface->flush(); //enregistrer data

            $this->addFlash(
                'success',
                'Votre compte est correctement créé, veuillez vous connecter !'
            );

            //return to login page
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}
