<?php

namespace App\Controller\Account;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressUserType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddressController extends AbstractController
{
    private $entityManagerInterface;
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }
    
    #[Route('/compte/adresses', name: 'app_account_addresses')]
    public function index(): Response
    {
        
        return $this->render('account/address/index.html.twig');
    }

    #[Route('/compte/adresses/delete/{id}', name: 'app_account_addresses_delete')]
    public function delete($id, AddressRepository $addressRepository): Response
    {
        if (!$id) {
            // message erreur
            $this->addFlash(
                'error',
                'Adresse non trouvée !'
            );
            return $this->redirectToRoute('app_account_addresses');
        }

        $address = $addressRepository->findOneById($id);

        if (!$address || ($address->getUser() != $this->getUser())) {
            // message erreur
            $this->addFlash(
                'error',
                'Vous n\'êtes pas autorisés à effectuer cette action'
            );

            return $this->redirectToRoute('app_account_addresses');
        }

        if ($address) {
            $this->entityManagerInterface->remove($address);
            $this->entityManagerInterface->flush();

            $this->addFlash(
                'success',
                'L\'adresse a bien été supprimée'
            );
        }
        return $this->redirectToRoute('app_account_addresses');
    }

    /* 
        defaults: ['id' => null] --> déclare donc une valeur par défaut au param id
    */
    #[Route('/compte/adress/ajouter/{id}', name: 'app_account_address_form', defaults: ['id' => null])]
    public function form(Request $request, $id, AddressRepository $addressRepository, Cart $cart): Response
    {
        if ($id) {
            // modification adresse existante
            $address = $addressRepository->findOneById($id);

            // vérification adresse existe bien
            // vérification que l'adresse appartient bien au user connecté
            if (!$address || ($address->getUser() != $this->getUser())) {
                // message erreur
                $this->addFlash(
                    'error',
                    'Vous n\'êtes pas autorisés à effectuer cette action'
                );

                return $this->redirectToRoute('app_account_addresses');
            }
        } else {
            // création nouvelle adresse
            $address = new Address();
        }

        // dire que l'adresse est lié à l'user connecté
        $address->setUser($this->getUser());

        // création formulaire
        $form = $this->createForm(AddressUserType::class, $address); // passage objet Adresse et ses data au formulaire

        //ecouter la requete
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManagerInterface->persist($address); // persist car on est dans une création objet (en passant objet address)
            $this->entityManagerInterface->flush(); // enregistrer en BDD

            $this->addFlash(
                'success',
                'Votre adresse est correctement sauvegardée'
            );

            // si l'user a des produits dans son panier
            if ($cart->fullQuantity() > 0){
                // je le redirige vers sa commande
                return $this->redirectToRoute('app_order');
            }
            
            return $this->redirectToRoute('app_account_addresses');
        }

        return $this->render('account/address/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
