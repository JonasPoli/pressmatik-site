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
        $languages = $request->getLanguages();
        $supportedLocales = ['pt', 'en', 'es'];
        $defaultLocale = 'pt';

        foreach ($languages as $lang) {
            $langShort = strtolower(substr($lang, 0, 2));
            if (in_array($langShort, $supportedLocales)) {
                return $this->redirectToRoute('app_home', ['_locale' => $langShort]);
            }
        }

        return $this->redirectToRoute('app_home', ['_locale' => $defaultLocale]);
    }
}
