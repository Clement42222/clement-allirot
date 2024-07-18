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

        // Si panier vide --> reirection vers home page
        if (empty($cart->getCart())) {
            return $this->redirectToRoute('app_home');
        }

        $isMobile = $this->mobileDetector->isMobile();

        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart(),
            'totalHt' => $cart->getTotalHt(),
            'totalWt' => $cart->getTotalWt(),
            'isMobile' => $isMobile
        ]);
    }

    #[Route('/cart/add/{id}/{is_referer}', name: 'app_cart_add', defaults: ['is_referer' => false])]
    public function add(
        $id,
        $is_referer,
        Cart $cart,
        ProductRepository $productRepository,
        Request $request
    ) {
        // derniere url visitée
        $referer = $request->headers->get("referer");

        $product = $productRepository->findOneById($id);

        $cart->add($product);

        // $this->addFlash(
        //     'success',
        //     'Produit correctement ajouté à votre panier'
        // );

        if ($is_referer) {
            return $this->redirect($referer);
        }
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrease/{id}/{is_referer}', name: 'app_cart_decrease', defaults: ['is_referer' => false])]
    public function decrease(
        $id,
        $is_referer,
        Cart $cart,
        Request $request
    ) {
        $cart->decrease($id);

        // $this->addFlash(
        //     'success',
        //     'Produit correctement supprimé de votre panier'
        // );

        if ($is_referer) {
            return $this->redirect($request->headers->get('referer'));
        }
        return $this->redirectToRoute("app_cart");
    }

    /*
        Fonction permettant la suppression d'un produit du panier
    */
    #[Route('/cart/remove/{id}/{is_referer}', name: 'app_cart_remove_product', defaults: ['is_referer' => false])]
    public function removeProduct($id, Cart $cart, $is_referer, Request $request)
    {
        $cart->removeProduct($id);

        // $this->addFlash(
        //     'success',
        //     'Produit correctement supprimé de votre panier'
        // );

        if ($is_referer) {
            return $this->redirect($request->headers->get('referer'));
        }
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove', name: 'app_cart_remove')]
    public function remove(Cart $cart)
    {
        $cart->remove();

        // $this->addFlash(
        //     'success',
        //     'Votre panier a bien été vidé'
        // );

        return $this->redirectToRoute('app_home');
    }
}
