<?php

namespace App\Controller\Admin;

use App\Entity\SuccessCase;
use App\Repository\SuccessCaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/success-case')]
final class SuccessCaseController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SuccessCaseRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_success_case_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/success_case/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_success_case_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new SuccessCase();
            $this->handleFormData($item, $request);
            $this->em->persist($item);
            $this->em->flush();
            $this->addFlash('success', 'Case de sucesso adicionado!');
            return $this->redirectToRoute('app_admin_success_case_index');
        }
        return $this->render('admin/success_case/form.html.twig', ['item' => null, 'formTitle' => 'Novo Case de Sucesso']);
    }

    #[Route('/{id}/edit', name: 'app_admin_success_case_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SuccessCase $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();
            $this->addFlash('success', 'Case atualizado!');
            return $this->redirectToRoute('app_admin_success_case_index');
        }
        return $this->render('admin/success_case/form.html.twig', ['item' => $item, 'formTitle' => 'Editar Case de Sucesso']);
    }

    #[Route('/{id}/delete', name: 'app_admin_success_case_delete', methods: ['POST'])]
    public function delete(Request $request, SuccessCase $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Case removido!');
        }
        return $this->redirectToRoute('app_admin_success_case_index');
    }

    #[Route('/reorder', name: 'app_admin_success_case_reorder', methods: ['POST'])]
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

    private function handleFormData(SuccessCase $item, Request $request): void
    {
        $data = $request->request;
        $item->setTitlePt($data->get('titlePt'));
        $item->setTitleEn($data->get('titleEn'));
        $item->setTitleEs($data->get('titleEs'));
        $item->setDescriptionPt($data->get('descriptionPt'));
        $item->setDescriptionEn($data->get('descriptionEn'));
        $item->setDescriptionEs($data->get('descriptionEs'));
        $item->setClientName($data->get('clientName'));
        $item->setClientIndustry($data->get('clientIndustry'));
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
