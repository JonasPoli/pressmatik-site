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
        return $this->render('admin/megamenu/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
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
