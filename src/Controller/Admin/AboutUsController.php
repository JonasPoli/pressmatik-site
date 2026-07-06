<?php

namespace App\Controller\Admin;

use App\Entity\AboutUs;
use App\Entity\AboutGalleryImage;
use App\Repository\AboutUsRepository;
use App\Repository\AboutGalleryImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/about-us')]
final class AboutUsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AboutUsRepository $aboutUsRepository,
        private readonly AboutGalleryImageRepository $galleryRepo,
    ) {}

    #[Route('', name: 'app_admin_about_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $about = $this->aboutUsRepository->findOrCreate();

        if ($request->isMethod('POST')) {
            $data = $request->request;

            // Textos básicos
            $about->setTitlePt($data->get('titlePt'));
            $about->setTitleEn($data->get('titleEn'));
            $about->setTitleEs($data->get('titleEs'));
            $about->setSubtitlePt($data->get('subtitlePt'));
            $about->setSubtitleEn($data->get('subtitleEn'));
            $about->setSubtitleEs($data->get('subtitleEs'));
            $about->setDescriptionPt($data->get('descriptionPt'));
            $about->setDescriptionEn($data->get('descriptionEn'));
            $about->setDescriptionEs($data->get('descriptionEs'));

            // Missão / Visão / Valores
            $about->setMissionPt($data->get('missionPt'));
            $about->setMissionEn($data->get('missionEn'));
            $about->setMissionEs($data->get('missionEs'));
            $about->setVisionPt($data->get('visionPt'));
            $about->setVisionEn($data->get('visionEn'));
            $about->setVisionEs($data->get('visionEs'));
            $about->setValuesPt($data->get('valuesPt'));
            $about->setValuesEn($data->get('valuesEn'));
            $about->setValuesEs($data->get('valuesEs'));

            // Vantagens
            for ($i = 1; $i <= 4; $i++) {
                $about->{"setAdvantage{$i}TitlePt"}($data->get("advantage{$i}TitlePt"));
                $about->{"setAdvantage{$i}TitleEn"}($data->get("advantage{$i}TitleEn"));
                $about->{"setAdvantage{$i}TitleEs"}($data->get("advantage{$i}TitleEs"));
                $about->{"setAdvantage{$i}DescPt"}($data->get("advantage{$i}DescPt"));
                $about->{"setAdvantage{$i}DescEn"}($data->get("advantage{$i}DescEn"));
                $about->{"setAdvantage{$i}DescEs"}($data->get("advantage{$i}DescEs"));
                $about->{"setAdvantage{$i}Icon"}($data->get("advantage{$i}Icon"));
            }

            // Banner image
            $bannerFile = $request->files->get('bannerImageFile');
            if ($bannerFile instanceof UploadedFile) {
                $about->setBannerImageFile($bannerFile);
            }

            $this->em->flush();
            $this->addFlash('success', 'Quem Somos atualizado com sucesso!');

            return $this->redirectToRoute('app_admin_about_edit');
        }

        return $this->render('admin/about-us/edit.html.twig', [
            'about' => $about,
            'galleryImages' => $this->galleryRepo->findBy(['aboutUs' => $about], ['position' => 'ASC']),
        ]);
    }

    #[Route('/gallery/upload', name: 'app_admin_about_gallery_upload', methods: ['POST'])]
    public function galleryUpload(Request $request): JsonResponse
    {
        $about = $this->aboutUsRepository->findOrCreate();
        $file = $request->files->get('file');

        if (!$file instanceof UploadedFile) {
            return new JsonResponse(['error' => 'Nenhum arquivo enviado.'], 400);
        }

        $image = new AboutGalleryImage();
        $image->setAboutUs($about);
        $image->setImageFile($file);
        $image->setPosition(
            count($about->getGalleryImages())
        );

        $this->em->persist($image);
        $this->em->flush();

        return new JsonResponse([
            'id' => $image->getId(),
            'imageName' => $image->getImageName(),
            'url' => '/uploads/gallery/' . $image->getImageName(),
        ]);
    }

    #[Route('/gallery/{id}/delete', name: 'app_admin_about_gallery_delete', methods: ['POST'])]
    public function galleryDelete(AboutGalleryImage $image): JsonResponse
    {
        $this->em->remove($image);
        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/gallery/reorder', name: 'app_admin_about_gallery_reorder', methods: ['POST'])]
    public function galleryReorder(Request $request): JsonResponse
    {
        $ids = json_decode($request->getContent(), true)['ids'] ?? [];

        foreach ($ids as $position => $id) {
            $image = $this->galleryRepo->find($id);
            if ($image) {
                $image->setPosition($position);
            }
        }
        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }
}
