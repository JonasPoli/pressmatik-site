<?php

namespace App\Controller\Admin;

use App\Entity\QualityCertification;
use App\Repository\QualityCertificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/quality')]
final class QualityCertificationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly QualityCertificationRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_quality_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/quality/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_quality_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new QualityCertification();
            $this->handleFormData($item, $request);
            $this->em->persist($item);
            $this->em->flush();
            $this->addFlash('success', 'Certificação adicionada!');
            return $this->redirectToRoute('app_admin_quality_index');
        }
        return $this->render('admin/quality/form.html.twig', ['item' => null, 'formTitle' => 'Nova Certificação']);
    }

    #[Route('/{id}/edit', name: 'app_admin_quality_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, QualityCertification $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();
            $this->addFlash('success', 'Certificação atualizada!');
            return $this->redirectToRoute('app_admin_quality_index');
        }
        return $this->render('admin/quality/form.html.twig', ['item' => $item, 'formTitle' => 'Editar Certificação']);
    }

    #[Route('/{id}/delete', name: 'app_admin_quality_delete', methods: ['POST'])]
    public function delete(Request $request, QualityCertification $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Certificação removida!');
        }
        return $this->redirectToRoute('app_admin_quality_index');
    }

    #[Route('/reorder', name: 'app_admin_quality_reorder', methods: ['POST'])]
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

    private function handleFormData(QualityCertification $item, Request $request): void
    {
        $data = $request->request;
        $item->setTitlePt($data->get('titlePt'));
        $item->setTitleEn($data->get('titleEn'));
        $item->setTitleEs($data->get('titleEs'));
        $item->setDescriptionPt($data->get('descriptionPt'));
        $item->setDescriptionEn($data->get('descriptionEn'));
        $item->setDescriptionEs($data->get('descriptionEs'));
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
