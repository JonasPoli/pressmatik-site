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
        $size->setHasVType($request->request->getBoolean('hasVType', false));
        $size->setHasHType($request->request->getBoolean('hasHType', false));
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
    //  SPEC VALUES (Subproduct level) — Safe upsert pattern
    // ═══════════════════════════════════════════════════════════════════════

    #[Route('/subproduct/{subproductId}/specs/save', name: 'app_admin_subproduct_specs_save', methods: ['POST'])]
    public function saveSubproductSpecs(Request $request, int $subproductId): Response
    {
        $subproduct = $this->em->getRepository(Subproduct::class)->find($subproductId);
        if (!$subproduct) {
            return new JsonResponse(['success' => false, 'message' => 'Subproduto não encontrado.'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data) || !isset($data['rows'])) {
            return new JsonResponse(['success' => false, 'message' => 'Dados inválidos recebidos.'], 400);
        }

        $rows = $data['rows'];

        // ─── Guard: refuse to save completely empty data when specs already exist ───
        $existing = $this->specValueRepo->findBySubproductOrdered($subproductId);
        $validRows = array_filter($rows, fn($r) => !empty($r['specId']));

        if (count($validRows) === 0 && count($existing) > 0) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Operação bloqueada: não é possível apagar todos os parâmetros de uma vez. Se deseja limpar todos os dados, remova os tamanhos individualmente.'
            ], 422);
        }

        // ─── Build a set of expected (specId, sizeId) keys from submitted data ───
        $submittedKeys = [];
        foreach ($validRows as $position => $row) {
            $specId = (int) $row['specId'];
            foreach (($row['values'] ?? []) as $sizeId => $typeValues) {
                $submittedKeys[$specId . '_' . $sizeId] = [
                    'specId' => $specId,
                    'sizeId' => (int) $sizeId,
                    'v' => $typeValues['v'] ?? null,
                    'h' => $typeValues['h'] ?? null,
                    'position' => (int) $position,
                ];
            }
        }

        // ─── Index existing records for diff ───
        $existingByKey = [];
        foreach ($existing as $val) {
            $key = $val->getSpecification()->getId() . '_' . $val->getProductSize()->getId();
            $existingByKey[$key] = $val;
        }

        // ─── Delete records no longer in submitted data ───
        foreach ($existingByKey as $key => $val) {
            if (!isset($submittedKeys[$key])) {
                $this->em->remove($val);
            }
        }

        // ─── Upsert: update existing or create new ───
        foreach ($submittedKeys as $key => $data) {
            $spec = $this->techSpecRepo->find($data['specId']);
            $size = $this->sizeRepo->find($data['sizeId']);
            if (!$spec || !$size) continue;

            if (isset($existingByKey[$key])) {
                // Update
                $val = $existingByKey[$key];
                $val->setVTypeValue($data['v']);
                $val->setHTypeValue($data['h']);
                $val->setPosition($data['position']);
            } else {
                // Insert
                $val = new ProductSpecValue();
                $val->setSubproduct($subproduct);
                $val->setSpecification($spec);
                $val->setProductSize($size);
                $val->setVTypeValue($data['v']);
                $val->setHTypeValue($data['h']);
                $val->setPosition($data['position']);
                $this->em->persist($val);
            }
        }

        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  CSV EXPORT / IMPORT (Subproduct specs)
    // ═══════════════════════════════════════════════════════════════════════

    #[Route('/subproduct/{subproductId}/specs/export.csv', name: 'app_admin_subproduct_specs_export_csv', methods: ['GET'])]
    public function exportSubproductSpecsCsv(int $subproductId): Response
    {
        $subproduct = $this->em->getRepository(Subproduct::class)->find($subproductId);
        if (!$subproduct) {
            throw $this->createNotFoundException('Subproduto não encontrado');
        }

        $sizes = $this->sizeRepo->findBySubproductOrdered($subproductId);
        $specValues = $this->specValueRepo->findBySubproductOrdered($subproductId);

        // Build header columns
        $headers = ['Especificação', 'Unidade'];
        foreach ($sizes as $size) {
            if ($size->isHasVType() && $size->isHasHType()) {
                $headers[] = $size->getName() . ' (V)';
                $headers[] = $size->getName() . ' (H)';
            } elseif ($size->isHasVType()) {
                $headers[] = $size->getName() . ' (V)';
            } elseif ($size->isHasHType()) {
                $headers[] = $size->getName() . ' (H)';
            } else {
                $headers[] = $size->getName();
            }
        }

        // Group spec values by position
        $groupedByPosition = [];
        foreach ($specValues as $val) {
            $pos = $val->getPosition();
            if (!isset($groupedByPosition[$pos])) {
                $groupedByPosition[$pos] = [
                    'specification' => $val->getSpecification(),
                    'values' => [],
                ];
            }
            $groupedByPosition[$pos]['values'][$val->getProductSize()->getId()] = [
                'v' => $val->getVTypeValue() ?? '',
                'h' => $val->getHTypeValue() ?? '',
            ];
        }
        ksort($groupedByPosition);

        // Build CSV
        $output = fopen('php://temp', 'r+');
        // BOM for UTF-8
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, $headers, ';');

        foreach ($groupedByPosition as $row) {
            $spec = $row['specification'];
            $line = [$spec->getNamePt(), $spec->getUnitPt() ?? ''];
            foreach ($sizes as $size) {
                $vals = $row['values'][$size->getId()] ?? ['v' => '', 'h' => ''];
                if ($size->isHasVType() && $size->isHasHType()) {
                    $line[] = $vals['v'];
                    $line[] = $vals['h'];
                } elseif ($size->isHasVType()) {
                    $line[] = $vals['v'];
                } elseif ($size->isHasHType()) {
                    $line[] = $vals['h'];
                } else {
                    $line[] = $vals['v'];
                }
            }
            fputcsv($output, $line, ';');
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        $filename = 'parametros_' . $subproduct->getModel() . '_' . date('Y-m-d') . '.csv';

        return new Response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    #[Route('/subproduct/{subproductId}/specs/import', name: 'app_admin_subproduct_specs_import_csv', methods: ['POST'])]
    public function importSubproductSpecsCsv(Request $request, int $subproductId): Response
    {
        $subproduct = $this->em->getRepository(Subproduct::class)->find($subproductId);
        if (!$subproduct) {
            $this->addFlash('danger', 'Subproduto não encontrado.');
            return $this->redirectToRoute('app_admin_product_manage_index');
        }

        /** @var UploadedFile|null $file */
        $file = $request->files->get('csvFile');
        if (!$file || !$file->isValid()) {
            $this->addFlash('danger', 'Arquivo CSV inválido ou não enviado.');
            return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $subproduct->getProduct()->getSlug()]);
        }

        $sizes = $this->sizeRepo->findBySubproductOrdered($subproductId);
        if (count($sizes) === 0) {
            $this->addFlash('danger', 'Cadastre pelo menos um tamanho antes de importar.');
            return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $subproduct->getProduct()->getSlug()]);
        }

        // Build column mapping: ordered list of (sizeId, type) matching header order
        $columnMapping = [];
        foreach ($sizes as $size) {
            if ($size->isHasVType() && $size->isHasHType()) {
                $columnMapping[] = ['sizeId' => $size->getId(), 'type' => 'v'];
                $columnMapping[] = ['sizeId' => $size->getId(), 'type' => 'h'];
            } elseif ($size->isHasVType()) {
                $columnMapping[] = ['sizeId' => $size->getId(), 'type' => 'v'];
            } elseif ($size->isHasHType()) {
                $columnMapping[] = ['sizeId' => $size->getId(), 'type' => 'h'];
            } else {
                $columnMapping[] = ['sizeId' => $size->getId(), 'type' => 'v'];
            }
        }

        // Read CSV
        $content = file_get_contents($file->getPathname());
        // Remove BOM if present
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $lines = array_filter(explode("\n", $content), fn($l) => trim($l) !== '');

        if (count($lines) < 2) {
            $this->addFlash('danger', 'Arquivo CSV vazio ou sem dados.');
            return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $subproduct->getProduct()->getSlug()]);
        }

        // Skip header row
        array_shift($lines);

        // Index existing spec values
        $existing = $this->specValueRepo->findBySubproductOrdered($subproductId);
        $existingByKey = [];
        foreach ($existing as $val) {
            $key = $val->getSpecification()->getId() . '_' . $val->getProductSize()->getId();
            $existingByKey[$key] = $val;
        }

        // Build specs lookup by name
        $allSpecs = $this->techSpecRepo->findAllOrdered();
        $specsByName = [];
        foreach ($allSpecs as $spec) {
            $specsByName[mb_strtolower(trim($spec->getNamePt()))] = $spec;
        }

        $sizesById = [];
        foreach ($sizes as $size) {
            $sizesById[$size->getId()] = $size;
        }

        $importedCount = 0;
        foreach ($lines as $position => $line) {
            $cols = str_getcsv($line, ';');
            if (count($cols) < 2) continue;

            $specName = trim($cols[0]);
            $unitName = trim($cols[1] ?? '');

            if (empty($specName)) continue;

            // Find or create spec
            $specKey = mb_strtolower($specName);
            $spec = $specsByName[$specKey] ?? null;
            if (!$spec) {
                $spec = new \App\Entity\TechnicalSpecification();
                $spec->setNamePt($specName);
                $spec->setUnitPt($unitName);
                $spec->setPosition(count($allSpecs) + $importedCount);
                $this->em->persist($spec);
                $specsByName[$specKey] = $spec;
            }

            // Parse value columns
            $dataColumns = array_slice($cols, 2);
            foreach ($columnMapping as $colIdx => $map) {
                $cellValue = isset($dataColumns[$colIdx]) ? trim($dataColumns[$colIdx]) : '';
                $sizeId = $map['sizeId'];
                $type = $map['type'];

                // Force flush to get spec ID if it was just created
                if ($spec->getId() === null) {
                    $this->em->flush();
                }

                $key = $spec->getId() . '_' . $sizeId;

                if (isset($existingByKey[$key])) {
                    $val = $existingByKey[$key];
                    if ($type === 'v') {
                        $val->setVTypeValue($cellValue ?: null);
                    } else {
                        $val->setHTypeValue($cellValue ?: null);
                    }
                    $val->setPosition($position);
                } else {
                    $val = new ProductSpecValue();
                    $val->setSubproduct($subproduct);
                    $val->setSpecification($spec);
                    $val->setProductSize($sizesById[$sizeId]);
                    if ($type === 'v') {
                        $val->setVTypeValue($cellValue ?: null);
                    } else {
                        $val->setHTypeValue($cellValue ?: null);
                    }
                    $val->setPosition($position);
                    $this->em->persist($val);
                    $existingByKey[$key] = $val;
                }
            }
            $importedCount++;
        }

        $this->em->flush();

        $this->addFlash('success', "CSV importado com sucesso! {$importedCount} linhas processadas.");
        return $this->redirectToRoute('app_admin_product_manage_detail', ['slug' => $subproduct->getProduct()->getSlug()]);
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
