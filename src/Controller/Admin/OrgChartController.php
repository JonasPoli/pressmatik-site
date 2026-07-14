<?php

namespace App\Controller\Admin;

use App\Entity\OrgChartItem;
use App\Repository\OrgChartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/org-chart')]
final class OrgChartController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly OrgChartItemRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_org_chart_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/org_chart/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_org_chart_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new OrgChartItem();
            $this->handleFormData($item, $request);
            $this->em->persist($item);
            $this->em->flush();
            $this->addFlash('success', 'Item do organograma adicionado!');
            return $this->redirectToRoute('app_admin_org_chart_index');
        }
        return $this->render('admin/org_chart/form.html.twig', ['item' => null, 'formTitle' => 'Novo Item']);
    }

    #[Route('/{id}/edit', name: 'app_admin_org_chart_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OrgChartItem $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();
            $this->addFlash('success', 'Item atualizado!');
            return $this->redirectToRoute('app_admin_org_chart_index');
        }
        return $this->render('admin/org_chart/form.html.twig', ['item' => $item, 'formTitle' => 'Editar Item']);
    }

    #[Route('/{id}/delete', name: 'app_admin_org_chart_delete', methods: ['POST'])]
    public function delete(Request $request, OrgChartItem $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Item removido!');
        }
        return $this->redirectToRoute('app_admin_org_chart_index');
    }

    #[Route('/reorder', name: 'app_admin_org_chart_reorder', methods: ['POST'])]
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

    private function handleFormData(OrgChartItem $item, Request $request): void
    {
        $data = $request->request;
        $item->setTitlePt($data->get('titlePt'));
        $item->setTitleEn($data->get('titleEn'));
        $item->setTitleEs($data->get('titleEs'));
        $item->setDescriptionPt($data->get('descriptionPt'));
        $item->setDescriptionEn($data->get('descriptionEn'));
        $item->setDescriptionEs($data->get('descriptionEs'));
        $item->setIcon($data->get('icon'));
        $item->setPosition((int) $data->get('position', 0));
    }
}
