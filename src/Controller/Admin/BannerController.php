<?php

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Repository\BannerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/banner')]
final class BannerController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BannerRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_banner_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/banner/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_banner_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new Banner();
            $this->handleFormData($item, $request);
            $this->em->persist($item);
            $this->em->flush();
            $this->addFlash('success', 'Banner criado com sucesso!');
            return $this->redirectToRoute('app_admin_banner_index');
        }

        return $this->render('admin/banner/form.html.twig', [
            'item' => null,
            'formTitle' => 'Novo Banner',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_banner_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Banner $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();
            $this->addFlash('success', 'Banner atualizado com sucesso!');
            return $this->redirectToRoute('app_admin_banner_index');
        }

        return $this->render('admin/banner/form.html.twig', [
            'item' => $item,
            'formTitle' => 'Editar Banner',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_banner_delete', methods: ['POST'])]
    public function delete(Request $request, Banner $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Banner removido com sucesso!');
        }
        return $this->redirectToRoute('app_admin_banner_index');
    }

    #[Route('/reorder', name: 'app_admin_banner_reorder', methods: ['POST'])]
    public function reorder(Request $request): JsonResponse
    {
        $ids = json_decode($request->getContent(), true)['ids'] ?? [];
        foreach ($ids as $position => $id) {
            $item = $this->repo->find($id);
            if ($item) { $item->setPosition($position); }
        }
        $this->em->flush();
        return new JsonResponse(['success' => true]);
    }

    private function handleFormData(Banner $item, Request $request): void
    {
        $data = $request->request;
        $item->setTitlePt($data->get('titlePt'));
        $item->setTitleEn($data->get('titleEn'));
        $item->setTitleEs($data->get('titleEs'));
        $item->setSubtitlePt($data->get('subtitlePt'));
        $item->setSubtitleEn($data->get('subtitleEn'));
        $item->setSubtitleEs($data->get('subtitleEs'));
        $item->setButtonTextPt($data->get('buttonTextPt'));
        $item->setButtonTextEn($data->get('buttonTextEn'));
        $item->setButtonTextEs($data->get('buttonTextEs'));
        $item->setButtonUrl($data->get('buttonUrl'));
        $item->setPosition((int) $data->get('position', 0));
        $item->setIsActive((bool) $data->get('isActive', false));

        if ($data->get('deleteImage')) {
            $item->setImageFile(null);
            $item->setImageName(null);
        }

        $imageFile = $request->files->get('imageFile');
        if ($imageFile instanceof UploadedFile) {
            $item->setImageFile($imageFile);
        }
    }
}
