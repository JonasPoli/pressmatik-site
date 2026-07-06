<?php

namespace App\Controller\Admin;

use App\Entity\HistoryTimeline;
use App\Repository\HistoryTimelineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/history')]
final class HistoryTimelineController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HistoryTimelineRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_history_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/history/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_history_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new HistoryTimeline();
            $this->handleFormData($item, $request);

            $this->em->persist($item);
            $this->em->flush();

            $this->addFlash('success', 'Item da linha do tempo criado com sucesso!');
            return $this->redirectToRoute('app_admin_history_index');
        }

        return $this->render('admin/history/form.html.twig', [
            'item' => null,
            'formTitle' => 'Novo Item',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_history_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HistoryTimeline $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();

            $this->addFlash('success', 'Item atualizado com sucesso!');
            return $this->redirectToRoute('app_admin_history_index');
        }

        return $this->render('admin/history/form.html.twig', [
            'item' => $item,
            'formTitle' => 'Editar Item',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_history_delete', methods: ['POST'])]
    public function delete(Request $request, HistoryTimeline $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Item removido com sucesso!');
        }

        return $this->redirectToRoute('app_admin_history_index');
    }

    #[Route('/reorder', name: 'app_admin_history_reorder', methods: ['POST'])]
    public function reorder(Request $request): JsonResponse
    {
        $ids = json_decode($request->getContent(), true)['ids'] ?? [];

        foreach ($ids as $position => $id) {
            $item = $this->repo->find($id);
            if ($item) {
                $item->setPosition($position);
            }
        }
        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }

    private function handleFormData(HistoryTimeline $item, Request $request): void
    {
        $data = $request->request;

        $item->setTitlePt($data->get('titlePt'));
        $item->setTitleEn($data->get('titleEn'));
        $item->setTitleEs($data->get('titleEs'));
        $item->setDescriptionPt($data->get('descriptionPt'));
        $item->setDescriptionEn($data->get('descriptionEn'));
        $item->setDescriptionEs($data->get('descriptionEs'));
        $item->setPosition((int) $data->get('position', 0));

        $dateStr = $data->get('eventDate');
        if ($dateStr) {
            $item->setEventDate(new \DateTime($dateStr));
        }

        $imageFile = $request->files->get('imageFile');
        if ($imageFile instanceof UploadedFile) {
            $item->setImageFile($imageFile);
        }
    }
}
