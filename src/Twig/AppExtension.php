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
        private readonly \App\Repository\MegaMenuCategoryRepository $megaMenuRepo,
        private readonly \App\Repository\ServiceHeaderRepository $serviceHeaderRepo,
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
            new TwigFunction('get_megamenu_categories', [$this, 'getMegaMenuCategories']),
            new TwigFunction('get_service_video_url', [$this, 'getServiceVideoUrl']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('role_label', [$this, 'roleLabel']),
            new TwigFilter('webp', [$this, 'toWebp']),
        ];
    }

    public function toWebp(?string $path): string
    {
        if (!$path) {
            return '';
        }
        
        $webpPath = (string) preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $path);
        
        // If absolute path from root domain or relative to public
        $cleanPath = '/' . ltrim($webpPath, '/');
        $projectDir = dirname(__DIR__, 2);
        $realWebpPath = $projectDir . '/public' . $cleanPath;
        
        if (file_exists($realWebpPath)) {
            return $webpPath;
        }
        
        return $path;
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

    /** @return array<string, \App\Entity\MegaMenuCategory> */
    public function getMegaMenuCategories(): array
    {
        $items = $this->megaMenuRepo->findAllOrdered();
        $indexed = [];
        foreach ($items as $item) {
            $indexed[$item->getCategoryKey()] = $item;
        }
        return $indexed;
    }

    public function getServiceVideoUrl(): string
    {
        return $this->serviceHeaderRepo->findOrCreate()->getActiveVideoUrl();
    }
}
