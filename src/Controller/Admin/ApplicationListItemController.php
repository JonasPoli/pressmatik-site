<?php

namespace App\Controller\Admin;

use App\Entity\ApplicationListItem;
use App\Repository\ApplicationListItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/application-list-item')]
final class ApplicationListItemController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ApplicationListItemRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_application_list_item_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/application_list_item/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_application_list_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new ApplicationListItem();
            $this->handleFormData($item, $request);
            $this->em->persist($item);
            $this->em->flush();
            $this->addFlash('success', 'Aplicação (Listagem) criada com sucesso!');
            return $this->redirectToRoute('app_admin_application_list_item_index');
        }

        return $this->render('admin/application_list_item/form.html.twig', [
            'item' => null,
            'formTitle' => 'Nova Aplicação (Listagem)',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_application_list_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ApplicationListItem $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();
            $this->addFlash('success', 'Aplicação (Listagem) atualizada com sucesso!');
            return $this->redirectToRoute('app_admin_application_list_item_index');
        }

        return $this->render('admin/application_list_item/form.html.twig', [
            'item' => $item,
            'formTitle' => 'Editar Aplicação (Listagem)',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_application_list_item_delete', methods: ['POST'])]
    public function delete(Request $request, ApplicationListItem $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Aplicação (Listagem) removida com sucesso!');
        }
        return $this->redirectToRoute('app_admin_application_list_item_index');
    }

    #[Route('/reorder', name: 'app_admin_application_list_item_reorder', methods: ['POST'])]
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

    private function handleFormData(ApplicationListItem $item, Request $request): void
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
