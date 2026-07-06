<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TechnicalSpecificationRepository;
use App\Repository\HistoryTimelineRepository;
use App\Repository\ProductVideoRepository;
use App\Service\ProductCatalogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class DashController extends AbstractController
{
    #[Route('/', name: 'admin_dash')]
    public function dashboard(
        UserRepository $userRepository,
        ProductSizeRepository $sizeRepository,
        TechnicalSpecificationRepository $specRepository,
        HistoryTimelineRepository $historyRepository,
        ProductVideoRepository $videoRepository,
        ProductCatalogService $catalogService
    ): Response {
        $catalog = $catalogService->getCatalog();
        $groupedCatalog = [
            'hydraulic' => [
                'name' => 'Prensas Hidráulicas',
                'products' => [],
            ],
            'servo-hydraulic' => [
                'name' => 'Prensas Servo-Hidráulicas',
                'products' => [],
            ],
            'mechanical' => [
                'name' => 'Prensas Mecânicas',
                'products' => [],
            ],
            'peripherals' => [
                'name' => 'Periféricos e Outros',
                'products' => [],
            ],
        ];

        foreach ($catalog as $slug => $prod) {
            $cat = $prod['category'] ?? 'peripherals';
            if (!isset($groupedCatalog[$cat])) {
                $groupedCatalog[$cat] = [
                    'name' => ucfirst($cat),
                    'products' => [],
                ];
            }
            $groupedCatalog[$cat]['products'][] = [
                'slug' => $slug,
                'name_key' => $prod['name_key'] ?? $slug,
            ];
        }

        return $this->render('admin/dash/dashboard.html.twig', [
            'catalog' => $groupedCatalog,
            'stats' => [
                'users_total' => $userRepository->count([]),
                'users_admin' => count(array_filter(
                    $userRepository->findAll(),
                    fn($u) => in_array('ROLE_ADMIN', $u->getRoles())
                )),
                'products_total' => count($catalog),
                'specs_total' => $specRepository->count([]),
                'sizes_total' => $sizeRepository->count([]),
                'timeline_total' => $historyRepository->count([]),
                'videos_total' => $videoRepository->count([]),
            ],
        ]);
    }
}
