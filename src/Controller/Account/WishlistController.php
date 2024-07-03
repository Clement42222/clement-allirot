<?php

namespace App\Controller\Account;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WishlistController extends AbstractController
{
    #[Route('/compte/liste-de-souhait', name: 'app_account_wishlist')]
    public function index(): Response
    {
        return $this->render('account/wishlist/index.html.twig');
    }

    #[Route('/compte/liste-de-souhait/add/{id}', name: 'app_account_wishlist_add')]
    public function add(
        ProductRepository $productRepository,
        $id,
        EntityManagerInterface $entityManagerInterface,
        Request $request
    ) {
        // Récupérer objet du produit souhaité
        $product = $productRepository->findOneById($id);

        // Si produit existe
        if ($product) {
            //récupération utilisateur connecté
            $user = $this->getUser();

            // ajouter le produit à la wishlist
            $user->addWishlist($product);

            //Sauvegarder en base de données
            $entityManagerInterface->flush();

            $this->addFlash(
                'success',
                'Produit correctement ajouté à votre liste de souhait'
            );
        } else {
            $this->addFlash(
                'danger',
                'Produit introuvable'
            );
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/compte/liste-de-souhait/remove/{id}', name: 'app_account_wishlist_remove')]
    public function remove(
        ProductRepository $productRepository,
        $id,
        EntityManagerInterface $entityManagerInterface,
        Request $request
    ) {
        // Récupérer objet du produit à supprimer
        $product = $productRepository->findOneById($id);

        // Si produit existe
        if ($product) {
            $this->addFlash(
                'success',
                'Produit correctement supprimé de votre liste de souhait'
            );

            //récupération utilisateur connecté
            $user = $this->getUser();
            // supprimer le produit à la wishlist
            $user->removeWishlist($product);

            //Sauvegarder en base de données
            $entityManagerInterface->flush();
        } else {
            $this->addFlash(
                'danger',
                'Produit introuvable'
            );
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
