<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Entity\NewsImage;
use App\Repository\NewsRepository;
use App\Repository\NewsCategoryRepository;
use App\Repository\NewsImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[Route('/admin/news')]
final class NewsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly NewsRepository $repo,
        private readonly NewsCategoryRepository $categoryRepo,
        private readonly NewsImageRepository $imageRepo,
    ) {}

    #[Route('', name: 'app_admin_news_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/news/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_news_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new News();
            $this->handleFormData($item, $request);

            $this->em->persist($item);
            $this->em->flush();

            $this->addFlash('success', 'Notícia criada com sucesso!');
            return $this->redirectToRoute('app_admin_news_edit', ['id' => $item->getId()]);
        }

        return $this->render('admin/news/form.html.twig', [
            'item' => null,
            'categories' => $this->categoryRepo->findAllOrdered(),
            'formTitle' => 'Nova Notícia',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_news_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, News $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();

            $this->addFlash('success', 'Notícia atualizada com sucesso!');
            return $this->redirectToRoute('app_admin_news_index');
        }

        return $this->render('admin/news/form.html.twig', [
            'item' => $item,
            'categories' => $this->categoryRepo->findAllOrdered(),
            'formTitle' => 'Editar Notícia',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_news_delete', methods: ['POST'])]
    public function delete(Request $request, News $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Notícia removida com sucesso!');
        }

        return $this->redirectToRoute('app_admin_news_index');
    }

    // ─── News Gallery Management ──────────────────────────────────────────

    #[Route('/{id}/gallery/add', name: 'app_admin_news_gallery_add', methods: ['POST'])]
    public function addGalleryImage(Request $request, News $item): Response
    {
        $imageFile = $request->files->get('galleryFile');
        if ($imageFile instanceof UploadedFile) {
            $img = new NewsImage();
            $img->setNews($item);
            $img->setImageFile($imageFile);
            $img->setCaptionPt($request->request->get('captionPt'));
            $img->setCaptionEn($request->request->get('captionEn'));
            $img->setCaptionEs($request->request->get('captionEs'));
            
            // Set position as the last item
            $existingCount = count($item->getImages());
            $img->setPosition($existingCount);

            $this->em->persist($img);
            $this->em->flush();

            $this->addFlash('success', 'Imagem adicionada à galeria!');
        } else {
            $this->addFlash('error', 'Nenhum arquivo enviado.');
        }

        return $this->redirectToRoute('app_admin_news_edit', ['id' => $item->getId()]);
    }

    #[Route('/gallery/{imageId}/delete', name: 'app_admin_news_gallery_delete', methods: ['POST'])]
    public function deleteGalleryImage(Request $request, int $imageId): Response
    {
        $img = $this->imageRepo->find($imageId);
        if (!$img) {
            throw $this->createNotFoundException('Imagem não encontrada');
        }

        $newsId = $img->getNews()->getId();

        if ($this->isCsrfTokenValid('delete_gallery' . $img->getId(), $request->request->get('_token'))) {
            $this->em->remove($img);
            $this->em->flush();
            $this->addFlash('success', 'Imagem removida da galeria!');
        }

        return $this->redirectToRoute('app_admin_news_edit', ['id' => $newsId]);
    }

    #[Route('/gallery/reorder', name: 'app_admin_news_gallery_reorder', methods: ['POST'])]
    public function reorderGallery(Request $request): JsonResponse
    {
        $ids = json_decode($request->getContent(), true)['ids'] ?? [];

        foreach ($ids as $position => $id) {
            $img = $this->imageRepo->find($id);
            if ($img) {
                $img->setPosition($position);
            }
        }
        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }

    private function handleFormData(News $item, Request $request): void
    {
        $data = $request->request;

        $item->setTitlePt($data->get('titlePt'));
        $item->setTitleEn($data->get('titleEn'));
        $item->setTitleEs($data->get('titleEs'));
        
        $item->setShortDescriptionPt($data->get('shortDescriptionPt'));
        $item->setShortDescriptionEn($data->get('shortDescriptionEn'));
        $item->setShortDescriptionEs($data->get('shortDescriptionEs'));

        $item->setFullDescriptionPt($data->get('fullDescriptionPt'));
        $item->setFullDescriptionEn($data->get('fullDescriptionEn'));
        $item->setFullDescriptionEs($data->get('fullDescriptionEs'));

        $item->setYoutubeVideoCode($data->get('youtubeVideoCode'));

        $item->setSeoTitlePt($data->get('seoTitlePt'));
        $item->setSeoTitleEn($data->get('seoTitleEn'));
        $item->setSeoTitleEs($data->get('seoTitleEs'));

        $item->setSeoDescriptionPt($data->get('seoDescriptionPt'));
        $item->setSeoDescriptionEn($data->get('seoDescriptionEn'));
        $item->setSeoDescriptionEs($data->get('seoDescriptionEs'));

        $item->setImageAltPt($data->get('imageAltPt'));
        $item->setImageAltEn($data->get('imageAltEn'));
        $item->setImageAltEs($data->get('imageAltEs'));

        $item->setIsActive((bool) $data->get('isActive', false));
        $item->setIsHighlighted((bool) $data->get('isHighlighted', false));

        $dateStr = $data->get('date');
        if ($dateStr) {
            $item->setDate(new \DateTime($dateStr));
        }

        // Generate slugs
        $slugger = new AsciiSlugger();
        $slugPt = $data->get('slugPt') ?: $slugger->slug($item->getTitlePt() ?? '')->lower()->toString();
        $slugEn = $data->get('slugEn') ?: $slugger->slug($item->getTitleEn() ?: ($item->getTitlePt() ?? ''))->lower()->toString();
        $slugEs = $data->get('slugEs') ?: $slugger->slug($item->getTitleEs() ?: ($item->getTitlePt() ?? ''))->lower()->toString();
        
        $item->setSlugPt($slugPt);
        $item->setSlugEn($slugEn);
        $item->setSlugEs($slugEs);

        // Manage categories relation
        // Remove existing relations
        foreach ($item->getCategories() as $cat) {
            $item->removeCategory($cat);
        }
        // Add new relations
        $catIds = $data->all('categories') ?: [];
        foreach ($catIds as $catId) {
            $cat = $this->categoryRepo->find($catId);
            if ($cat) {
                $item->addCategory($cat);
            }
        }

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
