<?php

namespace App\Twig;

use App\Classe\Cart;
use App\Repository\CategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    private $categoryRepository;
    private $cart;

    public function __construct(CategoryRepository $categoryRepository, Cart $cart)
    {
        $this->categoryRepository = $categoryRepository;
        $this->cart = $cart;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']), // nom filtre / nom fonction
            new TwigFilter('description', [$this, 'formatDescription']),
        ];
    }

    public function formatPrice($price) //price est la valeur avant l'appel du filtre
    {
        return number_format($price, '2', ',') . ' €';
    }

    public function formatDescription($description)
    {
        if ($description && strpos($description, '&nbsp;') !== false) {
            // Rechercher les occurrences de &nbsp;
            $parts = explode('&nbsp;', $description);

            // Prendre seulement la première partie avant chaque &nbsp;
            $description = reset($parts);
        }

        $description .= "...";


        return $description;
    }

    //créer variables globals a utiliser partout dans environment twig
    public function getGlobals(): array
    {
        return [
            'allCategories' => $this->categoryRepository->findAll(),
            'fullCartQuantity' => $this->cart->fullQuantity()
        ];
    }
}
