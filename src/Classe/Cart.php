<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{

    public function __construct(
        private RequestStack $requestStack,
    ) {
        // Accessing the session in the constructor is *NOT* recommended, since
        // it might not be accessible yet or lead to unwanted side-effects
        // $this->session = $requestStack->getSession();
    }

    /*
        Fonction permettant l'ajout de produit au panier
    */
    public function add($product)
    {
        // obtenir la session en cours
        $cart = $this->getCart();

        // obtenir la quantité actuelle et y ajouter + 1
        $qty = 1;
        if (isset($cart[$product->getId()]['qty'])) {
            $qty = $cart[$product->getId()]['qty'] + 1;
        }

        // Mettre à jour le produit et ses infos dans la variable $cart
        $cart[$product->getId()] = [
            'object' => $product,
            'qty' => $qty
        ];

        // Enregistrer la $cart dans session
        $this->requestStack->getSession()->set('cart', $cart);
    }


    /*
        Fonction permettant la suppression d'un produit au panier
    */
    public function decrease($idProduct)
    {
        // obtenir la session en cours
        $cart = $this->getCart();

        // erreur si cart n'existe pas en session
        if (!isset($cart[$idProduct]['qty'])) {
            return "error";
        }

        // obtenir la quantité actuelle
        $qty = $cart[$idProduct]['qty'];

        // si qty == 1 on supprime le produit
        if($qty == 1){
            unset($cart[$idProduct]);
            // Enregistrer la $cart dans session
            $this->requestStack->getSession()->set('cart', $cart);
            return;
        }

        // obtenir la quantité actuelle et y enlever - 1
        $qty = $cart[$idProduct]['qty'] - 1;

        // Mettre à jour le produit et ses infos dans la variable $cart
        $cart[$idProduct]['qty'] = $qty;

        // Enregistrer la $cart dans session
        $this->requestStack->getSession()->set('cart', $cart);
    }

    /*
        Fonction retournant le nombre total de produit au panier
    */
    public function fullQuantity(){

        // obtenir la session en cours
        $cart = $this->getCart();

        // erreur si cart n'existe pas en session
        if (!isset($cart)) {
            return 0;
        }

        //init la qty à 0;
        $qty = 0;

        // foreach les cart pour ajouter toutes les qty à $qty
        foreach ($cart as $idProduct => $value) {
            $qty += $value['qty'];
        }

        return $qty;
    }

    /*
        Fonction retournant le prix HT total des produits au panier
    */
    public function getTotalHt()
    {

        // obtenir la session en cours
        $cart = $this->getCart();

        // erreur si cart n'existe pas en session
        if (!isset($cart)) {
            return 0;
        }

        //init le prix with tax à 0;
        $totalHt = 0;

        // foreach les cart pour ajouter toutes les qty à $qty
        foreach ($cart as $value) {
            $totalHt += ($value['object']->getPrice() * $value['qty']);
        }

        return $totalHt;
    }

    /*
        Fonction retournant le prix TTC total des produits au panier
    */
    public function getTotalWt(){

        // obtenir la session en cours
        $cart = $this->getCart();

        // erreur si cart n'existe pas en session
        if (!isset($cart)) {
            return 0;
        }

        //init le prix with tax à 0;
        $totalWt = 0;

        // foreach les cart pour ajouter toutes les qty à $qty
        foreach ($cart as $value) {
            $totalWt += ($value['object']->getPriceWt() * $value['qty']);
        }

        return $totalWt;
    }

    /*
        Fonction permettant la suppression de tous les produits du panier
    */
    public function remove(){
        // Supprimer l'object cart dans la session
        $this->requestStack->getSession()->remove('cart');
    }

    /*
        Fonction permettant d'obtenir l'objet cart en session
    */
    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');
    }
}
