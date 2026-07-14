<?php

namespace App\Twig;

use App\Repository\BannerRepository;
use App\Repository\DifferentialRepository;
use App\Repository\ProductRepository;
use App\Service\ProductCatalogService;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ProductCatalogService $catalogService,
        private readonly \App\Repository\ServiceRepository $serviceRepo,
        private readonly ProductRepository $productRepo,
        private readonly BannerRepository $bannerRepo,
        private readonly DifferentialRepository $differentialRepo,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('nav_item_class', [$this, 'navItemClass'], ['is_safe' => ['html']]),
            new TwigFunction('get_product_catalog', [$this, 'getProductCatalog']),
            new TwigFunction('get_active_services', [$this, 'getActiveServices']),
            new TwigFunction('get_products_grouped', [$this, 'getProductsGrouped']),
            new TwigFunction('get_active_banners', [$this, 'getActiveBanners']),
            new TwigFunction('get_active_differentials', [$this, 'getActiveDifferentials']),
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

    /**
     * Legacy: returns the hardcoded catalog array.
     * Kept during transition; will be replaced by get_products_grouped().
     */
    public function getProductCatalog(): array
    {
        return $this->catalogService->getCatalog();
    }

    public function getActiveServices(): array
    {
        return $this->serviceRepo->findActiveOrdered();
    }

    /**
     * Returns DB products grouped by category for megamenu.
     * @return array<string, \App\Entity\Product[]>
     */
    public function getProductsGrouped(): array
    {
        return $this->productRepo->findGroupedByCategory();
    }

    /** @return \App\Entity\Banner[] */
    public function getActiveBanners(): array
    {
        return $this->bannerRepo->findActive();
    }

    /** @return \App\Entity\Differential[] */
    public function getActiveDifferentials(): array
    {
        return $this->differentialRepo->findActive();
    }
}
