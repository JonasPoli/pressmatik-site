<?php

namespace App\Controller\Admin;

use App\Entity\TechnicalSpecification;
use App\Repository\TechnicalSpecificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/technical-specs')]
final class TechnicalSpecController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TechnicalSpecificationRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_tech_spec_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/product/specs_list.html.twig', [
            'specs' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_tech_spec_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $spec = new TechnicalSpecification();
            $this->handleFormData($spec, $request);
            $this->em->persist($spec);
            $this->em->flush();
            $this->addFlash('success', 'Especificação criada com sucesso!');
            return $this->redirectToRoute('app_admin_tech_spec_index');
        }

        return $this->render('admin/product/spec_form.html.twig', [
            'spec' => null,
            'formTitle' => 'Nova Especificação',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_tech_spec_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TechnicalSpecification $spec): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($spec, $request);
            $this->em->flush();
            $this->addFlash('success', 'Especificação atualizada!');
            return $this->redirectToRoute('app_admin_tech_spec_index');
        }

        return $this->render('admin/product/spec_form.html.twig', [
            'spec' => $spec,
            'formTitle' => 'Editar Especificação',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_tech_spec_delete', methods: ['POST'])]
    public function delete(Request $request, TechnicalSpecification $spec): Response
    {
        if ($this->isCsrfTokenValid('delete' . $spec->getId(), $request->request->get('_token'))) {
            $this->em->remove($spec);
            $this->em->flush();
            $this->addFlash('success', 'Especificação removida!');
        }
        return $this->redirectToRoute('app_admin_tech_spec_index');
    }

    #[Route('/reorder', name: 'app_admin_tech_spec_reorder', methods: ['POST'])]
    public function reorder(Request $request): JsonResponse
    {
        $ids = json_decode($request->getContent(), true)['ids'] ?? [];
        foreach ($ids as $pos => $id) {
            $spec = $this->repo->find($id);
            if ($spec) $spec->setPosition($pos);
        }
        $this->em->flush();
        return new JsonResponse(['success' => true]);
    }

    private function handleFormData(TechnicalSpecification $spec, Request $request): void
    {
        $data = $request->request;
        $spec->setNamePt($data->get('namePt'));
        $spec->setNameEn($data->get('nameEn'));
        $spec->setNameEs($data->get('nameEs'));
        $spec->setUnitPt($data->get('unitPt'));
        $spec->setUnitEn($data->get('unitEn'));
        $spec->setUnitEs($data->get('unitEs'));
        $spec->setPosition((int) $data->get('position', 0));
    }
}
