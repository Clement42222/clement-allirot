<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{
    #[Route('/commande/paiement/{order_id}', name: 'app_payment')]
    public function index($order_id, OrderRepository $orderRepository, EntityManagerInterface $entityManagerInterface)
    {
        $order = $orderRepository->findOneById($order_id);

        $order = $orderRepository->findOneBy(
            [
                'id' => $order_id,
                'user' => $this->getUser()
            ]
        );

        $stripe_secret_key = $_ENV['STRIPE_SECRET_KEY'];
        Stripe::setApiKey($stripe_secret_key);
        $app_url = $_ENV['APP_URL'];

        $products_for_stripe = [];

        if (!$order) {
            return $this->redirectToRoute('app_home');
        }

        $amount = 0;

        // Calcul du montant total pour les produits de la commande
        foreach ($order->getOrderDetails() as $orderDetail) {
            $productPriceWt = $orderDetail->getProductPriceWt() * 100;
            $amount += $productPriceWt * $orderDetail->getProductQuantity();
        }

        // Ajout des frais de transporteur au montant total
        $amount += $order->getCarrierPrice() * 100;

        $amount = round($amount);

        $products_for_stripe = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $amount,
                'product_data' => [
                    'name' => 'Total TTC'
                ]
            ],
            'quantity' => 1,
        ];

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [$products_for_stripe],
            'mode' => 'payment',
            'success_url' => $app_url . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $app_url . '/mon-panier/annulation',
        ]);

        //stocker l'id session checkout stripe en BDD
        $order->setStripeSessionId($checkout_session->id);
        $entityManagerInterface->flush();

        return $this->redirect($checkout_session->url);
    }

    #[Route('/commande/merci/{stripe_session_id}', name: 'app_payment_success')]
    public function success($stripe_session_id, OrderRepository $orderRepository,
    EntityManagerInterface $entityManagerInterface, Cart $cart)
    {
        $order = $orderRepository->findOneBy([
            'stripe_session_id' => $stripe_session_id,
            'user' => $this->getUser()
        ]);

        if (!$order) {
            return $this->redirectToRoute('app_home');
        }

        // MAJ statut commande en 'Paiement validé'
        if ($order->getState() === 1) {
            $order->setState(2);
            $entityManagerInterface->flush();

            //vider le panier
            $cart->remove();
        }

        return $this->render('payment/success.html.twig', [
            'order' => $order,
        ]);
    }



    /**
     * Code ci-dessous utilisé pour afficher les produits en détails dans stripe ..
     *  <!> Seulement erreur de prix TTC inexacte sur stripe dans ce cas-là <!>
     */
    // foreach ($order->getOrderDetails() as $orderDetail) {
    //     // calcul prix produit TTC
    //     $productPriceWt = $orderDetail->getProductPriceWt() * 100;

    //     array_push($products_for_stripe,  [
    //         'price_data' => [
    //             'currency' => 'eur', // type de monnaie
    //             'unit_amount' => number_format($productPriceWt, 0 , '', ''), // prix Unitaire ttc
    //             'product_data' => [
    //                 'name' => $orderDetail->getProductName(),
    //                 'images' => [
    //                     $app_url . '/uploads/' . $orderDetail->getProductIllustration()
    //                 ]
    //             ],
    //         ],
    //         'quantity' => $orderDetail->getProductQuantity(),
    //     ]);
    // }
    // // frais transporteur
    // array_push($products_for_stripe,  [
    //     'price_data' => [
    //         'currency' => 'eur',
    //         'unit_amount' => number_format($order->getCarrierPrice() * 100, 0, '', ''),
    //         'product_data' => [
    //             'name' => 'Transporteur : ' . $order->getCarrierName(),
    //         ],
    //     ],
    //     'quantity' => 1,
    // ]);
}
