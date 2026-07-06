<?php

namespace App\Twig;

use App\Service\ProductCatalogService;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private RequestStack $requestStack;
    private ProductCatalogService $catalogService;
    private \App\Repository\ServiceRepository $serviceRepo;

    public function __construct(RequestStack $requestStack, ProductCatalogService $catalogService, \App\Repository\ServiceRepository $serviceRepo)
    {
        $this->requestStack = $requestStack;
        $this->catalogService = $catalogService;
        $this->serviceRepo = $serviceRepo;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('nav_item_class', [$this, 'navItemClass'], ['is_safe' => ['html']]),
            new TwigFunction('get_product_catalog', [$this, 'getProductCatalog']),
            new TwigFunction('get_active_services', [$this, 'getActiveServices']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('role_label', [$this, 'roleLabel']),
        ];
    }

    public function navItemClass(string $routePrefix): string
    {
        $currentRouteName = $this->requestStack->getCurrentRequest()?->attributes->get('_route') ?? '';
        if (strpos($currentRouteName, $routePrefix) !== false) {
            return 'class="nav-link nav-link--active" aria-current="page"';
        }
        return 'class="nav-link" ';
    }

    public function roleLabel(string $role): string
    {
        return match ($role) {
            'ROLE_ADMIN' => 'Administrador',
            'ROLE_USER'  => 'Usuário',
            default      => ucfirst(strtolower(str_replace(['ROLE_', '_'], ['', ' '], $role))),
        };
    }

    public function getProductCatalog(): array
    {
        return $this->catalogService->getCatalog();
    }

    public function getActiveServices(): array
    {
        return $this->serviceRepo->findActiveOrdered();
    }
}
