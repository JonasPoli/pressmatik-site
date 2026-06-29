<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SiteController extends AbstractController
{
    #[Route('/{_locale}/', name: 'app_home', requirements: ['_locale' => 'pt|en|es'])]
    public function home(string $_locale): Response
    {
        return $this->render('site/home.html.twig', [
            'locale' => $_locale,
        ]);
    }

    #[Route('/{_locale}/produtos/{slug}', name: 'app_product_detail', requirements: ['_locale' => 'pt|en|es'])]
    public function productDetail(string $_locale, string $slug): Response
    {
        return $this->render('site/product_detail.html.twig', [
            'locale' => $_locale,
            'slug' => $slug,
        ]);
    }
}
