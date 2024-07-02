<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Dompdf\Dompdf;

class InvoiceController extends AbstractController
{
    /*
        - Impression facture PDF pour un utilisateur connecté
        - Vérification commande pour utilisateur donné
    */
    #[Route('/compte/facture/impression/{id_order}', name: 'app_invoice_customer')]
    public function printForCustomer(OrderRepository $orderRepository, $id_order): Response
    {
        // Vérification objet commande - Existe ?
        $order = $orderRepository->findOneById($id_order);
        if (!$order) {
            return $this->redirectToRoute('app_account');
        }

        // Vérification objet commande - Utilisateur connecté peut y accéder ?
        if ($order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account');
        }

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();

        // obtenir le html du template twig
        $html = $this->renderView('invoice/index.html.twig', [
            'order' => $order
        ]);

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('facture.pdf', [
            'Attachment' => false // ouvrir le fichier dans le navigateur directement
        ]);

        exit();
    }

    /*
        - Impression facture PDF pour un Administrateur connecté
        - Vérification commande pour utilisateur donné
    */
    #[Route('/admin/facture/impression/{id_order}', name: 'app_invoice_admin')]
    public function printForAdmin(OrderRepository $orderRepository, $id_order): Response
    {
        // Vérification objet commande - Existe ?
        $order = $orderRepository->findOneById($id_order);
        if (!$order) {
            return $this->redirectToRoute('admin');
        }

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();

        // obtenir le html du template twig
        $html = $this->renderView('invoice/index.html.twig', [
            'order' => $order
        ]);

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('facture.pdf', [
            'Attachment' => false // ouvrir le fichier dans le navigateur directement
        ]);

        exit();
    }
}
