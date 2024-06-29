<?php

namespace App\Controller;

use App\Repository\HeaderRepository;
use App\Repository\ProductRepository;
use App\Service\MobileDetector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private $mobileDetector;

    public function __construct(MobileDetector $mobileDetector)
    {
        $this->mobileDetector = $mobileDetector;
    }
    
    #[Route('/', name: 'app_home')]
    public function index(HeaderRepository $headerRepository, ProductRepository $productRepository): Response
    {
        $isMobile = $this->mobileDetector->isMobile();

        return $this->render('home/index.html.twig', [
            'headers' => $headerRepository->findAll(),
            'productsInHomepage' => $productRepository->findByIsHomePage(true),
            'isMobile' => $isMobile
        ]);
    }
}
