<?php

namespace App\Controller;

use App\Classe\Cart as Cart;
use App\Repository\ProductRepository;
use App\Service\MobileDetector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    private $mobileDetector;

    public function __construct(MobileDetector $mobileDetector)
    {
        $this->mobileDetector = $mobileDetector;
    }

    #[Route('/mon-panier/{motif}', name: 'app_cart', defaults: ['motif' => null])]
    public function index(Cart $cart, $motif): Response
    {
        if ($motif === "annulation") {
            $this->addFlash(
                'info',
                'Paiement annulé : Vous pouvez mettre à jour votre panier et votre commande.'
            );
        }

        $isMobile = $this->mobileDetector->isMobile();

        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart(),
            'totalHt' => $cart->getTotalHt(),
            'totalWt' => $cart->getTotalWt(),
            'isMobile' => $isMobile
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add(
        $id,
        Cart $cart,
        ProductRepository $productRepository,
        Request $request
    ) {
        // derniere url visitée
        $referer = $request->headers->get("referer");

        $product = $productRepository->findOneById($id);

        $cart->add($product);

        $this->addFlash(
            'success',
            'Produit correctement ajouté à votre panier'
        );

        return $this->redirect($referer);
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease(
        $id,
        Cart $cart,
    ) {
        $cart->decrease($id);

        $this->addFlash(
            'success',
            'Produit correctement supprimé de votre panier'
        );

        return $this->redirectToRoute("app_cart");
    }

    #[Route('/cart/remove', name: 'app_cart_remove')]
    public function remove(Cart $cart)
    {
        $cart->remove();

        $this->addFlash(
            'success',
            'Votre panier a bien été vidé'
        );

        return $this->redirectToRoute('app_home');
    }
}
