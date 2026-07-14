<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Subproduct;
use App\Entity\Application;
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
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        $products = $this->em->getRepository(Product::class)->findBy([], ['position' => 'ASC']);

        return $this->render('admin/product/manage_index.html.twig', [
            'products' => $products,
        ]);
    }

    // ─── Product Detail ─────────────────────────────────────────────────────
    #[Route('/{slug}', name: 'app_admin_product_manage_detail', methods: ['GET'])]
    public function detail(string $slug): Response
    {
        $product = $this->em->getRepository(Product::class)->findOneBy(['slug' => $slug]);

        return $this->render('admin/product/manage_detail.html.twig', [
            'slug' => $slug,
            'product' => $product,
            'standardItems' => $this->configRepo->findBySlugAndType($slug, 'standard'),
            'optionalItems' => $this->configRepo->findBySlugAndType($slug, 'optional'),
            'videos' => $this->videoRepo->findBySlugOrdered($slug),
            'allSpecs' => $this->techSpecRepo->findAllOrdered(),
            'allApplications' => $this->em->getRepository(Application::class)->findAllOrdered(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  SIZES (Subproduct level)
    // ═══════════════════════════════════════════════════════════════════════

    #[Route('/subproduct/{subproductId}/size/add', name: 'app_admin_subproduct_size_add', methods: ['POST'])]
    public function addSubproductSize(Request $request, int $subproductId): Response
    {
        $subproduct = $this->em->getRepository(Subproduct::class)->find($subproductId);
        if (!$subproduct) {
            throw $this->createNotFoundException('Subproduto não encontrado');
        }

        $size = new ProductSize();
        $size->setSubproduct($subproduct);
        $size->setName($request->request->get('name'));
        $size->setHasVType($request->request->getBoolean('hasVType', true));
        $size->setHasHType($request->request->getBoolean('hasHType', true));
        $size->setPosition(count($subproduct->getSizes()));

        $this->em->persist($size);
        $this->em->flush();

        $this->addFlash('success', "Tamanho \"{$size->getName()}\" adicionado ao subproduto!");
        return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $subproduct->getProduct()->getSlug()]);
    }

    #[Route('/subproduct/{subproductId}/size/{id}/delete', name: 'app_admin_subproduct_size_delete', methods: ['POST'])]
    public function deleteSubproductSize(Request $request, int $subproductId, ProductSize $size): Response
    {
        $subproduct = $this->em->getRepository(Subproduct::class)->find($subproductId);
        if (!$subproduct) {
            throw $this->createNotFoundException('Subproduto não encontrado');
        }

        if ($this->isCsrfTokenValid('delete_size' . $size->getId(), $request->request->get('_token'))) {
            $this->em->remove($size);
            $this->em->flush();
            $this->addFlash('success', 'Tamanho removido!');
        }
        return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $subproduct->getProduct()->getSlug()]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  SPEC VALUES (Subproduct level)
    // ═══════════════════════════════════════════════════════════════════════

    #[Route('/subproduct/{subproductId}/specs/save', name: 'app_admin_subproduct_specs_save', methods: ['POST'])]
    public function saveSubproductSpecs(Request $request, int $subproductId): Response
    {
        $subproduct = $this->em->getRepository(Subproduct::class)->find($subproductId);
        if (!$subproduct) {
            return new JsonResponse(['success' => false, 'message' => 'Subproduct not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $rows = $data['rows'] ?? [];

        // Clear existing values for this subproduct
        $existing = $this->specValueRepo->findBySubproductOrdered($subproductId);
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
                $val->setSubproduct($subproduct);
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



    // ═══════════════════════════════════════════════════════════════════════
    //  SUBPRODUCTS (Basic CRUD & PDF Catalog Uploads)
    // ═══════════════════════════════════════════════════════════════════════

    #[Route('/{slug}/subproduct/add', name: 'app_admin_subproduct_add', methods: ['POST'])]
    public function addSubproduct(Request $request, string $slug): Response
    {
        $product = $this->em->getRepository(Product::class)->findOneBy(['slug' => $slug]);
        if (!$product) {
            throw $this->createNotFoundException('Produto não encontrado');
        }

        $sub = new Subproduct();
        $sub->setProduct($product);
        $sub->setModel($request->request->get('model'));
        $sub->setNamePt($request->request->get('namePt'));
        $sub->setNameEn($request->request->get('nameEn'));
        $sub->setNameEs($request->request->get('nameEs'));
        $sub->setDescriptionPt($request->request->get('descriptionPt'));
        $sub->setDescriptionEn($request->request->get('descriptionEn'));
        $sub->setDescriptionEs($request->request->get('descriptionEs'));
        $sub->setTag($request->request->get('tag'));
        $sub->setIsActive(true);
        $sub->setPosition(count($product->getSubproducts()));

        $imageFile = $request->files->get('imageFile');
        if ($imageFile instanceof UploadedFile) {
            $sub->setImageFile($imageFile);
        }

        $pdfFilePt = $request->files->get('pdfFilePt');
        if ($pdfFilePt instanceof UploadedFile) {
            $sub->setPdfFilePt($pdfFilePt);
        }
        $pdfFileEn = $request->files->get('pdfFileEn');
        if ($pdfFileEn instanceof UploadedFile) {
            $sub->setPdfFileEn($pdfFileEn);
        }
        $pdfFileEs = $request->files->get('pdfFileEs');
        if ($pdfFileEs instanceof UploadedFile) {
            $sub->setPdfFileEs($pdfFileEs);
        }

        // ManyToMany applications association
        $appIds = $request->request->all('applications') ?? [];
        foreach ($appIds as $appId) {
            $app = $this->em->getRepository(Application::class)->find($appId);
            if ($app) {
                $sub->addApplication($app);
            }
        }

        $this->em->persist($sub);
        $this->em->flush();

        $this->addFlash('success', 'Subproduto "' . $sub->getModel() . '" adicionado!');
        return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $slug]);
    }

    #[Route('/{slug}/subproduct/{id}/edit', name: 'app_admin_subproduct_edit', methods: ['POST'])]
    public function editSubproduct(Request $request, string $slug, Subproduct $subproduct): Response
    {
        $data = $request->request;
        $subproduct->setModel($data->get('model'));
        $subproduct->setNamePt($data->get('namePt'));
        $subproduct->setNameEn($data->get('nameEn'));
        $subproduct->setNameEs($data->get('nameEs'));
        $subproduct->setDescriptionPt($data->get('descriptionPt'));
        $subproduct->setDescriptionEn($data->get('descriptionEn'));
        $subproduct->setDescriptionEs($data->get('descriptionEs'));
        $subproduct->setTag($data->get('tag'));
        $subproduct->setIsActive((bool) $data->get('isActive', true));

        $imageFile = $request->files->get('imageFile');
        if ($imageFile instanceof UploadedFile) {
            $subproduct->setImageFile($imageFile);
        }

        $pdfFilePt = $request->files->get('pdfFilePt');
        if ($pdfFilePt instanceof UploadedFile) {
            $subproduct->setPdfFilePt($pdfFilePt);
        }

        $pdfFileEn = $request->files->get('pdfFileEn');
        if ($pdfFileEn instanceof UploadedFile) {
            $subproduct->setPdfFileEn($pdfFileEn);
        }

        $pdfFileEs = $request->files->get('pdfFileEs');
        if ($pdfFileEs instanceof UploadedFile) {
            $subproduct->setPdfFileEs($pdfFileEs);
        }

        // ManyToMany applications sync
        $subproduct->getApplications()->clear();
        $appIds = $request->request->all('applications') ?? [];
        foreach ($appIds as $appId) {
            $app = $this->em->getRepository(Application::class)->find($appId);
            if ($app) {
                $subproduct->addApplication($app);
            }
        }

        $this->em->flush();

        $this->addFlash('success', 'Subproduto "' . $subproduct->getModel() . '" atualizado com sucesso!');
        return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $slug]);
    }

    #[Route('/{slug}/subproduct/{id}/delete', name: 'app_admin_subproduct_delete', methods: ['POST'])]
    public function deleteSubproduct(Request $request, string $slug, Subproduct $subproduct): Response
    {
        if ($this->isCsrfTokenValid('delete_sub' . $subproduct->getId(), $request->request->get('_token'))) {
            $this->em->remove($subproduct);
            $this->em->flush();
            $this->addFlash('success', 'Subproduto removido!');
        }
        return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $slug]);
    }

    #[Route('/{slug}/subproduct/reorder', name: 'app_admin_subproduct_reorder', methods: ['POST'])]
    public function reorderSubproducts(Request $request, string $slug): JsonResponse
    {
        $ids = json_decode($request->getContent(), true)['ids'] ?? [];
        $repo = $this->em->getRepository(Subproduct::class);
        foreach ($ids as $pos => $id) {
            $sub = $repo->find($id);
            if ($sub) {
                $sub->setPosition($pos);
            }
        }
        $this->em->flush();
        return new JsonResponse(['success' => true]);
    }
}
