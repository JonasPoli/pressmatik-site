<?php

namespace App\Controller;

use App\Repository\AboutUsRepository;
use App\Repository\BannerRepository;
use App\Repository\DifferentialRepository;
use App\Repository\HistoryTimelineRepository;
use App\Repository\OrgChartItemRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductSpecValueRepository;
use App\Repository\ProductConfigItemRepository;
use App\Repository\ProductVideoRepository;
use App\Repository\QualityCertificationRepository;
use App\Repository\SuccessCaseRepository;
use App\Repository\SupplierRepository;
use App\Service\ProductCatalogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class SiteController extends AbstractController
{
    public function __construct(
        private readonly ProductCatalogService $catalogService,
        private readonly AboutUsRepository $aboutUsRepo,
        private readonly HistoryTimelineRepository $historyRepo,
        private readonly ProductSizeRepository $sizeRepo,
        private readonly ProductSpecValueRepository $specValueRepo,
        private readonly ProductConfigItemRepository $configRepo,
        private readonly ProductVideoRepository $videoRepo,
        private readonly \App\Repository\TestimonyRepository $testimonyRepo,
        private readonly \App\Repository\ClientLogoRepository $clientLogoRepo,
        private readonly \App\Repository\ServiceRepository $serviceRepo,
        private readonly \App\Repository\NewsRepository $newsRepo,
        private readonly ProductRepository $productRepo,
        private readonly BannerRepository $bannerRepo,
        private readonly DifferentialRepository $differentialRepo,
        private readonly SupplierRepository $supplierRepo,
        private readonly QualityCertificationRepository $qualityRepo,
        private readonly SuccessCaseRepository $successCaseRepo,
        private readonly OrgChartItemRepository $orgChartRepo,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/{_locale}/', name: 'app_home', requirements: ['_locale' => 'pt|en|es'])]
    public function home(string $_locale): Response
    {
        return $this->render('site/home.html.twig', [
            'locale' => $_locale,
            'catalog' => $this->catalogService->getCatalog(),
            'testimonials' => $this->testimonyRepo->findActiveOrdered(),
            'clientLogos' => $this->clientLogoRepo->findAllOrdered(),
            'services' => $this->serviceRepo->findActiveOrdered(),
            'news' => $this->newsRepo->findLatestActive(3),
            'banners' => $this->bannerRepo->findActive(),
            'differentials' => $this->differentialRepo->findActive(),
            'about' => $this->aboutUsRepo->findOrCreate(),
        ]);
    }

    #[Route('/{_locale}/quem-somos', name: 'app_about_us', requirements: ['_locale' => 'pt|en|es'])]
    public function aboutUs(string $_locale): Response
    {
        $about = $this->aboutUsRepo->findOrCreate();
        $timeline = $this->historyRepo->findAllOrdered();

        return $this->render('site/about.html.twig', [
            'locale' => $_locale,
            'about' => $about,
            'timeline' => $timeline,
            'catalog' => $this->catalogService->getCatalog(),
            'suppliers' => $this->supplierRepo->findActive(),
            'certifications' => $this->qualityRepo->findActive(),
            'successCases' => $this->successCaseRepo->findActive(),
            'orgChart' => $this->orgChartRepo->findAllOrdered(),
            'differentials' => $this->differentialRepo->findActive(),
            'clientLogos' => $this->clientLogoRepo->findAllOrdered(),
        ]);
    }

    #[Route('/{_locale}/produtos/{slug}', name: 'app_product_detail', requirements: ['_locale' => 'pt|en|es'])]
    public function productDetail(Request $request, string $_locale, string $slug): Response
    {
        $product = $this->productRepo->findBySlug($slug);

        if (!$product) {
            throw $this->createNotFoundException('Produto não encontrado');
        }

        // Find active subproduct from query parameter, fallback to first subproduct
        $subproducts = $product->getSubproducts();
        $activeSubproduct = null;
        $modelCode = $request->query->get('model');
        if ($modelCode) {
            foreach ($subproducts as $sub) {
                if (strcasecmp($sub->getModel(), $modelCode) === 0) {
                    $activeSubproduct = $sub;
                    break;
                }
            }
        }
        if (!$activeSubproduct) {
            $activeSubproduct = $product->getDefaultSubproduct() ?: ($subproducts->first() ?: null);
        }

        // Fetch dynamic data from database
        $sizes = $this->sizeRepo->findBySlugOrdered($slug);
        $specValues = $this->specValueRepo->findBySlugOrdered($slug);
        $standardItems = $this->configRepo->findBySlugAndType($slug, 'standard');
        $optionalItems = $this->configRepo->findBySlugAndType($slug, 'optional');
        $videos = $this->videoRepo->findBySlugOrdered($slug);

        // Group specValues by spec and position for the public template specs table
        $specsTableRows = [];
        $groupedByPosition = [];
        foreach ($specValues as $val) {
            $pos = $val->getPosition();
            if (!isset($groupedByPosition[$pos])) {
                $groupedByPosition[$pos] = [
                    'specification' => $val->getSpecification(),
                    'values' => [],
                ];
            }
            $groupedByPosition[$pos]['values'][$val->getProductSize()->getId()] = [
                'v' => $val->getVTypeValue(),
                'h' => $val->getHTypeValue(),
            ];
        }
        ksort($groupedByPosition);
        $specsTableRows = array_values($groupedByPosition);

        return $this->render('site/product_detail.html.twig', [
            'locale' => $_locale,
            'slug' => $slug,
            'product' => $product,
            'activeSubproduct' => $activeSubproduct,
            'catalog' => $this->catalogService->getCatalog(),
            'productSizes' => $sizes,
            'specsTableRows' => $specsTableRows,
            'standardItems' => $standardItems,
            'optionalItems' => $optionalItems,
            'productVideos' => $videos,
        ]);
    }

    #[Route('/{_locale}/noticias', name: 'app_news_list', requirements: ['_locale' => 'pt|en|es'])]
    public function newsList(Request $request, string $_locale): Response
    {
        $catId = $request->query->get('category');
        $activeCategory = null;
        if ($catId) {
            $activeCategory = $this->em->getRepository(\App\Entity\NewsCategory::class)->find($catId);
        }

        $newsItems = $this->newsRepo->findActiveOrdered($activeCategory);
        $categories = $this->em->getRepository(\App\Entity\NewsCategory::class)->findAllOrdered();

        return $this->render('site/news_list.html.twig', [
            'locale' => $_locale,
            'catalog' => $this->catalogService->getCatalog(),
            'newsItems' => $newsItems,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
        ]);
    }

    #[Route('/{_locale}/noticias/{slug}', name: 'app_news_detail', requirements: ['_locale' => 'pt|en|es'])]
    public function newsDetail(string $_locale, string $slug): Response
    {
        $item = $this->newsRepo->findOneBySlug($slug, $_locale);
        if (!$item) {
            throw $this->createNotFoundException('Notícia não encontrada');
        }

        return $this->render('site/news_detail.html.twig', [
            'locale' => $_locale,
            'catalog' => $this->catalogService->getCatalog(),
            'item' => $item,
        ]);
    }

    #[Route('/{_locale}/servicos/{slug}', name: 'app_service_detail', requirements: ['_locale' => 'pt|en|es'])]
    public function serviceDetail(string $_locale, string $slug): Response
    {
        $item = $this->serviceRepo->findOneBySlug($slug);
        if (!$item) {
            throw $this->createNotFoundException('Serviço não encontrado');
        }

        return $this->render('site/service_detail.html.twig', [
            'locale' => $_locale,
            'catalog' => $this->catalogService->getCatalog(),
            'item' => $item,
        ]);
    }

    #[Route('/{_locale}/contato-enviar', name: 'app_contact_submit', methods: ['POST'], requirements: ['_locale' => 'pt|en|es'])]
    public function contactSubmit(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true) ?: $request->request->all();

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;
        $cpfCnpj = $data['cpf_cnpj'] ?? null;
        $company = $data['company'] ?? null;
        $productInterest = $data['product_interest'] ?? null;
        $message = $data['message'] ?? null;
        $type = $data['type'] ?? 'contact';
        $productSlug = $data['product_slug'] ?? null;

        if (!$name || !$email) {
            return $this->json(['success' => false, 'message' => 'Nome e e-mail são obrigatórios.'], Response::HTTP_BAD_REQUEST);
        }

        $msg = new \App\Entity\ContactMessage();
        $msg->setName($name);
        $msg->setEmail($email);
        $msg->setPhone($phone);
        $msg->setCpfCnpj($cpfCnpj);
        $msg->setCompany($company);
        $msg->setProductInterest($productInterest);
        $msg->setMessage($message);
        $msg->setType($type);
        $msg->setProductSlug($productSlug);

        $em->persist($msg);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Sua mensagem foi enviada com sucesso!']);
    }
}
