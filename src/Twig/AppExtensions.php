<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']) // nom filtre / nom fonction
        ];
    }

    public function formatPrice($number) //number est la valeur avant l'appel du filtre
    {
        return number_format($number, '2', ',') . ' €';
    }

    //créer variables globals a utiliser partout dans environment twig
    public function getGlobals(): array
    {
        return [
            'allCategories' => $this->categoryRepository->findAll()
        ];
    }
}
