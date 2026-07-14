<?php

namespace App\Controller\Admin;

use App\Entity\Supplier;
use App\Repository\SupplierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/supplier')]
final class SupplierController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SupplierRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_supplier_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/supplier/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_supplier_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new Supplier();
            $this->handleFormData($item, $request);
            $this->em->persist($item);
            $this->em->flush();
            $this->addFlash('success', 'Fornecedor adicionado com sucesso!');
            return $this->redirectToRoute('app_admin_supplier_index');
        }
        return $this->render('admin/supplier/form.html.twig', ['item' => null, 'formTitle' => 'Novo Fornecedor']);
    }

    #[Route('/{id}/edit', name: 'app_admin_supplier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Supplier $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();
            $this->addFlash('success', 'Fornecedor atualizado!');
            return $this->redirectToRoute('app_admin_supplier_index');
        }
        return $this->render('admin/supplier/form.html.twig', ['item' => $item, 'formTitle' => 'Editar Fornecedor']);
    }

    #[Route('/{id}/delete', name: 'app_admin_supplier_delete', methods: ['POST'])]
    public function delete(Request $request, Supplier $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Fornecedor removido!');
        }
        return $this->redirectToRoute('app_admin_supplier_index');
    }

    #[Route('/reorder', name: 'app_admin_supplier_reorder', methods: ['POST'])]
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

    private function handleFormData(Supplier $item, Request $request): void
    {
        $data = $request->request;
        $item->setName($data->get('name'));
        $item->setWebsiteUrl($data->get('websiteUrl'));
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
