<?php

namespace App\Controller\Admin;

use App\Entity\StandardItem;
use App\Repository\StandardItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/standard-item')]
final class StandardItemController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly StandardItemRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_standard_item_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/standard_item/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_standard_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new StandardItem();
            $this->handleFormData($item, $request);
            $this->em->persist($item);
            $this->em->flush();
            $this->addFlash('success', 'Item de Série criado com sucesso!');
            return $this->redirectToRoute('app_admin_standard_item_index');
        }

        return $this->render('admin/standard_item/form.html.twig', [
            'item' => null,
            'formTitle' => 'Novo Item de Série',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_standard_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StandardItem $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();
            $this->addFlash('success', 'Item de Série atualizado com sucesso!');
            return $this->redirectToRoute('app_admin_standard_item_index');
        }

        return $this->render('admin/standard_item/form.html.twig', [
            'item' => $item,
            'formTitle' => 'Editar Item de Série',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_standard_item_delete', methods: ['POST'])]
    public function delete(Request $request, StandardItem $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Item de Série removido com sucesso!');
        }
        return $this->redirectToRoute('app_admin_standard_item_index');
    }

    #[Route('/reorder', name: 'app_admin_standard_item_reorder', methods: ['POST'])]
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

    private function handleFormData(StandardItem $item, Request $request): void
    {
        $data = $request->request;
        $item->setIcon($data->get('icon'));
        $item->setNamePt($data->get('namePt'));
        $item->setNameEn($data->get('nameEn'));
        $item->setNameEs($data->get('nameEs'));
        $item->setPosition((int) $data->get('position', 0));
        $item->setIsActive((bool) $data->get('isActive', false));
    }
}
