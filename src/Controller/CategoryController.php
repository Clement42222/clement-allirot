<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/categorie/{slug}', name: 'app_category')]
    public function index($slug, CategoryRepository $categoryRepository, Cart $cart): Response
    {
        $category = $categoryRepository->findOneBySlug($slug);

        if (!$category) {
            return $this->redirectToRoute('app_home');
        }

        // Récupérer les Cart dans session
        $cart = $cart->getCart();

        if (empty($cart)) {
            $cart = [];
        }
        // Récupérer les clés du tableau $cart dans un array
        $productsInCart = array_keys($cart);

        return $this->render('category/index.html.twig', [
            'category' => $category,
            'productsInCart' => $productsInCart
        ]);
    }
}
