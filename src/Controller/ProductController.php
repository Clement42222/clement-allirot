<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/produit/{slug}', name: 'app_product')]
    public function index($slug, ProductRepository $productRepository, Cart $cart): Response
    {
        $product = $productRepository->findOneBySlug($slug);

        if (!$product) {
            return $this->redirectToRoute('app_home');
        }

        // Récupérer les Cart dans session
        $cart = $cart->getCart();

        if (empty($cart)) {
            $cart = [];
        }
        // Récupérer les clés du tableau $cart dans un array
        $productsInCart = array_keys($cart);

        return $this->render('product/index.html.twig', [
            'product' => $product,
            'productsInCart' => $productsInCart
        ]);
    }

    // //facon de recuperer le produit plus facilement avec MapEntity
    // Auto Mapping
    
    // #[Route('/produit/{slug}', name: 'app_product')]
    // public function index(#[MapEntity(slug: 'slug')] Product $product): Response{
    //     if (!$product) {
    //         return $this->redirectToRoute('app_home');
    //     }
    //     return $this->render('product/index.html.twig', [
    //         'product' => $product,
    //     ]);
    // }
}
