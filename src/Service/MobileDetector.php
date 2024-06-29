<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class MobileDetector
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function isMobile(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        $userAgent = $request->headers->get('User-Agent');
        return preg_match('/Mobile|Android|BlackBerry|iPhone|Windows Phone/i', $userAgent);
    }
}
