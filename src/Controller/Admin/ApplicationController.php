<?php

namespace App\Controller\Admin;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/application')]
final class ApplicationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ApplicationRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_application_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/application/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_application_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new Application();
            $this->handleFormData($item, $request);
            $this->em->persist($item);
            $this->em->flush();
            $this->addFlash('success', 'Aplicação criada com sucesso!');
            return $this->redirectToRoute('app_admin_application_index');
        }

        return $this->render('admin/application/form.html.twig', [
            'item' => null,
            'formTitle' => 'Nova Aplicação',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_application_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Application $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();
            $this->addFlash('success', 'Aplicação atualizada com sucesso!');
            return $this->redirectToRoute('app_admin_application_index');
        }

        return $this->render('admin/application/form.html.twig', [
            'item' => $item,
            'formTitle' => 'Editar Aplicação',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_application_delete', methods: ['POST'])]
    public function delete(Request $request, Application $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Aplicação removida com sucesso!');
        }
        return $this->redirectToRoute('app_admin_application_index');
    }

    #[Route('/reorder', name: 'app_admin_application_reorder', methods: ['POST'])]
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

    private function handleFormData(Application $item, Request $request): void
    {
        $data = $request->request;
        $item->setNamePt($data->get('namePt'));
        $item->setNameEn($data->get('nameEn'));
        $item->setNameEs($data->get('nameEs'));
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
