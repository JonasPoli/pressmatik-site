<?php

namespace App\Controller\Admin;

use App\Entity\Service;
use App\Entity\ServiceImage;
use App\Repository\ServiceRepository;
use App\Repository\ServiceImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[Route('/admin/service')]
final class ServiceController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ServiceRepository $repo,
        private readonly ServiceImageRepository $imageRepo,
    ) {}

    #[Route('', name: 'app_admin_service_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/service/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'app_admin_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $item = new Service();
            $this->handleFormData($item, $request);

            $this->em->persist($item);
            $this->em->flush();

            $this->addFlash('success', 'Serviço criado com sucesso!');
            return $this->redirectToRoute('app_admin_service_edit', ['id' => $item->getId()]);
        }

        return $this->render('admin/service/form.html.twig', [
            'item' => null,
            'formTitle' => 'Novo Serviço',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $item): Response
    {
        if ($request->isMethod('POST')) {
            $this->handleFormData($item, $request);
            $this->em->flush();

            $this->addFlash('success', 'Serviço atualizado com sucesso!');
            return $this->redirectToRoute('app_admin_service_index');
        }

        return $this->render('admin/service/form.html.twig', [
            'item' => $item,
            'formTitle' => 'Editar Serviço',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_service_delete', methods: ['POST'])]
    public function delete(Request $request, Service $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Serviço removido com sucesso!');
        }

        return $this->redirectToRoute('app_admin_service_index');
    }

    #[Route('/reorder', name: 'app_admin_service_reorder', methods: ['POST'])]
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

    // ─── Service Gallery Management ────────────────────────────────────────

    #[Route('/{id}/gallery/add', name: 'app_admin_service_gallery_add', methods: ['POST'])]
    public function addGalleryImage(Request $request, Service $item): Response
    {
        $imageFile = $request->files->get('galleryFile');
        if ($imageFile instanceof UploadedFile) {
            $img = new ServiceImage();
            $img->setService($item);
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

        return $this->redirectToRoute('app_admin_service_edit', ['id' => $item->getId()]);
    }

    #[Route('/gallery/{imageId}/delete', name: 'app_admin_service_gallery_delete', methods: ['POST'])]
    public function deleteGalleryImage(Request $request, int $imageId): Response
    {
        $img = $this->imageRepo->find($imageId);
        if (!$img) {
            throw $this->createNotFoundException('Imagem não encontrada');
        }

        $serviceId = $img->getService()->getId();

        if ($this->isCsrfTokenValid('delete_gallery' . $img->getId(), $request->request->get('_token'))) {
            $this->em->remove($img);
            $this->em->flush();
            $this->addFlash('success', 'Imagem removida da galeria!');
        }

        return $this->redirectToRoute('app_admin_service_edit', ['id' => $serviceId]);
    }

    #[Route('/gallery/reorder', name: 'app_admin_service_gallery_reorder', methods: ['POST'])]
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

    private function handleFormData(Service $item, Request $request): void
    {
        $data = $request->request;

        $item->setTitlePt($data->get('titlePt'));
        $item->setTitleEn($data->get('titleEn'));
        $item->setTitleEs($data->get('titleEs'));
        
        $item->setShortDescriptionPt($data->get('shortDescriptionPt'));
        $item->setShortDescriptionEn($data->get('shortDescriptionEn'));
        $item->setShortDescriptionEs($data->get('shortDescriptionEs'));

        $item->setDescriptionPt($data->get('descriptionPt'));
        $item->setDescriptionEn($data->get('descriptionEn'));
        $item->setDescriptionEs($data->get('descriptionEs'));

        $item->setIsActive((bool) $data->get('isActive', false));
        $item->setPosition((int) $data->get('position', 0));

        // Generate slugs
        $slugger = new AsciiSlugger();
        $slugPt = $data->get('slugPt') ?: $slugger->slug($item->getTitlePt() ?? '')->lower()->toString();
        $slugEn = $data->get('slugEn') ?: $slugger->slug($item->getTitleEn() ?: ($item->getTitlePt() ?? ''))->lower()->toString();
        $slugEs = $data->get('slugEs') ?: $slugger->slug($item->getTitleEs() ?: ($item->getTitlePt() ?? ''))->lower()->toString();
        
        $item->setSlugPt($slugPt);
        $item->setSlugEn($slugEn);
        $item->setSlugEs($slugEs);

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
