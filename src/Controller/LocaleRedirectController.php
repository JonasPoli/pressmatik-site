<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LocaleRedirectController extends AbstractController
{
    #[Route('/')]
    public function redirectToLocale(Request $request): Response
    {
        return $this->redirectToRoute('app_home', ['_locale' => 'pt']);
    }
}
