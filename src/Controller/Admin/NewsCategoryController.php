<?php

namespace App\Controller\Admin;

use App\Entity\NewsCategory;
use App\Repository\NewsCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/news-category')]
final class NewsCategoryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly NewsCategoryRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_news_category_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/news_category/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_news_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new NewsCategory();
            $this->handleFormData($item, $request);

            $this->em->persist($item);
            $this->em->flush();

            $this->addFlash('success', 'Categoria de notícia criada com sucesso!');
            return $this->redirectToRoute('app_admin_news_category_index');
        }

        return $this->render('admin/news_category/form.html.twig', [
            'item' => null,
            'formTitle' => 'Nova Categoria de Notícias',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_news_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NewsCategory $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();

            $this->addFlash('success', 'Categoria de notícia atualizada com sucesso!');
            return $this->redirectToRoute('app_admin_news_category_index');
        }

        return $this->render('admin/news_category/form.html.twig', [
            'item' => $item,
            'formTitle' => 'Editar Categoria de Notícias',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_news_category_delete', methods: ['POST'])]
    public function delete(Request $request, NewsCategory $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            if ($item->getNews()->count() > 0) {
                $this->addFlash('error', 'Esta categoria está vinculada a notícias e não pode ser removida.');
            } else {
                $this->em->remove($item);
                $this->em->flush();
                $this->addFlash('success', 'Categoria removida com sucesso!');
            }
        }

        return $this->redirectToRoute('app_admin_news_category_index');
    }

    private function handleFormData(NewsCategory $item, Request $request): void
    {
        $data = $request->request;

        $item->setNamePt($data->get('namePt'));
        $item->setNameEn($data->get('nameEn'));
        $item->setNameEs($data->get('nameEs'));
    }
}
