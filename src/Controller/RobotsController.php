<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * robots.txt dinâmico.
 * - No domínio de produção: libera o site e aponta o sitemap canônico.
 * - Nos domínios de homologação (*.wab.com / *.wab.com.br): bloqueia toda
 *   indexação, para evitar conteúdo duplicado do espelho de homologação.
 */
class RobotsController extends AbstractController
{
    private const SITEMAP_URL = 'https://www.pressmatik.com.br/sitemap.xml';

    #[Route('/robots.txt', name: 'app_robots')]
    public function robots(Request $request): Response
    {
        // Fonte unica dos dominios de producao: parametro app.production_hosts
        // (config/services.yaml), tambem usado pelo partial do Google Analytics.
        $productionHosts = (array) $this->getParameter('app.production_hosts');
        $isProduction = in_array(strtolower($request->getHost()), $productionHosts, true);

        if ($isProduction) {
            $body = <<<TXT
                User-agent: *
                Allow: /
                Disallow: /admin/
                Disallow: /login
                Disallow: /logout

                Sitemap: {$this->sitemapUrl()}

                TXT;
        } else {
            // Homologação: não indexar
            $body = <<<TXT
                User-agent: *
                Disallow: /

                TXT;
        }

        $response = new Response($body, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/plain; charset=UTF-8');

        return $response;
    }

    private function sitemapUrl(): string
    {
        return self::SITEMAP_URL;
    }
}
