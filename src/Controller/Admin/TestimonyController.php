<?php

namespace App\Controller\Admin;

use App\Entity\Testimony;
use App\Repository\TestimonyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/testimony')]
final class TestimonyController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TestimonyRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_testimony_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/testimony/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_testimony_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new Testimony();
            $this->handleFormData($item, $request);

            $this->em->persist($item);
            $this->em->flush();

            $this->addFlash('success', 'Depoimento criado com sucesso!');
            return $this->redirectToRoute('app_admin_testimony_index');
        }

        return $this->render('admin/testimony/form.html.twig', [
            'item' => null,
            'formTitle' => 'Novo Depoimento',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_testimony_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Testimony $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();

            $this->addFlash('success', 'Depoimento atualizado com sucesso!');
            return $this->redirectToRoute('app_admin_testimony_index');
        }

        return $this->render('admin/testimony/form.html.twig', [
            'item' => $item,
            'formTitle' => 'Editar Depoimento',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_testimony_delete', methods: ['POST'])]
    public function delete(Request $request, Testimony $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Depoimento removido com sucesso!');
        }

        return $this->redirectToRoute('app_admin_testimony_index');
    }

    #[Route('/reorder', name: 'app_admin_testimony_reorder', methods: ['POST'])]
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

    private function handleFormData(Testimony $item, Request $request): void
    {
        $data = $request->request;

        $item->setName($data->get('name'));
        $item->setCompany($data->get('company'));
        $item->setRolePt($data->get('rolePt'));
        $item->setRoleEn($data->get('roleEn'));
        $item->setRoleEs($data->get('roleEs'));
        $item->setTextPt($data->get('textPt'));
        $item->setTextEn($data->get('textEn'));
        $item->setTextEs($data->get('textEs'));
        $item->setIsActive((bool) $data->get('isActive', false));
        $item->setPosition((int) $data->get('position', 0));

        $imageFile = $request->files->get('imageFile');
        if ($imageFile instanceof UploadedFile) {
            $item->setImageFile($imageFile);
        }
    }
}
