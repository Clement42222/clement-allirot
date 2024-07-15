<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\ForgotPasswordFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ForgotPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    #[Route('/mot-de-passe-oublie', name: 'app_password')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        // Créer un formulaire
        $form = $this->createForm(ForgotPasswordFormType::class);
        $form->handleRequest($request);

        // Traiter les données du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $email = $form->getData()['email'];

            // Vérifier si l'email existe dans la base de données
            $user = $userRepository->findOneBy(['email' => $email]);

            // Envoyer une notification à l'utilisateur
            $this->addFlash('success', 'Si votre adresse email est associée à un compte, un email vous sera envoyé pour réinitialiser votre mot de passe.');

            // Si l'utilisateur existe, on reset le mot de passe et envoie le nouveau mot de passe par email
            if ($user) {
                // Créer un token qu'on va stocker dans la base de données
                $token = bin2hex(random_bytes(32));

                // Stocker le token dans la base de données
                $user->setToken($token);

                // Rendre le token valable pendant 10 minutes
                $user->setTokenExpireAt(new \DateTime('+10 minutes'));

                // Enregistrer en bdd
                $this->em->flush();

                // Récupérer le lien de réinitialisation du mot de passe
                $link = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $mail = new Mail();

                $vars = [
                    'link' => $link,
                ];

                $mail->send(
                    $user->getEmail(),
                    $user->getFirstname() . ' ' . $user->getLastname(),
                    "Modification de votre mot de passe",
                    "forgot_password.html",
                    $vars
                );
            }
        }

        return $this->render('password/index.html.twig', [
            'forgotPasswordForm' => $form->createView(),
        ]);
    }

    #[Route('/mot-de-passe/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, $token): Response
    {
        if (!$token) {
            $this->addFlash('danger', 'Token invalide');
            return $this->redirectToRoute('app_password');
        }

        // Récupérer l'utilisateur avec le token
        $user = $this->em->getRepository(User::class)->findOneBy(['token' => $token]);

        // Si l'utilisateur n'existe pas, redirigez vers la route app_password
        if (!$user) {
            $this->addFlash('danger', 'Utilisateur introuvable');
            return $this->redirectToRoute('app_password');
        }

        // Vérifier si le token est toujours valide
        $now = new \DateTime();
        if ($now > $user->getTokenExpireAt()) {
            $this->addFlash('danger', 'Token expiré');
            return $this->redirectToRoute('app_password');
        }

        $form = $this->createForm(ResetPasswordFormType::class, $user);

        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setToken(null);
            $user->setTokenExpireAt(null);

            // Récupérer les données du formulaire
            $data = $form->getData();

            $this->em->flush();

            $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('password/reset.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }
}
