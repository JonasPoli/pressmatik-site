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
        $sizes = [];
        $specValues = [];
        if ($activeSubproduct) {
            $sizes = $this->sizeRepo->findBySubproductOrdered($activeSubproduct->getId());
            $specValues = $this->specValueRepo->findBySubproductOrdered($activeSubproduct->getId());
        }
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
    public function contactSubmit(
        Request $request,
        EntityManagerInterface $em,
        \Symfony\Component\Mailer\MailerInterface $mailer,
        \Psr\Log\LoggerInterface $logger
    ): Response {
        $data = json_decode($request->getContent(), true) ?: $request->request->all();

        $name = trim((string)($data['name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $phone = trim((string)($data['phone'] ?? ''));
        $cpfCnpjRaw = (string)($data['cpf_cnpj'] ?? '');
        $cpfCnpjDigits = preg_replace('/[^\d]/', '', $cpfCnpjRaw);
        $company = trim((string)($data['company'] ?? ''));
        $productInterest = trim((string)($data['product_interest'] ?? ''));
        $message = trim((string)($data['message'] ?? ''));
        $type = $data['type'] ?? 'contact';
        $productSlug = $data['product_slug'] ?? null;

        // Validação de campos obrigatórios
        if (mb_strlen($name) < 3) {
            return $this->json(['success' => false, 'message' => 'Por favor, informe seu nome completo (mínimo 3 caracteres).'], Response::HTTP_BAD_REQUEST);
        }

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['success' => false, 'message' => 'Por favor, informe um endereço de e-mail válido (ex: nome@empresa.com.br).'], Response::HTTP_BAD_REQUEST);
        }

        $phoneDigits = preg_replace('/[^\d]/', '', $phone);
        if (strlen($phoneDigits) < 8) {
            return $this->json(['success' => false, 'message' => 'Por favor, informe um número de telefone/WhatsApp válido com DDD.'], Response::HTTP_BAD_REQUEST);
        }

        // Validação de CPF ou CNPJ
        if (strlen($cpfCnpjDigits) === 11) {
            if (!$this->validateCPF($cpfCnpjDigits)) {
                return $this->json(['success' => false, 'message' => 'O CPF informado é inválido. Verifique os dígitos.'], Response::HTTP_BAD_REQUEST);
            }
        } elseif (strlen($cpfCnpjDigits) === 14) {
            if (!$this->validateCNPJ($cpfCnpjDigits)) {
                return $this->json(['success' => false, 'message' => 'O CNPJ informado é inválido. Verifique os dígitos.'], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return $this->json(['success' => false, 'message' => 'Por favor, informe um CPF (11 dígitos) ou CNPJ (14 dígitos) válido.'], Response::HTTP_BAD_REQUEST);
        }

        // 1. Tenta enviar o e-mail via WMailer PRIMEIRO
        try {
            $fromEmail = $_SERVER['EMAIL_FROM'] ?? $_ENV['EMAIL_FROM'] ?? $_SERVER['MAIL_FROM'] ?? $_ENV['MAIL_FROM'] ?? $_SERVER['MAIL_SENDER'] ?? $_ENV['MAIL_SENDER'] ?? getenv('EMAIL_FROM') ?: 'no-reply@wab.com.br';

            $toEmail = $_SERVER['CONTACT_TO'] ?? $_ENV['CONTACT_TO'] ?? $_SERVER['EMAIL_CONTACT_TO'] ?? $_ENV['EMAIL_CONTACT_TO'] ?? getenv('CONTACT_TO') ?: 'tiago.simoes@pressmatik.com.br';

            $bccEmail = $_SERVER['MAIL_BCC'] ?? $_ENV['MAIL_BCC'] ?? getenv('MAIL_BCC') ?: null;

            $senderName = $_SERVER['MAIL_SENDER_NAME'] ?? $_ENV['MAIL_SENDER_NAME'] ?? getenv('MAIL_SENDER_NAME') ?: 'Pressmatik';

            $subject = ($type === 'quote' ? '[Cotação Site] ' : '[Contato Site] ') . ($productInterest ?: $name);

            $emailText = "Nova mensagem recebida pelo site Pressmatik:\n\n"
                . "• Nome: " . $name . "\n"
                . "• E-mail: " . $email . "\n"
                . "• Telefone: " . ($phone ?: 'Não informado') . "\n"
                . "• CPF/CNPJ: " . ($cpfCnpjDigits ?: 'Não informado') . "\n"
                . "• Empresa: " . ($company ?: 'Não informada') . "\n"
                . "• Produto/Interesse: " . ($productInterest ?: 'Não especificado') . "\n"
                . "• Tipo: " . ($type === 'quote' ? 'Solicitação de Cotação' : 'Mensagem de Contato') . "\n\n"
                . "Mensagem:\n" . ($message ?: 'Sem mensagem adicional') . "\n\n"
                . "Data de envio: " . (new \DateTime())->format('d/m/Y H:i:s');

            $emailObj = (new \Symfony\Component\Mime\Email())
                ->from(new \Symfony\Component\Mime\Address($fromEmail, $senderName))
                ->to($toEmail)
                ->replyTo(new \Symfony\Component\Mime\Address($email, $name))
                ->subject($subject)
                ->text($emailText);

            if ($bccEmail) {
                $emailObj->addBcc($bccEmail);
            }

            $mailer->send($emailObj);
        } catch (\Throwable $e) {
            $logger->error('Erro ao enviar email via WMailer: ' . $e->getMessage(), [
                'exception' => $e,
                'email' => $email,
                'name' => $name
            ]);

            return $this->json([
                'success' => false,
                'message' => 'Não foi possível entregar o e-mail de contato no momento. A mensagem não foi registrada. Tente novamente ou entre em contato via WhatsApp.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // 2. Salva no banco de dados SOMENTE se o e-mail foi enviado com sucesso
        $msg = new \App\Entity\ContactMessage();
        $msg->setName($name);
        $msg->setEmail($email);
        $msg->setPhone($phone);
        $msg->setCpfCnpj($cpfCnpjDigits);
        $msg->setCompany($company);
        $msg->setProductInterest($productInterest);
        $msg->setMessage($message);
        $msg->setType($type);
        $msg->setProductSlug($productSlug);

        $em->persist($msg);
        $em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Sua mensagem foi enviada por e-mail e gravada com sucesso! Nossa equipe entrará em contato em breve.'
        ]);
    }

    private function validateCPF(string $cpf): bool
    {
        $cpf = preg_replace('/[^\d]/', '', $cpf);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += (int) $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ((int) $cpf[$t] !== $d) {
                return false;
            }
        }
        return true;
    }

    private function validateCNPJ(string $cnpj): bool
    {
        $cnpj = preg_replace('/[^\d]/', '', $cnpj);
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0, $n = 0; $i < 12; $n += (int) $cnpj[$i] * $b[++$i]);
        if ((int) $cnpj[12] !== (($n %= 11) < 2 ? 0 : 11 - $n)) {
            return false;
        }

        for ($i = 0, $n = 0; $i < 13; $n += (int) $cnpj[$i] * $b[$i++]);
        if ((int) $cnpj[13] !== (($n %= 11) < 2 ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }
}
