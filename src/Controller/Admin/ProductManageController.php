<?php

namespace App\Controller\Admin;

use App\Entity\ProductSize;
use App\Entity\ProductSpecValue;
use App\Entity\ProductConfigItem;
use App\Entity\ProductVideo;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductSpecValueRepository;
use App\Repository\ProductConfigItemRepository;
use App\Repository\ProductVideoRepository;
use App\Repository\TechnicalSpecificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/product-manage')]
final class ProductManageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProductSizeRepository $sizeRepo,
        private readonly ProductSpecValueRepository $specValueRepo,
        private readonly ProductConfigItemRepository $configRepo,
        private readonly ProductVideoRepository $videoRepo,
        private readonly TechnicalSpecificationRepository $techSpecRepo,
    ) {}

    // ─── Index: list of products to manage ──────────────────────────────────
    #[Route('', name: 'app_admin_product_manage_index', methods: ['GET'])]
    public function index(): Response
    {
        // For now, hardcode the product slugs from translation files
        $products = [
            ['slug' => 'prensas-hidraulicas-tipo-c', 'name' => 'Prensas Hidráulicas Tipo C'],
            ['slug' => 'prensas-hidraulicas-tipo-h', 'name' => 'Prensas Hidráulicas Tipo H'],
            ['slug' => 'prensas-mecanicas-tipo-c', 'name' => 'Prensas Mecânicas Tipo C'],
            ['slug' => 'prensas-mecanicas-tipo-h', 'name' => 'Prensas Mecânicas Tipo H'],
            ['slug' => 'prensas-pneumaticas', 'name' => 'Prensas Pneumáticas'],
            ['slug' => 'alimentadores', 'name' => 'Alimentadores e Desbobinadores'],
        ];

        return $this->render('admin/product/manage_index.html.twig', [
            'products' => $products,
        ]);
    }

    // ─── Product Detail ─────────────────────────────────────────────────────
    #[Route('/{slug}', name: 'app_admin_product_manage_detail', methods: ['GET'])]
    public function detail(string $slug): Response
    {
        return $this->render('admin/product/manage_detail.html.twig', [
            'slug' => $slug,
            'sizes' => $this->sizeRepo->findBySlugOrdered($slug),
            'specValues' => $this->specValueRepo->findBySlugOrdered($slug),
            'standardItems' => $this->configRepo->findBySlugAndType($slug, 'standard'),
            'optionalItems' => $this->configRepo->findBySlugAndType($slug, 'optional'),
            'videos' => $this->videoRepo->findBySlugOrdered($slug),
            'allSpecs' => $this->techSpecRepo->findAllOrdered(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  SIZES
    // ═══════════════════════════════════════════════════════════════════════

    #[Route('/{slug}/size/add', name: 'app_admin_product_size_add', methods: ['POST'])]
    public function addSize(Request $request, string $slug): Response
    {
        $size = new ProductSize();
        $size->setProductSlug($slug);
        $size->setName($request->request->get('name'));
        $size->setHasVType($request->request->getBoolean('hasVType', true));
        $size->setHasHType($request->request->getBoolean('hasHType', true));
        $size->setPosition(count($this->sizeRepo->findBySlugOrdered($slug)));

        $this->em->persist($size);
        $this->em->flush();

        $this->addFlash('success', "Tamanho \"{$size->getName()}\" adicionado!");
        return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $slug]);
    }

    #[Route('/{slug}/size/{id}/delete', name: 'app_admin_product_size_delete', methods: ['POST'])]
    public function deleteSize(Request $request, string $slug, ProductSize $size): Response
    {
        if ($this->isCsrfTokenValid('delete_size' . $size->getId(), $request->request->get('_token'))) {
            $this->em->remove($size);
            $this->em->flush();
            $this->addFlash('success', 'Tamanho removido!');
        }
        return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $slug]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  SPEC VALUES (the big table)
    // ═══════════════════════════════════════════════════════════════════════

    #[Route('/{slug}/specs/save', name: 'app_admin_product_specs_save', methods: ['POST'])]
    public function saveSpecs(Request $request, string $slug): Response
    {
        $data = json_decode($request->getContent(), true);
        $rows = $data['rows'] ?? [];

        // Remove existing values for this slug
        $existing = $this->specValueRepo->findBySlugOrdered($slug);
        foreach ($existing as $val) {
            $this->em->remove($val);
        }
        $this->em->flush();

        // Re-create from submitted data
        foreach ($rows as $position => $row) {
            $specId = $row['specId'] ?? null;
            $spec = $specId ? $this->techSpecRepo->find($specId) : null;
            if (!$spec) continue;

            foreach ($row['values'] as $sizeId => $typeValues) {
                $size = $this->sizeRepo->find($sizeId);
                if (!$size) continue;

                $val = new ProductSpecValue();
                $val->setProductSlug($slug);
                $val->setSpecification($spec);
                $val->setProductSize($size);
                $val->setVTypeValue($typeValues['v'] ?? null);
                $val->setHTypeValue($typeValues['h'] ?? null);
                $val->setPosition($position);

                $this->em->persist($val);
            }
        }

        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  CONFIG ITEMS (Standard / Optional)
    // ═══════════════════════════════════════════════════════════════════════

    #[Route('/{slug}/config/add', name: 'app_admin_product_config_add', methods: ['POST'])]
    public function addConfig(Request $request, string $slug): Response
    {
        $item = new ProductConfigItem();
        $item->setProductSlug($slug);
        $item->setType($request->request->get('type', 'standard'));
        $item->setNamePt($request->request->get('namePt'));
        $item->setNameEn($request->request->get('nameEn'));
        $item->setNameEs($request->request->get('nameEs'));
        $item->setPosition((int) $request->request->get('position', 0));

        $this->em->persist($item);
        $this->em->flush();

        $this->addFlash('success', 'Item de configuração adicionado!');
        return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $slug]);
    }

    #[Route('/{slug}/config/{id}/delete', name: 'app_admin_product_config_delete', methods: ['POST'])]
    public function deleteConfig(Request $request, string $slug, ProductConfigItem $item): Response
    {
        if ($this->isCsrfTokenValid('delete_config' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Item removido!');
        }
        return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $slug]);
    }

    #[Route('/{slug}/config/reorder', name: 'app_admin_product_config_reorder', methods: ['POST'])]
    public function reorderConfig(Request $request, string $slug): JsonResponse
    {
        $ids = json_decode($request->getContent(), true)['ids'] ?? [];
        foreach ($ids as $pos => $id) {
            $item = $this->configRepo->find($id);
            if ($item) $item->setPosition($pos);
        }
        $this->em->flush();
        return new JsonResponse(['success' => true]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  VIDEOS
    // ═══════════════════════════════════════════════════════════════════════

    #[Route('/{slug}/video/add', name: 'app_admin_product_video_add', methods: ['POST'])]
    public function addVideo(Request $request, string $slug): Response
    {
        $video = new ProductVideo();
        $video->setProductSlug($slug);
        $video->setTitlePt($request->request->get('titlePt'));
        $video->setTitleEn($request->request->get('titleEn'));
        $video->setTitleEs($request->request->get('titleEs'));
        $video->setUrl($request->request->get('url'));
        $video->setPosition(count($this->videoRepo->findBySlugOrdered($slug)));

        $this->em->persist($video);
        $this->em->flush();

        $this->addFlash('success', 'Vídeo adicionado!');
        return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $slug]);
    }

    #[Route('/{slug}/video/{id}/delete', name: 'app_admin_product_video_delete', methods: ['POST'])]
    public function deleteVideo(Request $request, string $slug, ProductVideo $video): Response
    {
        if ($this->isCsrfTokenValid('delete_video' . $video->getId(), $request->request->get('_token'))) {
            $this->em->remove($video);
            $this->em->flush();
            $this->addFlash('success', 'Vídeo removido!');
        }
        return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $slug]);
    }
}
