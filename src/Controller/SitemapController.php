<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use App\Repository\ProductRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Sitemap XML multilíngue (pt/en/es) com alternates hreflang.
 * As URLs são sempre absolutas no domínio canônico de produção,
 * independentemente do host que atende a requisição.
 */
class SitemapController extends AbstractController
{
    private const HOST = 'https://www.pressmatik.com.br';
    private const LOCALES = ['pt', 'en', 'es'];

    public function __construct(
        private readonly ProductRepository $productRepo,
        private readonly ServiceRepository $serviceRepo,
        private readonly NewsRepository $newsRepo,
    ) {
    }

    #[Route('/sitemap.xml', name: 'app_sitemap', defaults: ['_format' => 'xml'])]
    public function sitemap(): Response
    {
        /** @var array<int, array{links: array<string, string>, lastmod: ?string}> $entries */
        $entries = [];

        // Páginas institucionais (mesma rota nas 3 línguas)
        foreach (['app_home', 'app_about_us', 'app_news_list', 'app_privacy_policy', 'app_terms_of_use'] as $route) {
            $links = [];
            foreach (self::LOCALES as $loc) {
                $links[$loc] = $this->path($route, ['_locale' => $loc]);
            }
            $entries[] = ['links' => $links, 'lastmod' => null];
        }

        // Produtos (slug único para as 3 línguas)
        foreach ($this->productRepo->findActive() as $product) {
            $slug = $product->getSlug();
            if (!$slug) {
                continue;
            }
            $links = [];
            foreach (self::LOCALES as $loc) {
                $links[$loc] = $this->path('app_product_detail', ['_locale' => $loc, 'slug' => $slug]);
            }
            $entries[] = ['links' => $links, 'lastmod' => $this->lastmod($product->getUpdatedAt())];
        }

        // Serviços (slug por língua)
        foreach ($this->serviceRepo->findActiveOrdered() as $service) {
            $links = [];
            foreach (self::LOCALES as $loc) {
                $slug = $service->getSlug($loc);
                if (!$slug) {
                    continue;
                }
                $links[$loc] = $this->path('app_service_detail', ['_locale' => $loc, 'slug' => $slug]);
            }
            if ($links) {
                $entries[] = ['links' => $links, 'lastmod' => $this->lastmod($service->getUpdatedAt())];
            }
        }

        // Notícias (slug por língua)
        foreach ($this->newsRepo->findActiveOrdered() as $news) {
            $links = [];
            foreach (self::LOCALES as $loc) {
                $slug = $news->getSlug($loc);
                if (!$slug) {
                    continue;
                }
                $links[$loc] = $this->path('app_news_detail', ['_locale' => $loc, 'slug' => $slug]);
            }
            if ($links) {
                $entries[] = ['links' => $links, 'lastmod' => $this->lastmod($news->getUpdatedAt())];
            }
        }

        $xml = $this->renderXml($entries);

        $response = new Response($xml, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/xml; charset=UTF-8');

        return $response;
    }

    /**
     * @param array<int, array{links: array<string, string>, lastmod: ?string}> $entries
     */
    private function renderXml(array $entries): string
    {
        $lines = [];
        $lines[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        foreach ($entries as $entry) {
            foreach ($entry['links'] as $loc => $path) {
                $lines[] = '  <url>';
                $lines[] = '    <loc>' . $this->esc(self::HOST . $path) . '</loc>';
                foreach ($entry['links'] as $altLoc => $altPath) {
                    $lines[] = sprintf(
                        '    <xhtml:link rel="alternate" hreflang="%s" href="%s"/>',
                        $altLoc,
                        $this->esc(self::HOST . $altPath)
                    );
                }
                // x-default aponta para a versão em português
                if (isset($entry['links']['pt'])) {
                    $lines[] = sprintf(
                        '    <xhtml:link rel="alternate" hreflang="x-default" href="%s"/>',
                        $this->esc(self::HOST . $entry['links']['pt'])
                    );
                }
                if ($entry['lastmod']) {
                    $lines[] = '    <lastmod>' . $entry['lastmod'] . '</lastmod>';
                }
                $lines[] = '  </url>';
            }
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines) . "\n";
    }

    /**
     * @param array<string, mixed> $params
     */
    private function path(string $route, array $params): string
    {
        return $this->generateUrl($route, $params, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    private function lastmod(?\DateTimeInterface $date): ?string
    {
        return $date?->format('Y-m-d');
    }

    private function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
