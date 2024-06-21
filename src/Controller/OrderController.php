<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    /*
    *   1ère étape du tunnel d'achat
    *   Choix de l'adresse livraison et du transporteur
    */
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {
        $addresses = $this->getUser()->getAddresses();

        if (count($addresses) === 0) {
            return  $this->redirectToRoute('app_account_address_form');
        }

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            'action' => $this->generateUrl('app_order_summary') // redirige vers cette route apres validation form
        ]);

        return $this->render('order/index.html.twig', [
            'deliveryForm' => $form->createView(),
        ]);
    }

    /*
    *   2ème étape du tunnel d'achat
    *   Récap de la commande de l'utilisateur
    *   Insertion en base de données
    *   Préparation du paiement vers Stripe
    */
    #[Route('/commande/recapitulatif', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManagerInterface): Response
    {
        /*  methods:['POST'] à coté de name: 'app_order_summary' -->
        *   utilisé pour dire que cette route ne peut 
        *   être que POST et pas GET par exemple
        */
        if($request->getMethod() != 'POST'){
            return $this->redirectToRoute('app_cart');
        }

        $products = $cart->getCart();

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAddresses(),
        ]);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            // get data sous forme array
            $data = $form->getData();

            //get data sous forme objet
            $carriers = $form->get('carriers')->getData();
            $addresses = $form->get('addresses')->getData();

            // création de la chaine adresse
            $address = $addresses->getFirstname(). ' ' . $addresses->getLastname(). '<br>';
            $address .= $addresses->getAddress() . '<br>';
            $address .= $addresses->getPostal() . ' '. $addresses->getCity() . '<br>';
            $address .= $addresses->getCountry() . '<br>';
            $address .= $addresses->getPhone();

            // stocker information en base de données
            $order = new Order();
            $order->setCreatedAt(new DateTime());
            $order->setState(1);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($address);
            $order->setUser($this->getUser());

            foreach ($products as $idProduct => $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product["object"]->getName());
                $orderDetail->setProductIllustration($product["object"]->getIllustration());
                $orderDetail->setProductPrice($product["object"]->getPrice());
                $orderDetail->setProductTva($product["object"]->getTva());
                $orderDetail->setProductQuantity($product["qty"]);
                $order->addOrderDetail($orderDetail);
            }

            $entityManagerInterface->persist($order);
            $entityManagerInterface->flush();
        }

        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'order' => $order,
            'cart' => $products,
            'totalHt' => $cart->getTotalHt(),
            'totalWt' => $cart->getTotalWt()
        ]);
    }
}
