<?php

namespace App\Controller\Admin;

use App\Entity\MegaMenuCategory;
use App\Repository\MegaMenuCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/megamenu')]
final class MegaMenuCategoryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MegaMenuCategoryRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_megamenu_index', methods: ['GET'])]
    public function index(): Response
    {
        $items = $this->repo->findAllOrdered();

        if (empty($items)) {
            $this->seedDefaults();
            $items = $this->repo->findAllOrdered();
        }

        return $this->render('admin/megamenu/index.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/seed-defaults', name: 'app_admin_megamenu_seed', methods: ['GET', 'POST'])]
    public function seed(): Response
    {
        $this->seedDefaults();
        $this->addFlash('success', 'Categorias do MegaMenu inicializadas com sucesso!');
        return $this->redirectToRoute('app_admin_megamenu_index');
    }

    private function seedDefaults(): void
    {
        $defaults = [
            [
                'key' => 'hydraulic',
                'titlePt' => 'Prensas Hidráulicas',
                'titleEn' => 'Hydraulic Presses',
                'titleEs' => 'Prensas Hidráulicas',
                'defaultImg' => '/images/prensa-hidraulica-tipo-c-linha-st.png',
                'position' => 1
            ],
            [
                'key' => 'servo-hydraulic',
                'titlePt' => 'Prensas Servo-Hidráulicas',
                'titleEn' => 'Servo-Hydraulic Presses',
                'titleEs' => 'Prensas Servo-Hidráulicas',
                'defaultImg' => '/images/prensa-servo-hidraulica.png',
                'position' => 2
            ],
            [
                'key' => 'mechanical',
                'titlePt' => 'Prensas Mecânicas',
                'titleEn' => 'Mechanical Presses',
                'titleEs' => 'Prensas Mecánicas',
                'defaultImg' => '/images/prensa-hidraulica-tipo-c-linha-st.png',
                'position' => 3
            ],
            [
                'key' => 'equipments',
                'titlePt' => 'Máquinas e Equipamentos',
                'titleEn' => 'Machinery & Equipment',
                'titleEs' => 'Maquinaria y Equipos',
                'defaultImg' => '/images/tipo-h.png',
                'position' => 4
            ],
            [
                'key' => 'parts',
                'titlePt' => 'Peças e Acessórios',
                'titleEn' => 'Parts & Accessories',
                'titleEs' => 'Piezas y Accesorios',
                'defaultImg' => '/images/especiais.png',
                'position' => 5
            ],
        ];

        foreach ($defaults as $cat) {
            $existing = $this->repo->findOneBy(['categoryKey' => $cat['key']]);
            if (!$existing) {
                $item = new MegaMenuCategory();
                $item->setCategoryKey($cat['key']);
                $item->setTitlePt($cat['titlePt']);
                $item->setTitleEn($cat['titleEn']);
                $item->setTitleEs($cat['titleEs']);
                $item->setDefaultImagePath($cat['defaultImg']);
                $item->setPosition($cat['position']);
                $this->em->persist($item);
            }
        }

        $this->em->flush();
    }

    #[Route('/{id}/edit', name: 'app_admin_megamenu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MegaMenuCategory $item): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request;
            $item->setTitlePt($data->get('titlePt'));
            $item->setTitleEn($data->get('titleEn'));
            $item->setTitleEs($data->get('titleEs'));
            $item->setPosition((int) $data->get('position', 0));

            if ($data->get('deleteImage')) {
                $item->setImageFile(null);
                $item->setImageName(null);
            }

            $imageFile = $request->files->get('imageFile');
            if ($imageFile instanceof UploadedFile) {
                $item->setImageFile($imageFile);
            }

            $this->em->flush();
            $this->addFlash('success', 'Imagem do MegaMenu atualizada com sucesso!');
            return $this->redirectToRoute('app_admin_megamenu_index');
        }

        return $this->render('admin/megamenu/form.html.twig', [
            'item' => $item,
            'formTitle' => 'Editar Imagem do MegaMenu - ' . ($item->getTitlePt() ?: $item->getCategoryKey()),
        ]);
    }
}
