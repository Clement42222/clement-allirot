<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        //Gérer les erreurs
        $error = $authenticationUtils->getLastAuthenticationError();

        //Dernier username (email)
        // si user se trompe de mot passe (il aura pas besoin de retaper l'email)
        $lastUsername = $authenticationUtils->getLastUsername(); 

        return $this->render('login/index.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(Security $security)
    {
        // logout the user in on the current firewall
        $response = $security->logout();

        // you can also disable the csrf logout
        $response = $security->logout(false);
    }
}
