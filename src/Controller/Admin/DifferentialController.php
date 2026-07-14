<?php

namespace App\Controller\Admin;

use App\Entity\Differential;
use App\Repository\DifferentialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/differential')]
final class DifferentialController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DifferentialRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_differential_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/differential/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_differential_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new Differential();
            $this->handleFormData($item, $request);
            $this->em->persist($item);
            $this->em->flush();
            $this->addFlash('success', 'Diferencial adicionado com sucesso!');
            return $this->redirectToRoute('app_admin_differential_index');
        }
        return $this->render('admin/differential/form.html.twig', ['item' => null, 'formTitle' => 'Novo Diferencial']);
    }

    #[Route('/{id}/edit', name: 'app_admin_differential_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Differential $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();
            $this->addFlash('success', 'Diferencial atualizado!');
            return $this->redirectToRoute('app_admin_differential_index');
        }
        return $this->render('admin/differential/form.html.twig', ['item' => $item, 'formTitle' => 'Editar Diferencial']);
    }

    #[Route('/{id}/delete', name: 'app_admin_differential_delete', methods: ['POST'])]
    public function delete(Request $request, Differential $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Diferencial removido!');
        }
        return $this->redirectToRoute('app_admin_differential_index');
    }

    #[Route('/reorder', name: 'app_admin_differential_reorder', methods: ['POST'])]
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

    private function handleFormData(Differential $item, Request $request): void
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
        $item->setIsActive((bool) $data->get('isActive', false));
    }
}
