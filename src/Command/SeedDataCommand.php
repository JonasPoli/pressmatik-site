<?php

namespace App\Command;

use App\Entity\AboutUs;
use App\Entity\Banner;
use App\Entity\Differential;
use App\Entity\HistoryTimeline;
use App\Entity\OrgChartItem;
use App\Entity\Product;
use App\Entity\ProductConfigItem;
use App\Entity\ProductSize;
use App\Entity\ProductSpecValue;
use App\Entity\ProductVideo;
use App\Entity\QualityCertification;
use App\Entity\Subproduct;
use App\Entity\Application;
use App\Entity\SuccessCase;
use App\Entity\Supplier;
use App\Entity\TechnicalSpecification;
use App\Entity\User;
use App\Entity\Testimony;
use App\Entity\ClientLogo;
use App\Entity\NewsCategory;
use App\Entity\News;
use App\Entity\Service;
use App\Entity\MegaMenuCategory;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-data',
    description: 'Seed initial data including admin user, products, technical specs, institutional pages, banners, differentials, suppliers, certifications, success cases, and org chart in 3 languages',
)]
class SeedDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserService $userService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Iniciando o Seeding de Dados - Pressmatik');

        // 1. Criar Usuário Administrador
        $io->section('1. Criando Usuário Administrador');
        $userRepo = $this->em->getRepository(User::class);
        $admin = $userRepo->findOneBy(['username' => 'admin']);

        if (!$admin) {
            $admin = new User();
            $admin->setUsername('admin');
            $admin->setEmail('admin@pressmatik.com.br');
            $admin->setName('Administrador Pressmatik');
            $admin->setRoles(['ROLE_ADMIN']);
            $this->userService->create($admin, 'wab12345678');
            $io->success('Usuário "admin" criado com a senha "wab12345678"!');
        } else {
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setName('Administrador Pressmatik');
            $this->userService->update($admin, 'wab12345678');
            $io->success('Usuário "admin" já existia. Senha e permissões atualizadas para "wab12345678"!');
        }

        // 2. Limpar dados anteriores
        $io->section('2. Limpando dados anteriores');
        
        $this->em->createQuery('DELETE FROM App\Entity\ProductSpecValue')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\ProductSize')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Subproduct')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Product')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Application')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Banner')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Differential')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Supplier')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\QualityCertification')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\SuccessCase')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\OrgChartItem')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\TechnicalSpecification')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\ProductConfigItem')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\ProductVideo')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\HistoryTimeline')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Testimony')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\ClientLogo')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\NewsImage')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\News')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\NewsCategory')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\ServiceImage')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Service')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\MegaMenuCategory')->execute();
        
        $io->text('Tabelas limpas com sucesso.');

        // 3. APPLICATIONS
        $io->section('3. Cadastrando Aplicações Globais');
        $this->seedApplications($io);

        // 3b. PRODUCTS & SUBPRODUCTS
        $io->section('3b. Cadastrando Produtos e Subprodutos');
        $this->seedProducts($io);

        // 4. TECHNICAL SPECS & SIZES
        $io->section('4. Cadastrando Especificações Técnicas');
        $specsEntities = $this->seedTechnicalSpecs($io);

        $io->section('5. Cadastrando Tamanhos e Tabela de Especificações');
        $this->seedProductSizesAndValues($io, $specsEntities);

        // 6. CONFIG ITEMS (Standard & Optional)
        $io->section('6. Cadastrando Equipamentos Standard e Opcionais');
        $this->seedConfigItems($io);

        // 7. VIDEOS
        $io->section('7. Cadastrando Vídeos de Operação');
        $this->seedVideos($io);

        // 8. ABOUT US
        $io->section('8. Cadastrando Quem Somos');
        $this->seedAboutUs($io);

        // 9. TIMELINE
        $io->section('9. Cadastrando Linha do Tempo');
        $this->seedTimeline($io);

        // 10. BANNERS
        $io->section('10. Cadastrando Banners');
        $this->seedBanners($io);

        // 11. DIFFERENTIALS
        $io->section('11. Cadastrando Diferenciais');
        $this->seedDifferentials($io);

        // 12. SUPPLIERS
        $io->section('12. Cadastrando Fornecedores');
        $this->seedSuppliers($io);

        // 13. QUALITY CERTIFICATIONS
        $io->section('13. Cadastrando Certificações de Qualidade');
        $this->seedQualityCertifications($io);

        // 14. SUCCESS CASES
        $io->section('14. Cadastrando Cases de Sucesso');
        $this->seedSuccessCases($io);

        // 15. ORG CHART
        $io->section('15. Cadastrando Organograma');
        $this->seedOrgChart($io);

        // 16. CLIENT LOGOS
        $io->section('16. Cadastrando Logos de Clientes');
        $this->seedClientLogos($io);

        // 17. TESTIMONIES
        $io->section('17. Cadastrando Depoimentos');
        $this->seedTestimonies($io);

        // 18. SERVICES
        $io->section('18. Cadastrando Serviços');
        $this->seedServices($io);

        // 19. NEWS
        $io->section('19. Cadastrando Notícias');
        $this->seedNews($io);

        // 20. MEGAMENU CATEGORIES
        $io->section('20. Cadastrando Categorias do MegaMenu');
        $this->seedMegaMenuCategories($io);

        $io->success('🎉 Seeding completo! Todos os dados inseridos com sucesso.');
        return Command::SUCCESS;
    }

    private function seedProducts(SymfonyStyle $io): void
    {
        $productsData = [
            // PRENSAS HIDRÁULICAS
            ['slug' => 'prensas-hidraulicas-tipo-c', 'category' => 'hydraulic', 'namePt' => 'Prensas Hidráulicas Tipo C', 'nameEn' => 'C-Frame Hydraulic Presses', 'nameEs' => 'Prensas Hidráulicas Tipo C', 'tonnage' => '25 ~ 160 Ton', 'hasSpecs' => true, 'pos' => 0,
                'descPt' => 'Linha completa de prensas hidráulicas tipo C, com estrutura monobloco em aço soldado, para operações de corte, estampagem, embutimento, dobra, montagem e rebarbação.',
                'descEn' => 'Complete line of C-frame hydraulic presses with monobloc welded steel structure for cutting, stamping, drawing, bending, assembly, and deburring operations.',
                'descEs' => 'Línea completa de prensas hidráulicas tipo C, con estructura monobloque de acero soldado, para operaciones de corte, estampado, embutición, plegado, montaje y desbarbado.',
                'image_src' => 'prensa hidráulica tipo “c” linha “st”/prensa hidráulica tipo “c” linha “st”.png',
                'subs' => [
                    ['model' => 'PMC-ST', 'namePt' => 'PMC-ST — Standard', 'nameEn' => 'PMC-ST — Standard', 'nameEs' => 'PMC-ST — Estándar', 'tag' => 'Standard'],
                    ['model' => 'PMC-BC', 'namePt' => 'PMC-BC — Bancada', 'nameEn' => 'PMC-BC — Bench', 'nameEs' => 'PMC-BC — Bancada', 'tag' => 'Compact'],
                    ['model' => 'PMC-GT', 'namePt' => 'PMC-GT — Mesa Giratória', 'nameEn' => 'PMC-GT — Rotary Table', 'nameEs' => 'PMC-GT — Mesa Giratoria', 'tag' => 'Rotary'],
                    ['model' => 'PMC-TR', 'namePt' => 'PMC-TR — Rebarbação', 'nameEn' => 'PMC-TR — Deflashing', 'nameEs' => 'PMC-TR — Rebarbación', 'tag' => 'Deflashing'],
                    ['model' => 'PMC-MT', 'namePt' => 'PMC-MT — Mesa Móvel', 'nameEn' => 'PMC-MT — Moving Table', 'nameEs' => 'PMC-MT — Mesa Móvil', 'tag' => 'Heavy Die'],
                    ['model' => 'PMC-AL', 'namePt' => 'PMC-AL — Alinhamento', 'nameEn' => 'PMC-AL — Alignment', 'nameEs' => 'PMC-AL — Alineamiento', 'tag' => 'Precision'],
                    ['model' => 'PMC-HZ', 'namePt' => 'PMC-HZ — Horizontal', 'nameEn' => 'PMC-HZ — Horizontal', 'nameEs' => 'PMC-HZ — Horizontal', 'tag' => 'Assembly'],
                    ['model' => 'PMC-ES', 'namePt' => 'PMC-ES — Especial', 'nameEn' => 'PMC-ES — Custom', 'nameEs' => 'PMC-ES — Especial', 'tag' => 'Custom'],
                ]
            ],
            ['slug' => 'prensas-hidraulicas-tipo-c-duplo', 'category' => 'hydraulic', 'namePt' => 'Prensas Hidráulicas Tipo C Duplo', 'nameEn' => 'Twin C-Frame Hydraulic Presses', 'nameEs' => 'Prensas Hidráulicas Tipo C Doble', 'tonnage' => '50 ~ 315 Ton', 'hasSpecs' => true, 'pos' => 1,
                'descPt' => 'Prensas de coluna dupla tipo C com maior rigidez estrutural, ideais para operações pesadas de conformação e estampagem.',
                'descEn' => 'Twin C-frame column presses with superior structural rigidity, ideal for heavy forming and stamping operations.',
                'descEs' => 'Prensas de doble columna tipo C con mayor rigidez estructural, ideales para operaciones pesadas de conformado y estampado.',
                'image_src' => 'prensa hidráulica tipo “C” duplo  - linha “St”/prensa hidráulica tipo “C” duplo  - linha “St”.png',
                'subs' => [
                    ['model' => 'PMCD-ST', 'namePt' => 'PMCD-ST — Standard', 'nameEn' => 'PMCD-ST — Standard', 'nameEs' => 'PMCD-ST — Estándar', 'tag' => 'Standard'],
                    ['model' => 'PMCD-GT', 'namePt' => 'PMCD-GT — Mesa Giratória', 'nameEn' => 'PMCD-GT — Rotary Table', 'nameEs' => 'PMCD-GT — Mesa Giratoria', 'tag' => 'Rotary'],
                    ['model' => 'PMCD-TR', 'namePt' => 'PMCD-TR — Rebarbação', 'nameEn' => 'PMCD-TR — Deflashing', 'nameEs' => 'PMCD-TR — Rebarbación', 'tag' => 'Deflashing'],
                    ['model' => 'PMCD-MT', 'namePt' => 'PMCD-MT — Mesa Móvel', 'nameEn' => 'PMCD-MT — Moving Table', 'nameEs' => 'PMCD-MT — Mesa Móvil', 'tag' => 'Heavy Die'],
                    ['model' => 'PMCD-BC', 'namePt' => 'PMCD-BC — Bancada', 'nameEn' => 'PMCD-BC — Bench', 'nameEs' => 'PMCD-BC — Bancada', 'tag' => 'Compact'],
                    ['model' => 'PMCD-ES', 'namePt' => 'PMCD-ES — Especial', 'nameEn' => 'PMCD-ES — Custom', 'nameEs' => 'PMCD-ES — Especial', 'tag' => 'Custom'],
                ]
            ],
            ['slug' => 'prensas-hidraulicas-4-colunas', 'category' => 'hydraulic', 'namePt' => 'Prensas Hidráulicas 4 Colunas', 'nameEn' => '4-Column Hydraulic Presses', 'nameEs' => 'Prensas Hidráulicas 4 Columnas', 'tonnage' => '60 ~ 2000 Ton', 'hasSpecs' => true, 'pos' => 2,
                'descPt' => 'Prensas de 4 colunas para aplicações de grande porte em conformação, repuxo, embutimento e calibração.',
                'descEn' => '4-column presses for large-scale forming, deep drawing, and calibration applications.',
                'descEs' => 'Prensas de 4 columnas para aplicaciones de gran escala en conformado, embutición profunda y calibración.',
                'image_src' => 'prensa HIDRÁULICA 4 COLUNAS - _LINHA PM4C - ST/prensa HIDRÁULICA 4 COLUNAS - _LINHA PM4C - ST.png',
                'subs' => [
                    ['model' => 'PM4C-ST', 'namePt' => 'PM4C-ST — Simples/Duplo Efeito', 'nameEn' => 'PM4C-ST — Single/Double Action', 'nameEs' => 'PM4C-ST — Simple/Doble Efecto', 'tag' => 'Standard'],
                    ['model' => 'PM4C-RP', 'namePt' => 'PM4C-RP — Paneleira', 'nameEn' => 'PM4C-RP — Deep Drawing', 'nameEs' => 'PM4C-RP — Embutición Profunda', 'tag' => 'Deep Draw'],
                    ['model' => 'PM4C-TR', 'namePt' => 'PM4C-TR — Rebarbação', 'nameEn' => 'PM4C-TR — Deflashing', 'nameEs' => 'PM4C-TR — Rebarbación', 'tag' => 'Deflashing'],
                    ['model' => 'PM4C-TY', 'namePt' => 'PM4C-TY — Try Out', 'nameEn' => 'PM4C-TY — Die Tryout', 'nameEs' => 'PM4C-TY — Prueba de Moldes', 'tag' => 'Tryout'],
                    ['model' => 'PM4C-PD', 'namePt' => 'PM4C-PD — Pastilhadeira', 'nameEn' => 'PM4C-PD — Tablet Press', 'nameEs' => 'PM4C-PD — Pastilladora', 'tag' => 'Tablet'],
                    ['model' => 'PMH4-CT', 'namePt' => 'PMH4-CT — Corte Não Metálicos', 'nameEn' => 'PMH4-CT — Non-Metal Cutting', 'nameEs' => 'PMH4-CT — Corte No Metálicos', 'tag' => 'Cutting'],
                    ['model' => 'PM4C-ES', 'namePt' => 'PM4C-ES — Especial', 'nameEn' => 'PM4C-ES — Custom', 'nameEs' => 'PM4C-ES — Especial', 'tag' => 'Custom'],
                ]
            ],
            ['slug' => 'prensas-hidraulicas-tipo-h', 'category' => 'hydraulic', 'namePt' => 'Prensas Hidráulicas Tipo H', 'nameEn' => 'H-Frame Hydraulic Presses', 'nameEs' => 'Prensas Hidráulicas Tipo H', 'tonnage' => '60 ~ 600 Ton', 'hasSpecs' => true, 'pos' => 3,
                'descPt' => 'Prensas tipo H com estrutura prismática guiada por 4 ou 8 pontos, para aplicações pesadas.',
                'descEn' => 'H-frame presses with prismatic structure guided by 4 or 8 points for heavy-duty applications.',
                'descEs' => 'Prensas tipo H con estructura prismática guiada por 4 u 8 puntos, para aplicaciones pesadas.',
                'image_src' => 'prensa HIDRÁULICA “H” pRISMÁTICA MARTELO _GUIADO POR 8 PONTOS  -  LINHA “PR”/prensa HIDRÁULICA “H” pRISMÁTICA MARTELO _GUIADO POR 8 PONTOS  -  LINHA “PR”.png',
                'subs' => [
                    ['model' => 'PMH-ST', 'namePt' => 'PMH-ST — Martelo Flutuante', 'nameEn' => 'PMH-ST — Floating Hammer', 'nameEs' => 'PMH-ST — Martillo Flotante', 'tag' => 'Standard'],
                    ['model' => 'PMH-PR', 'namePt' => 'PMH-PR — 4 e 8 Pontos', 'nameEn' => 'PMH-PR — 4 & 8 Point', 'nameEs' => 'PMH-PR — 4 y 8 Puntos', 'tag' => 'Multi-Point'],
                    ['model' => 'PMH-WK', 'namePt' => 'PMH-WK — Oficina', 'nameEn' => 'PMH-WK — Workshop', 'nameEs' => 'PMH-WK — Taller', 'tag' => 'Workshop'],
                    ['model' => 'PMH-WP', 'namePt' => 'PMH-WP — Pórtico', 'nameEn' => 'PMH-WP — Gantry', 'nameEs' => 'PMH-WP — Pórtico', 'tag' => 'Gantry'],
                    ['model' => 'PMH-VB', 'namePt' => 'PMH-VB — Vulcanização', 'nameEn' => 'PMH-VB — Vulcanization', 'nameEs' => 'PMH-VB — Vulcanización', 'tag' => 'Vulcanization'],
                    ['model' => 'PMH-WT', 'namePt' => 'PMH-WT — Montagem de Pneus', 'nameEn' => 'PMH-WT — Tire Assembly', 'nameEs' => 'PMH-WT — Montaje de Neumáticos', 'tag' => 'Tire'],
                    ['model' => 'PMH-ES', 'namePt' => 'PMH-ES — Especial', 'nameEn' => 'PMH-ES — Custom', 'nameEs' => 'PMH-ES — Especial', 'tag' => 'Custom'],
                ]
            ],
            ['slug' => 'prensas-hidraulicas-especiais', 'category' => 'hydraulic', 'namePt' => 'Prensas Hidráulicas Especiais', 'nameEn' => 'Custom Hydraulic Presses', 'nameEs' => 'Prensas Hidráulicas Especiales', 'tonnage' => 'Sob consulta', 'hasSpecs' => false, 'pos' => 4,
                'descPt' => 'Projetos especiais sob medida para aplicações específicas da indústria.',
                'descEn' => 'Custom-built special projects for specific industrial applications.',
                'descEs' => 'Proyectos especiales a medida para aplicaciones específicas de la industria.',
                'image_src' => 'linha “ES”/linha “ES”.png',
                'subs' => [
                    ['model' => 'PME-ES', 'namePt' => 'Prensa Especial Sob Consulta', 'nameEn' => 'Custom Press — Consult', 'nameEs' => 'Prensa Especial Bajo Consulta', 'tag' => 'Custom'],
                ]
            ],

            // SERVO-HIDRÁULICAS
            ['slug' => 'prensas-servo-hidraulicas-servo-bombas', 'category' => 'servo-hydraulic', 'namePt' => 'Prensas Servo-Hidráulicas', 'nameEn' => 'Servo-Hydraulic Presses', 'nameEs' => 'Prensas Servo-Hidráulicas', 'tonnage' => '100 ~ 2000 Ton', 'hasSpecs' => false, 'pos' => 0,
                'descPt' => 'Tecnologia servo-hidráulica com servo bombas para controle preciso de força, velocidade e posição.',
                'descEn' => 'Servo-hydraulic technology with servo pumps for precise force, speed, and position control.',
                'descEs' => 'Tecnología servo-hidráulica con servo bombas para control preciso de fuerza, velocidad y posición.',
                'image_src' => 'GT - MESA GIRATÓRIA/GT - MESA GIRATÓRIA.png',
                'subs' => [
                    ['model' => 'PMSB', 'namePt' => 'PMSB — Servo Bomba', 'nameEn' => 'PMSB — Servo Pump', 'nameEs' => 'PMSB — Servo Bomba', 'tag' => 'Servo'],
                ]
            ],

            // MECÂNICAS
            ['slug' => 'prensas-mecanicas-mecanicas', 'category' => 'mechanical', 'namePt' => 'Prensas Mecânicas Convencionais', 'nameEn' => 'Conventional Mechanical Presses', 'nameEs' => 'Prensas Mecánicas Convencionales', 'tonnage' => '25 ~ 630 Ton', 'hasSpecs' => false, 'pos' => 0,
                'descPt' => 'Prensas mecânicas de alta cadência para estampagem progressiva, corte e conformação.',
                'descEn' => 'High-speed mechanical presses for progressive stamping, cutting, and forming.',
                'descEs' => 'Prensas mecánicas de alta cadencia para estampado progresivo, corte y conformado.',
                'image_src' => 'puncionadeira/puncionadeira.png',
                'subs' => [
                    ['model' => 'QS-ST', 'namePt' => 'ST-Series — C-Frame', 'nameEn' => 'ST-Series — C-Frame', 'nameEs' => 'ST-Series — Tipo C', 'tag' => 'C-Frame'],
                    ['model' => 'QS-STC', 'namePt' => 'STC-Series — Straight Side', 'nameEn' => 'STC-Series — Straight Side', 'nameEs' => 'STC-Series — Lateral Recto', 'tag' => 'Straight'],
                ]
            ],
            ['slug' => 'prensas-mecanicas-servo', 'category' => 'mechanical', 'namePt' => 'Prensas Mecânicas Servo', 'nameEn' => 'Servo Mechanical Presses', 'nameEs' => 'Prensas Mecánicas Servo', 'tonnage' => '110 ~ 2500 Ton', 'hasSpecs' => false, 'pos' => 1,
                'descPt' => 'Prensas mecânicas com acionamento servo para controle total de curso e velocidade.',
                'descEn' => 'Servo-driven mechanical presses for full stroke and speed control.',
                'descEs' => 'Prensas mecánicas con accionamiento servo para control total de carrera y velocidad.',
                'image_src' => 'puncionadeira/puncionadeira.png',
                'subs' => [
                    ['model' => 'QS-STA', 'namePt' => 'STA-Series Servo', 'nameEn' => 'STA-Series Servo', 'nameEs' => 'STA-Series Servo', 'tag' => 'Servo'],
                ]
            ],
            ['slug' => 'prensas-mecanicas-alta-velocidade', 'category' => 'mechanical', 'namePt' => 'Prensas Mecânicas Alta Velocidade', 'nameEn' => 'High-Speed Mechanical Presses', 'nameEs' => 'Prensas Mecánicas Alta Velocidad', 'tonnage' => '35 ~ 300 Ton', 'hasSpecs' => false, 'pos' => 2,
                'descPt' => 'Prensas de alta velocidade para produção de alto volume em estampagem progressiva.',
                'descEn' => 'High-speed presses for high-volume progressive stamping production.',
                'descEs' => 'Prensas de alta velocidad para producción de alto volumen en estampado progresivo.',
                'image_src' => 'puncionadeira/puncionadeira.png',
                'subs' => [
                    ['model' => 'QS-STS', 'namePt' => 'STS-Series High Speed', 'nameEn' => 'STS-Series High Speed', 'nameEs' => 'STS-Series Alta Velocidad', 'tag' => 'High Speed'],
                ]
            ],

            // EQUIPAMENTOS
            ['slug' => 'equipamentos-yokes', 'category' => 'equipments', 'namePt' => 'Yokes — Rebitagem e Puncionamento', 'nameEn' => 'Yokes — Riveting & Punching', 'nameEs' => 'Yokes — Remachado y Punzonado', 'tonnage' => '5 ~ 30 Ton', 'hasSpecs' => false, 'pos' => 0,
                'descPt' => 'Equipamentos de rebitagem e puncionamento para montagem industrial.',
                'descEn' => 'Riveting and punching equipment for industrial assembly.',
                'descEs' => 'Equipos de remachado y punzonado para montaje industrial.',
                'image_src' => 'linha yoke/linha yoke.png',
                'subs' => [
                    ['model' => 'YK-RB', 'namePt' => 'Yoke Rebitagem', 'nameEn' => 'Riveting Yoke', 'nameEs' => 'Yoke Remachado', 'tag' => 'Riveting'],
                    ['model' => 'YK-PN', 'namePt' => 'Yoke Puncionamento', 'nameEn' => 'Punching Yoke', 'nameEs' => 'Yoke Punzonado', 'tag' => 'Punching'],
                ]
            ],
            ['slug' => 'equipamentos-unidades-forca', 'category' => 'equipments', 'namePt' => 'Unidades de Força Hidráulica', 'nameEn' => 'Hydraulic Power Units', 'nameEs' => 'Unidades de Fuerza Hidráulica', 'tonnage' => '—', 'hasSpecs' => false, 'pos' => 1,
                'descPt' => 'Unidades hidráulicas compactas e standard para aplicações industriais diversas.',
                'descEn' => 'Compact and standard hydraulic units for various industrial applications.',
                'descEs' => 'Unidades hidráulicas compactas y estándar para aplicaciones industriales diversas.',
                'image_src' => 'UNIDADES  HIDRÁULICAS/UNIDADES  HIDRÁULICAS.png',
                'subs' => [
                    ['model' => 'UF-MN', 'namePt' => 'Mini Unidade', 'nameEn' => 'Mini Unit', 'nameEs' => 'Mini Unidad', 'tag' => 'Mini'],
                    ['model' => 'UF-ST', 'namePt' => 'Unidade Standard', 'nameEn' => 'Standard Unit', 'nameEs' => 'Unidad Estándar', 'tag' => 'Standard'],
                    ['model' => 'UF-ES', 'namePt' => 'Unidade Especial', 'nameEn' => 'Custom Unit', 'nameEs' => 'Unidad Especial', 'tag' => 'Custom'],
                ]
            ],
            ['slug' => 'equipamentos-cilindros', 'category' => 'equipments', 'namePt' => 'Cilindros Hidráulicos', 'nameEn' => 'Hydraulic Cylinders', 'nameEs' => 'Cilindros Hidráulicos', 'tonnage' => '—', 'hasSpecs' => false, 'pos' => 2,
                'descPt' => 'Cilindros normatizados e especiais para integração em máquinas e sistemas.',
                'descEn' => 'Standardized and custom cylinders for machine and system integration.',
                'descEs' => 'Cilindros normalizados y especiales para integración en máquinas y sistemas.',
                'image_src' => 'cilindros hidráulicos/cilindros hidráulicos_.png',
                'subs' => [
                    ['model' => 'CL-NR', 'namePt' => 'Cilindro Normatizado', 'nameEn' => 'Standardized Cylinder', 'nameEs' => 'Cilindro Normalizado', 'tag' => 'Standard'],
                    ['model' => 'CL-ES', 'namePt' => 'Cilindro Especial', 'nameEn' => 'Custom Cylinder', 'nameEs' => 'Cilindro Especial', 'tag' => 'Custom'],
                ]
            ],
            ['slug' => 'equipamentos-transferencia', 'category' => 'equipments', 'namePt' => 'Transferência e Filtração', 'nameEn' => 'Transfer & Filtration', 'nameEs' => 'Transferencia y Filtración', 'tonnage' => '—', 'hasSpecs' => false, 'pos' => 3,
                'descPt' => 'Sistemas de transferência e filtração de fluidos hidráulicos.',
                'descEn' => 'Hydraulic fluid transfer and filtration systems.',
                'descEs' => 'Sistemas de transferencia y filtración de fluidos hidráulicos.',
                'image_src' => 'UNIDADE de transferência _para FLuidos hidráulicos/UNIDADE de transferência _para FLuidos hidráulicos.png',
                'subs' => [
                    ['model' => 'TF-ST', 'namePt' => 'Sistema de Transferência', 'nameEn' => 'Transfer System', 'nameEs' => 'Sistema de Transferencia', 'tag' => 'Transfer'],
                ]
            ],

            // PEÇAS E ACESSÓRIOS
            ['slug' => 'pecas-hidraulica', 'category' => 'parts', 'namePt' => 'Hidráulica — Bombas, Válvulas, Vedações', 'nameEn' => 'Hydraulics — Pumps, Valves, Seals', 'nameEs' => 'Hidráulica — Bombas, Válvulas, Sellos', 'tonnage' => '—', 'hasSpecs' => false, 'pos' => 0,
                'descPt' => 'Componentes hidráulicos: bombas, válvulas, vedações e filtros.',
                'descEn' => 'Hydraulic components: pumps, valves, seals, and filters.',
                'descEs' => 'Componentes hidráulicos: bombas, válvulas, sellos y filtros.',
                'image_src' => 'peças e acessórios/peças e acessórios_.png',
                'subs' => [
                    ['model' => 'PH-BV', 'namePt' => 'Bombas e Válvulas', 'nameEn' => 'Pumps & Valves', 'nameEs' => 'Bombas y Válvulas', 'tag' => 'Hydraulics'],
                ]
            ],
            ['slug' => 'pecas-eletroeletronica', 'category' => 'parts', 'namePt' => 'Eletroeletrônica — Automação e Potência', 'nameEn' => 'Electronics — Automation & Power', 'nameEs' => 'Electrónica — Automatización y Potencia', 'tonnage' => '—', 'hasSpecs' => false, 'pos' => 1,
                'descPt' => 'Componentes elétricos e eletrônicos para automação e potência industrial.',
                'descEn' => 'Electrical and electronic components for industrial automation and power.',
                'descEs' => 'Componentes eléctricos y electrónicos para automatización y potencia industrial.',
                'image_src' => 'peças e acessórios/peças e acessórios_.png',
                'subs' => [
                    ['model' => 'PE-AP', 'namePt' => 'Automação e Potência', 'nameEn' => 'Automation & Power', 'nameEs' => 'Automatización y Potencia', 'tag' => 'Electronics'],
                ]
            ],
            ['slug' => 'pecas-acessorios', 'category' => 'parts', 'namePt' => 'Acessórios Diversos', 'nameEn' => 'Miscellaneous Accessories', 'nameEs' => 'Accesorios Diversos', 'tonnage' => '—', 'hasSpecs' => false, 'pos' => 2,
                'descPt' => 'Acessórios NR12, peças de reposição e itens complementares.',
                'descEn' => 'NR12 accessories, spare parts, and complementary items.',
                'descEs' => 'Accesorios NR12, piezas de repuesto e ítems complementarios.',
                'image_src' => 'peças e acessórios/peças e acessórios_.png',
                'subs' => [
                    ['model' => 'AC-NR', 'namePt' => 'Acessórios NR12', 'nameEn' => 'NR12 Accessories', 'nameEs' => 'Accesorios NR12', 'tag' => 'Safety'],
                    ['model' => 'AC-DV', 'namePt' => 'Acessórios Diversos', 'nameEn' => 'Misc Accessories', 'nameEs' => 'Accesorios Diversos', 'tag' => 'Misc'],
                ]
            ],
        ];

        foreach ($productsData as $p) {
            $product = new Product();
            $product->setSlug($p['slug']);
            $product->setCategory($p['category']);
            $product->setNamePt($p['namePt']);
            $product->setNameEn($p['nameEn']);
            $product->setNameEs($p['nameEs']);
            $product->setDescriptionPt($p['descPt']);
            $product->setDescriptionEn($p['descEn']);
            $product->setDescriptionEs($p['descEs']);
            $product->setTonnage($p['tonnage']);
            $product->setPosition($p['pos']);
            $product->setIsActive(true);
            $product->setHasSpecs($p['hasSpecs']);

            // Copy and associate client-provided product image
            if (isset($p['image_src'])) {
                $targetFilename = basename($p['image_src']);
                $srcPath = $this->findFileRecursively(__DIR__ . '/../../seed/products', $targetFilename);
                if ($srcPath && file_exists($srcPath)) {
                    $destDir = __DIR__ . '/../../public/uploads/products';
                    if (!is_dir($destDir)) {
                        mkdir($destDir, 0777, true);
                    }
                    $fileName = $p['slug'] . '.png';
                    copy($srcPath, $destDir . '/' . $fileName);
                    $product->setImageName($fileName);
                }
            }

            $this->em->persist($product);
            $this->em->flush(); // need ID for subproducts

            $firstSub = null;

            foreach ($p['subs'] as $sIdx => $s) {
                $sub = new Subproduct();
                $sub->setProduct($product);
                $sub->setModel($s['model']);
                $sub->setNamePt($s['namePt']);
                $sub->setNameEn($s['nameEn'] ?? $s['namePt']);
                $sub->setNameEs($s['nameEs'] ?? $s['namePt']);
                $sub->setTag($s['tag'] ?? null);
                $sub->setPosition($sIdx);
                $sub->setIsActive(true);
                
                // Copy specific subproduct model image if exists, else fallback to category image
                $specificImage = strtolower($sub->getModel()) . '.png';
                $srcSubPath = $this->findFileRecursively(__DIR__ . '/../../seed/products', $specificImage);
                if ($srcSubPath && file_exists($srcSubPath)) {
                    $destDir = __DIR__ . '/../../public/uploads/products';
                    if (!is_dir($destDir)) {
                        mkdir($destDir, 0777, true);
                    }
                    $fileName = strtolower($sub->getModel()) . '-sub.png';
                    copy($srcSubPath, $destDir . '/' . $fileName);
                    $sub->setImageName($fileName);
                } elseif ($product->getImageName()) {
                    $sub->setImageName($product->getImageName());
                }

                $this->em->persist($sub);

                // Copy and associate subproduct PDF catalog if mapped
                $pdfMapping = [
                    'PMC-ST' => 'A1-PMC-ST.pdf',
                    'PMC-BC' => 'A3 - PMC-BC.pdf',
                    'PMC-GT' => 'A4 - PMC-GT.pdf',
                    'PMC-TR' => 'A5 - PMC-TR.pdf',
                    'PMC-MT' => 'A6 - PMC-MT.pdf',
                    'PMC-AL' => 'A7 - PMC-AL.pdf',
                    'PMC-HZ' => 'A8 - PMC-HZ.pdf',
                    'PMCD-ST' => 'B1 - PMCD-ST.pdf',
                    'PMCD-BC' => 'B3 - PMCD-BC.pdf',
                    'PMCD-GT' => 'B4 - PMCD-GT.pdf',
                    'PMCD-TR' => 'B5 - PMCD_TR.pdf',
                    'PMCD-MT' => 'B6 PMCD-MT.pdf',
                    'PMH-ST' => 'C1 - PMH-ST.pdf',
                    'PMH-PR' => 'C2-PMH-PR.pdf',
                    'PMH-WP' => 'C3 - PMH - WP.pdf',
                    'PMH-WK' => 'C4-PMH-WK.pdf',
                    'PMH-VB' => 'C5 - PMH -VB.pdf',
                    'PM4C-ST' => 'D1 - PM4C-ST.pdf',
                    'PM4C-RP' => 'D2 - PM4C-RP.pdf',
                    'PM4C-TR' => 'D3 - PM4C-TR.pdf',
                    'PM4C-TY' => 'D4 - PM4C - TY.pdf',
                ];

                $pdfFileName = $pdfMapping[$sub->getModel()] ?? null;
                if ($pdfFileName) {
                    $srcPdfPath = $this->findFileRecursively(__DIR__ . '/../../seed/pdf', $pdfFileName);
                    if ($srcPdfPath && file_exists($srcPdfPath)) {
                        $destDir = __DIR__ . '/../../public/uploads/products';
                        if (!is_dir($destDir)) {
                            mkdir($destDir, 0777, true);
                        }
                        $safePdfName = strtolower($sub->getModel()) . '-catalog-pt.pdf';
                        copy($srcPdfPath, $destDir . '/' . $safePdfName);
                        $sub->setPdfNamePt($safePdfName);
                    }
                }

                // Map the 19 applications to subproducts
                $subproductApps = [
                    'PMC-ST'  => ['Corte', 'Estampagem', 'Dobra', 'Rebarbação', 'Repuxo', 'Duplo efeito', 'Montagem', 'Progressivo'],
                    'PMC-SM'  => ['Corte', 'Estampagem', 'Dobra', 'Rebarbação', 'Repuxo', 'Progressivo', 'Montagem'],
                    'PMC-BC'  => ['Corte', 'Estampagem', 'Dobra', 'Rebarbação', 'Repuxo', 'Duplo efeito', 'Montagem', 'Progressivo'],
                    'PMC-GT'  => ['Rebarbação', 'Montagem', 'Forjamento', 'Mesa giratória'],
                    'PMC-TR'  => ['Rebarbação'],
                    'PMC-MT'  => ['Rebarbação', 'Montagem'],
                    'PMC-AL'  => ['Endireitamento'],
                    'PMC-HZ'  => ['Montagem', 'Endireitamento'],
                    'PMCD-ST' => ['Corte', 'Estampagem', 'Dobra', 'Rebarbação', 'Repuxo', 'Duplo efeito', 'Montagem', 'Progressivo'],
                    'PMCD-SM' => ['Corte', 'Estampagem', 'Dobra', 'Rebarbação', 'Repuxo', 'Duplo efeito', 'Montagem', 'Progressivo'],
                    'PMCD-BC' => ['Corte', 'Estampagem', 'Dobra', 'Rebarbação', 'Repuxo', 'Duplo efeito', 'Montagem', 'Progressivo'],
                    'PMCD-GT' => ['Rebarbação', 'Montagem', 'Forjamento', 'Mesa giratória'],
                    'PMCD-TR' => ['Rebarbação'],
                    'PMCD-MT' => ['Rebarbação', 'Montagem'],
                    'PMH-ST'  => ['Corte', 'Estampagem', 'Dobra', 'Rebarbação', 'Repuxo', 'Compactação', 'Montagem', 'Moldagem'],
                    'PMH-PR'  => ['Corte', 'Estampagem', 'Dobra', 'Rebarbação', 'Repuxo', 'Compactação', 'Montagem', 'Moldagem'],
                    'PMH-WP'  => ['Montagem', 'Desmontagem', 'Endireitamento'],
                    'PMH-WK'  => ['Montagem', 'Desmontagem', 'Dobra', 'Endireitamento'],
                    'PMH-VB'  => ['Vulcanização', 'Moldagem', 'Transfer'],
                    'PM4C-ST' => ['Corte', 'Estampagem', 'Dobra', 'Rebarbação', 'Repuxo', 'Progressivo', 'Montagem'],
                    'PM4C-RP' => ['Repuxo profundo', 'Repuxo invertido'],
                    'PM4C-TR' => ['Rebarbação / calibração'],
                    'PM4C-TY' => ['Corte', 'Estampagem', 'Dobra', 'Rebarbação', 'Repuxo', 'Montagem'],
                ];

                // Load applications index for fast linking
                static $normalizedApps = null;
                if ($normalizedApps === null) {
                    $normalizedApps = [];
                    $allSeededApps = $this->em->getRepository(Application::class)->findAll();
                    foreach ($allSeededApps as $app) {
                        $norm = $this->normalizeString($app->getNamePt());
                        $normalizedApps[$norm] = $app;
                    }
                }

                $subAppsList = $subproductApps[$sub->getModel()] ?? [];
                foreach ($subAppsList as $appName) {
                    $normName = $this->normalizeString($appName);
                    if (isset($normalizedApps[$normName])) {
                        $sub->addApplication($normalizedApps[$normName]);
                    }
                }

                if ($sIdx === 0) $firstSub = $sub;
            }
            $this->em->flush();

            if ($firstSub) {
                $product->setDefaultSubproduct($firstSub);
                $this->em->flush();
            }

            $io->text("  ✓ {$p['namePt']} ({$p['slug']}) + " . count($p['subs']) . " subprodutos");
        }
    }

    private function seedTechnicalSpecs(SymfonyStyle $io): array
    {
        $specsData = [
            ['namePt' => 'Força de Prensagem', 'nameEn' => 'Pressing Force', 'nameEs' => 'Fuerza de Prensado', 'unitPt' => 't', 'unitEn' => 't', 'unitEs' => 't', 'position' => 1],
            ['namePt' => 'Força de Retorno', 'nameEn' => 'Return Force', 'nameEs' => 'Fuerza de Retorno', 'unitPt' => 't', 'unitEn' => 't', 'unitEs' => 't', 'position' => 2],
            ['namePt' => 'Velocidade de Avanço Rápido', 'nameEn' => 'Fast Forward Speed', 'nameEs' => 'Velocidad de Avance Rápido', 'unitPt' => 'mm/s', 'unitEn' => 'mm/s', 'unitEs' => 'mm/s', 'position' => 3],
            ['namePt' => 'Velocidade de Trabalho', 'nameEn' => 'Working Speed', 'nameEs' => 'Velocidad de Trabajo', 'unitPt' => 'mm/s', 'unitEn' => 'mm/s', 'unitEs' => 'mm/s', 'position' => 4],
            ['namePt' => 'Velocidade de Retorno', 'nameEn' => 'Return Speed', 'nameEs' => 'Velocidad de Retorno', 'unitPt' => 'mm/s', 'unitEn' => 'mm/s', 'unitEs' => 'mm/s', 'position' => 5],
            ['namePt' => 'Abertura Mesa x Martelo', 'nameEn' => 'Daylight', 'nameEs' => 'Apertura Mesa x Martillo', 'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 6],
            ['namePt' => 'Curso do Cilindro', 'nameEn' => 'Cylinder Stroke', 'nameEs' => 'Carrera del Cilindro', 'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 7],
            ['namePt' => 'Mesa (Frente x Profundidade)', 'nameEn' => 'Table (Front x Depth)', 'nameEs' => 'Mesa (Frente x Profundidad)', 'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 8],
            ['namePt' => 'Saída de Cavaco', 'nameEn' => 'Chip Exit', 'nameEs' => 'Salida de Viruta', 'unitPt' => 'Ø', 'unitEn' => 'Ø', 'unitEs' => 'Ø', 'position' => 9],
            ['namePt' => 'Martelo (Frente x Profundidade)', 'nameEn' => 'Hammer (Front x Depth)', 'nameEs' => 'Martillo (Frente x Profundidad)', 'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 10],
            ['namePt' => 'Furo para Espiga', 'nameEn' => 'Shank Holder', 'nameEs' => 'Agujero para Espiga', 'unitPt' => 'Ø', 'unitEn' => 'Ø', 'unitEs' => 'Ø', 'position' => 11],
            ['namePt' => 'Centro do Pistão até a Estrutura', 'nameEn' => 'Throat', 'nameEs' => 'Centro del Pistón a la Estructura', 'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 12],
            ['namePt' => 'Vão Frontal da Grade (LxA)', 'nameEn' => 'Front Grille Gap (WxH)', 'nameEs' => 'Apertura de la Rejilla Frontal (LxA)', 'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 13],
            ['namePt' => 'Motor', 'nameEn' => 'Motor', 'nameEs' => 'Motor', 'unitPt' => 'CV', 'unitEn' => 'HP', 'unitEs' => 'CV', 'position' => 14],
            ['namePt' => 'Capacidade de Óleo (AW68)', 'nameEn' => 'Oil Capacity (AW68)', 'nameEs' => 'Capacidad de Aceite (AW68)', 'unitPt' => 'l', 'unitEn' => 'l', 'unitEs' => 'l', 'position' => 15],
            ['namePt' => 'Largura (Frontal)', 'nameEn' => 'Width (Front)', 'nameEs' => 'Ancho (Frontal)', 'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 16],
            ['namePt' => 'Profundidade (Lateral)', 'nameEn' => 'Depth (Side)', 'nameEs' => 'Profundidad (Lateral)', 'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 17],
            ['namePt' => 'Altura', 'nameEn' => 'Height', 'nameEs' => 'Altura', 'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 18],
            ['namePt' => 'Peso Aproximado', 'nameEn' => 'Approximate Weight', 'nameEs' => 'Peso Aproximado', 'unitPt' => 'Kg', 'unitEn' => 'Kg', 'unitEs' => 'Kg', 'position' => 19],
        ];

        $specsEntities = [];
        foreach ($specsData as $s) {
            $spec = new TechnicalSpecification();
            $spec->setNamePt($s['namePt']);
            $spec->setNameEn($s['nameEn']);
            $spec->setNameEs($s['nameEs']);
            $spec->setUnitPt($s['unitPt']);
            $spec->setUnitEn($s['unitEn']);
            $spec->setUnitEs($s['unitEs']);
            $spec->setPosition($s['position']);
            $this->em->persist($spec);
            $specsEntities[$s['namePt']] = $spec;
        }
        $this->em->flush();
        $io->text('Especificações técnicas inseridas.');
        return $specsEntities;
    }

    private function seedProductSizesAndValues(SymfonyStyle $io, array $specsEntities): void
    {
        $jsonPath = __DIR__ . '/../../docs/seed_data.json';
        if (!file_exists($jsonPath)) {
            $io->warning('Arquivo seed_data.json não encontrado — pulando spec values.');
            return;
        }

        $jsonData = json_decode(file_get_contents($jsonPath), true);

        $slugToModel = [
            'prensas-hidraulicas-tipo-c' => 'PMC-ST',
            'prensas-hidraulicas-tipo-c-duplo' => 'PMCD-ST',
            'prensas-hidraulicas-tipo-h' => 'PMH-ST',
            'prensas-hidraulicas-4-colunas' => 'PM4C-ST',
        ];

        foreach ($jsonData as $slug => $productData) {
            $model = $slugToModel[$slug] ?? null;
            if (!$model) {
                $io->warning("Slug {$slug} não possui mapeamento de subproduto.");
                continue;
            }

            $subproduct = $this->em->getRepository(Subproduct::class)->findOneBy(['model' => $model]);
            if (!$subproduct) {
                $io->warning("Subproduto com modelo {$model} não encontrado no banco.");
                continue;
            }

            $io->text("Processando tamanhos para subproduto: {$model}");
            $sizesList = $productData['sizes'] ?? [];
            $valuesList = $productData['values'] ?? [];

            $dbSizes = [];
            $pos = 0;
            foreach ($sizesList as $s) {
                $size = new ProductSize();
                $size->setSubproduct($subproduct);
                $size->setName($s['name']);
                $size->setHasVType($s['hasV']);
                $size->setHasHType($s['hasH']);
                $size->setPosition($pos++);
                $this->em->persist($size);
                $dbSizes[$s['name']] = $size;
            }
            $this->em->flush();

            $rowIndex = 0;
            foreach ($valuesList as $specName => $sizeValues) {
                $spec = $specsEntities[$specName] ?? null;
                if (!$spec) {
                    $spec = new TechnicalSpecification();
                    $spec->setNamePt($specName);
                    $spec->setNameEn($specName);
                    $spec->setNameEs($specName);
                    $spec->setUnitPt('—');
                    $spec->setUnitEn('—');
                    $spec->setUnitEs('—');
                    $spec->setPosition(99);
                    $this->em->persist($spec);
                    $this->em->flush();
                    $specsEntities[$specName] = $spec;
                }

                foreach ($sizeValues as $sizeName => $vals) {
                    $size = $dbSizes[$sizeName] ?? null;
                    if (!$size) continue;

                    $val = new ProductSpecValue();
                    $val->setSubproduct($subproduct);
                    $val->setSpecification($spec);
                    $val->setProductSize($size);
                    $val->setVTypeValue($vals['v'] ?? null);
                    $val->setHTypeValue($vals['h'] ?? null);
                    $val->setPosition($rowIndex);
                    $this->em->persist($val);
                }
                $rowIndex++;
            }
            $this->em->flush();
        }

        $io->text('Tamanhos e Parâmetros Técnicos de Subprodutos inseridos.');
    }

    private function seedConfigItems(SymfonyStyle $io): void
    {
        $configs = [
            ['type' => 'standard', 'pt' => 'Bloco Manifold monitorado - cat.4', 'en' => 'Monitored Manifold Block - Cat. 4', 'es' => 'Bloque Manifold monitoreado - cat.4', 'pos' => 0],
            ['type' => 'standard', 'pt' => 'CLP de segurança', 'en' => 'Safety PLC', 'es' => 'CLP de seguridad', 'pos' => 1],
            ['type' => 'standard', 'pt' => 'Calço de segurança mecânico monitorado', 'en' => 'Monitored mechanical safety block', 'es' => 'Calzo de seguridad mecánico monitoreado', 'pos' => 2],
            ['type' => 'standard', 'pt' => 'Cortina de luz de segurança', 'en' => 'Safety light curtain', 'es' => 'Cortina de luz de seguridad', 'pos' => 3],
            ['type' => 'standard', 'pt' => 'Grade de proteção lateral e traseira', 'en' => 'Side and rear protective guard', 'es' => 'Rejilla de protección lateral y trasera', 'pos' => 4],
            ['type' => 'standard', 'pt' => 'Console bimanual com simultaneidade', 'en' => 'Two-handed control console with simultaneity', 'es' => 'Consola bimanual con simultaneidad', 'pos' => 5],
            ['type' => 'optional', 'pt' => 'CLP+IHM (Programação de receitas)', 'en' => 'PLC+HMI (Recipe programming)', 'es' => 'CLP+IHM (Programación de recetas)', 'pos' => 0],
            ['type' => 'optional', 'pt' => 'Válvulas proporcionais de vazão e pressão', 'en' => 'Proportional flow and pressure valves', 'es' => 'Válvulas proporcionales de caudal y presión', 'pos' => 1],
            ['type' => 'optional', 'pt' => 'Célula de carga (Controle de força)', 'en' => 'Load cell (Force control)', 'es' => 'Célula de carga (Control de fuerza)', 'pos' => 2],
            ['type' => 'optional', 'pt' => 'Cilindro extrator ou almofada hidráulica', 'en' => 'Extractor cylinder or hydraulic cushion', 'es' => 'Cilindro extractor o cojín hidráulico', 'pos' => 3],
            ['type' => 'optional', 'pt' => 'Mesa móvel ou mesa giratória', 'en' => 'Mobile table or rotating table', 'es' => 'Mesa móvil o mesa giratoria', 'pos' => 4],
        ];

        $slugs = ['prensas-hidraulicas-tipo-c', 'prensas-hidraulicas-tipo-c-duplo', 'prensas-hidraulicas-4-colunas', 'prensas-hidraulicas-tipo-h'];
        foreach ($configs as $c) {
            foreach ($slugs as $productSlug) {
                $item = new ProductConfigItem();
                $item->setProductSlug($productSlug);
                $item->setType($c['type']);
                $item->setNamePt($c['pt']);
                $item->setNameEn($c['en']);
                $item->setNameEs($c['es']);
                $item->setPosition($c['pos']);
                $this->em->persist($item);
            }
        }
        $this->em->flush();
        $io->text('Equipamentos Standard e Opcionais inseridos.');
    }

    private function seedVideos(SymfonyStyle $io): void
    {
        $videos = [
            ['titlePt' => 'Demonstração Prensa Hidráulica Tipo C', 'titleEn' => 'C-Frame Hydraulic Press Demonstration', 'titleEs' => 'Demostración Prensa Hidráulica Tipo C', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['titlePt' => 'Operação de Estampagem Industrial', 'titleEn' => 'Industrial Stamping Operation', 'titleEs' => 'Operación de Estampado Industrial', 'url' => 'https://www.qiaosenpresses.com/uploads/Deep-Throat-Presses.mp4'],
        ];

        $slugs = ['prensas-hidraulicas-tipo-c', 'prensas-hidraulicas-tipo-c-duplo', 'prensas-hidraulicas-4-colunas', 'prensas-hidraulicas-tipo-h'];
        foreach ($videos as $idx => $v) {
            foreach ($slugs as $productSlug) {
                $video = new ProductVideo();
                $video->setProductSlug($productSlug);
                $video->setTitlePt($v['titlePt']);
                $video->setTitleEn($v['titleEn']);
                $video->setTitleEs($v['titleEs']);
                $video->setUrl($v['url']);
                $video->setPosition($idx);
                $this->em->persist($video);
            }
        }
        $this->em->flush();
        $io->text('Vídeos de operação inseridos.');
    }

    private function seedAboutUs(SymfonyStyle $io): void
    {
        $aboutUsRepo = $this->em->getRepository(AboutUs::class);
        $about = $aboutUsRepo->findOrCreate();

        $about->setTitlePt('Quem Somos');
        $about->setTitleEn('About Us');
        $about->setTitleEs('Quiénes Somos');

        $about->setSubtitlePt('Líder no desenvolvimento de prensas e sistemas de automação industrial');
        $about->setSubtitleEn('Leader in the development of industrial presses and automation systems');
        $about->setSubtitleEs('Líder en el desarrollo de prensas y sistemas de automatización industrial');

        $about->setDescriptionPt(
            "A Pressmatik é líder no desenvolvimento de prensas hidráulicas e servo-hidráulicas industriais pesadas. Nossas soluções aliam engenharia avançada, segurança rígida em conformidade com as normas NR10 e NR12, e o uso de componentes globais de ponta para entregar máxima eficiência energética e produtividade sem vazamentos ou lentidão.\n\n" .
            "Atendendo as mais rigorosas exigências dos principais mercados mundiais, hoje contamos com máquinas e equipamentos exportados para vários países, incluindo USA, México, Argentina, Chile, Colômbia, Peru, Áustria, Turquia, entre outros."
        );
        $about->setDescriptionEn(
            "Pressmatik is a leader in developing heavy-duty industrial hydraulic and servo-hydraulic presses. Our solutions combine advanced engineering, strict safety compliance with NR10 and NR12 regulations, and the integration of top-tier global components to deliver maximum energy efficiency and high productivity without oil leaks or slowdowns.\n\n" .
            "Meeting strict global standards, today we have machines and equipment exported to various countries, including USA, Mexico, Argentina, Chile, Colombia, Peru, Austria, Turkey, among others."
        );
        $about->setDescriptionEs(
            "Pressmatik es líder en el desarrollo de prensas hidráulicas y servo-hidráulicas industriales pesadas. Nuestras soluciones combinan ingeniería avanzada, estricto cumplimiento de las normas de seguridad NR10 y NR12, e integración de componentes globales de primera calidad para ofrecer la máxima eficiencia energética y productividad sin fugas de aceite ni lentitud.\n\n" .
            "Cumpliendo con los estrictos estándares mundiales, hoy contamos con máquinas y equipos exportados a varios países, incluyendo USA, México, Argentina, Chile, Colombia, Perú, Austria, Turquía, entre otros."
        );

        $about->setMissionPt('Desenvolver e entregar soluções industriais de conformidade rígida e tecnologia avançada, garantindo máxima produtividade, eficiência energética e segurança operacional.');
        $about->setMissionEn('Develop and deliver industrial solutions of strict compliance and advanced technology, ensuring maximum productivity, energy efficiency, and operational safety.');
        $about->setMissionEs('Desarrollar y entregar soluciones industriales de estricto cumplimiento y tecnología avanzada, garantizando la máxima productividad, eficiencia energética y seguridad operacional.');

        $about->setVisionPt('Ser referência global em engenharia de prensas hidráulicas e sistemas industriais customizados de alta performance.');
        $about->setVisionEn('To be a global reference in engineering of hydraulic presses and high-performance customized industrial systems.');
        $about->setVisionEs('Ser referencia global en ingeniería de prensas hidráulicas y sistemas industriales personalizados de alto rendimiento.');

        $about->setValuesPt("• Inovação tecnológica contínua\n• Segurança operacional absoluta (NR10 / NR12)\n• Compromisso com a qualidade e prazos\n• Parceria de longo prazo com clientes e fornecedores\n• Integridade e ética em todas as relações");
        $about->setValuesEn("• Continuous technological innovation\n• Absolute operational safety (NR10 / NR12)\n• Commitment to quality and deadlines\n• Long-term partnership with clients and suppliers\n• Integrity and ethics in all relationships");
        $about->setValuesEs("• Innovación tecnológica continua\n• Seguridad operacional absoluta (NR10 / NR12)\n• Compromiso con la calidad y plazos\n• Alianza a largo prazo con clientes y proveedores\n• Integridad y ética en todas las relaciones");

        $about->setAdvantage1TitlePt('Segurança Total (NR10 & NR12)');
        $about->setAdvantage1TitleEn('Total Safety (NR10 & NR12)');
        $about->setAdvantage1TitleEs('Seguridad Total (NR10 y NR12)');
        $about->setAdvantage1DescPt('Sistemas projetados de fábrica sob estrita conformidade de segurança para proteção total dos operadores.');
        $about->setAdvantage1DescEn('Systems designed from the ground up under strict safety compliance for complete operator protection.');
        $about->setAdvantage1DescEs('Sistemas diseñados desde la fábrica bajo estricto cumplimiento de seguridad para la protección total del operador.');
        $about->setAdvantage1Icon('fa-solid fa-shield-halved');

        $about->setAdvantage2TitlePt('Componentes Premium');
        $about->setAdvantage2TitleEn('Premium Components');
        $about->setAdvantage2TitleEs('Componentes Premium');
        $about->setAdvantage2DescPt('Integração com tecnologia de líderes mundiais como Bosch Rexroth, Siemens e Weg.');
        $about->setAdvantage2DescEn('Integration with technology from world leaders like Bosch Rexroth, Siemens, and Weg.');
        $about->setAdvantage2DescEs('Integración con tecnología de líderes mundiales como Bosch Rexroth, Siemens y Weg.');
        $about->setAdvantage2Icon('fa-solid fa-microchip');

        $about->setAdvantage3TitlePt('Alta Performance');
        $about->setAdvantage3TitleEn('High Performance');
        $about->setAdvantage3TitleEs('Alto Rendimiento');
        $about->setAdvantage3DescPt('Substituição de tecnologia mecânica antiga por sistemas servo-hidráulicos de setup rápido.');
        $about->setAdvantage3DescEn('Efficient replacement of legacy mechanical technology with fast-setup servo-hydraulic systems.');
        $about->setAdvantage3DescEs('Reemplazo eficiente de tecnología mecánica antigua por sistemas servo-hidráulicos de configuración rápida.');
        $about->setAdvantage3Icon('fa-solid fa-gauge-high');

        $about->setAdvantage4TitlePt('Exportação Global');
        $about->setAdvantage4TitleEn('Global Export');
        $about->setAdvantage4TitleEs('Exportación Global');
        $about->setAdvantage4DescPt('Atendimento às rigorosas exigências mundiais, exportando para USA, Europa e América Latina.');
        $about->setAdvantage4DescEn('Meeting strict global standards, exporting to the USA, Europe, and Latin America.');
        $about->setAdvantage4DescEs('Cumpliendo con los estrictos estándares mundiales, exportando a USA, Europa y América Latina.');
        $about->setAdvantage4Icon('fa-solid fa-earth-americas');

        $about->setHomeTextPt('Há mais de uma década fabricando prensas hidráulicas, servo-hidráulicas e equipamentos industriais com segurança total e tecnologia de ponta.');
        $about->setHomeTextEn('Over a decade manufacturing hydraulic, servo-hydraulic presses and industrial equipment with total safety and cutting-edge technology.');
        $about->setHomeTextEs('Más de una década fabricando prensas hidráulicas, servo-hidráulicas y equipos industriales con seguridad total y tecnología de punta.');

        $destDir = __DIR__ . '/../../public/uploads/about';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $homeImgSrc = __DIR__ . '/../../seed/about/about-press.png';
        if (file_exists($homeImgSrc)) {
            copy($homeImgSrc, $destDir . '/about-press.png');
            $about->setHomeImageName('about-press.png');
        }

        $bannerImgSrc = __DIR__ . '/../../seed/about/hero-bg.jpg';
        if (file_exists($bannerImgSrc)) {
            copy($bannerImgSrc, $destDir . '/hero-bg.jpg');
            $about->setBannerImageName('hero-bg.jpg');
        }

        $this->em->flush();
        $io->text('Quem Somos configurado.');
    }

    private function seedTimeline(SymfonyStyle $io): void
    {
        $timelineData = [
            ['year' => '2010', 'titlePt' => 'Fundação da Pressmatik', 'titleEn' => 'Founding of Pressmatik', 'titleEs' => 'Fundación de Pressmatik', 'descPt' => 'Início das atividades em Araraquara-SP, focada em manutenção e reforma de prensas excêntricas e hidráulicas.', 'descEn' => 'Beginning of activities in Araraquara-SP, focusing on maintenance and retrofit of eccentric and hydraulic presses.', 'descEs' => 'Inicio de actividades en Araraquara-SP, enfocada en mantenimiento y reforma de prensas excéntricas e hidráulicas.', 'pos' => 0],
            ['year' => '2018', 'titlePt' => 'Lançamento da Linha de Prensas Tipo C', 'titleEn' => 'Launch of C-Frame Presses Line', 'titleEs' => 'Lanzamiento de la línea de Prensas Tipo C', 'descPt' => 'Desenvolvimento e comercialização da linha PMC-ST, com foco em segurança NR12.', 'descEn' => 'Development and commercialization of the PMC-ST line, focusing on NR12 safety regulations.', 'descEs' => 'Desarrollo y comercialización de la línea PMC-ST, con enfoque en la segurança NR12.', 'pos' => 1],
            ['year' => '2026', 'titlePt' => 'Parceria Qiaosen e P/A Brasil', 'titleEn' => 'Qiaosen and P/A Brazil Partnership', 'titleEs' => 'Alianza con Qiaosen y P/A Brasil', 'descPt' => 'Consolidação como distribuidor de prensas mecânicas Qiaosen e sistemas de alimentação P/A no Brasil.', 'descEn' => 'Consolidation as distributor of Qiaosen mechanical presses and P/A feed systems in Brazil.', 'descEs' => 'Consolidación como distribuidor de prensas mecánicas Qiaosen y sistemas de alimentación P/A en Brasil.', 'pos' => 2],
        ];

        foreach ($timelineData as $t) {
            $item = new HistoryTimeline();
            $item->setTitlePt($t['titlePt']);
            $item->setTitleEn($t['titleEn']);
            $item->setTitleEs($t['titleEs']);
            $item->setDescriptionPt($t['descPt']);
            $item->setDescriptionEn($t['descEn']);
            $item->setDescriptionEs($t['descEs']);
            $item->setEventDate(new \DateTime($t['year'] . '-01-01'));
            $item->setPosition($t['pos']);
            $this->em->persist($item);
        }
        $this->em->flush();
        $io->text('Linha do Tempo inserida.');
    }

    private function seedBanners(SymfonyStyle $io): void
    {
        $banners = [
            ['titlePt' => 'Prensas Hidráulicas de Alta Performance', 'titleEn' => 'High Performance Hydraulic Presses', 'titleEs' => 'Prensas Hidráulicas de Alto Rendimiento', 'subtitlePt' => 'Segurança NR12 de fábrica com tecnologia Siemens e Bosch Rexroth', 'subtitleEn' => 'Factory NR12 safety with Siemens and Bosch Rexroth technology', 'subtitleEs' => 'Seguridad NR12 de fábrica con tecnología Siemens y Bosch Rexroth', 'buttonTextPt' => 'Conheça nossos produtos', 'buttonTextEn' => 'Explore our products', 'buttonTextEs' => 'Conozca nuestros productos', 'buttonUrl' => '/pt/produtos/prensas-hidraulicas-tipo-c', 'pos' => 0, 'logo' => 'banner1.png'],
            ['titlePt' => 'Servo-Hidráulicas — Tecnologia de Ponta', 'titleEn' => 'Servo-Hydraulic — Cutting-Edge Technology', 'titleEs' => 'Servo-Hidráulicas — Tecnología de Punta', 'subtitlePt' => 'Controle preciso de força, velocidade e posição', 'subtitleEn' => 'Precise force, speed, and position control', 'subtitleEs' => 'Control preciso de fuerza, velocidad y posición', 'buttonTextPt' => 'Saiba mais', 'buttonTextEn' => 'Learn more', 'buttonTextEs' => 'Conozca más', 'buttonUrl' => '/pt/produtos/prensas-servo-hidraulicas-servo-bombas', 'pos' => 1, 'logo' => 'banner2.png'],
            ['titlePt' => 'Exportação para mais de 15 países', 'titleEn' => 'Exporting to over 15 countries', 'titleEs' => 'Exportación a más de 15 países', 'subtitlePt' => 'Qualidade industrial brasileira reconhecida mundialmente', 'subtitleEn' => 'Globally recognized Brazilian industrial quality', 'subtitleEs' => 'Calidad industrial brasileña reconocida mundialmente', 'buttonTextPt' => 'Quem Somos', 'buttonTextEn' => 'About Us', 'buttonTextEs' => 'Quiénes Somos', 'buttonUrl' => '/pt/quem-somos', 'pos' => 2, 'logo' => 'banner3.png'],
        ];

        $destDir = __DIR__ . '/../../public/uploads/banners';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        foreach ($banners as $b) {
            $banner = new Banner();
            $banner->setTitlePt($b['titlePt']);
            $banner->setTitleEn($b['titleEn']);
            $banner->setTitleEs($b['titleEs']);
            $banner->setSubtitlePt($b['subtitlePt']);
            $banner->setSubtitleEn($b['subtitleEn']);
            $banner->setSubtitleEs($b['subtitleEs']);
            $banner->setButtonTextPt($b['buttonTextPt']);
            $banner->setButtonTextEn($b['buttonTextEn']);
            $banner->setButtonTextEs($b['buttonTextEs']);
            $banner->setButtonUrl($b['buttonUrl']);
            $banner->setPosition($b['pos']);
            $banner->setIsActive(true);

            $srcFile = __DIR__ . '/../../seed/banners/' . $b['logo'];
            if (file_exists($srcFile)) {
                copy($srcFile, $destDir . '/' . $b['logo']);
                $banner->setImageName($b['logo']);
            }

            $this->em->persist($banner);
        }
        $this->em->flush();
        $io->text(count($banners) . ' banners inseridos.');
    }

    private function seedDifferentials(SymfonyStyle $io): void
    {
        $diffs = [
            ['icon' => 'fa-solid fa-shield-halved', 'titlePt' => 'Segurança Avançada NR12', 'titleEn' => 'Advanced NR12 Safety', 'titleEs' => 'Seguridad Avanzada NR12', 'descPt' => 'Todas as prensas saem de fábrica com sistemas de segurança em conformidade total com NR10 e NR12.', 'descEn' => 'All presses leave the factory with safety systems in full compliance with NR10 and NR12.', 'descEs' => 'Todas las prensas salen de fábrica con sistemas de seguridad en total conformidad con NR10 y NR12.'],
            ['icon' => 'fa-solid fa-microchip', 'titlePt' => 'Engenharia e Tecnologia Siemens', 'titleEn' => 'Siemens Engineering & Technology', 'titleEs' => 'Ingeniería y Tecnología Siemens', 'descPt' => 'Automação industrial com CLPs e IHMs Siemens para controle preciso e conectividade Indústria 4.0.', 'descEn' => 'Industrial automation with Siemens PLCs and HMIs for precise control and Industry 4.0 connectivity.', 'descEs' => 'Automatización industrial con CLPs y IHMs Siemens para control preciso y conectividad Industria 4.0.'],
            ['icon' => 'fa-solid fa-gears', 'titlePt' => 'Componentes Bosch Rexroth', 'titleEn' => 'Bosch Rexroth Components', 'titleEs' => 'Componentes Bosch Rexroth', 'descPt' => 'Circuitos hidráulicos com tecnologia Bosch Rexroth para máxima eficiência e durabilidade.', 'descEn' => 'Hydraulic circuits with Bosch Rexroth technology for maximum efficiency and durability.', 'descEs' => 'Circuitos hidráulicos con tecnología Bosch Rexroth para máxima eficiencia y durabilidad.'],
            ['icon' => 'fa-solid fa-bolt', 'titlePt' => 'Eficiência Energética', 'titleEn' => 'Energy Efficiency', 'titleEs' => 'Eficiencia Energética', 'descPt' => 'Motores WEG de alta eficiência com consumo até 40% menor que tecnologias convencionais.', 'descEn' => 'WEG high-efficiency motors with up to 40% less consumption than conventional technologies.', 'descEs' => 'Motores WEG de alta eficiencia con consumo hasta 40% menor que tecnologías convencionales.'],
            ['icon' => 'fa-solid fa-earth-americas', 'titlePt' => 'Exportação Global', 'titleEn' => 'Global Export', 'titleEs' => 'Exportación Global', 'descPt' => 'Máquinas exportadas para mais de 15 países nas Américas, Europa e Ásia.', 'descEn' => 'Machines exported to over 15 countries in the Americas, Europe, and Asia.', 'descEs' => 'Máquinas exportadas a más de 15 países en las Américas, Europa y Asia.'],
            ['icon' => 'fa-solid fa-headset', 'titlePt' => 'Suporte Técnico Especializado', 'titleEn' => 'Specialized Technical Support', 'titleEs' => 'Soporte Técnico Especializado', 'descPt' => 'Equipe de engenharia dedicada para suporte na instalação, manutenção e treinamento.', 'descEn' => 'Dedicated engineering team for installation support, maintenance, and training.', 'descEs' => 'Equipo de ingeniería dedicado para soporte en instalación, mantenimiento y capacitación.'],
        ];

        foreach ($diffs as $i => $d) {
            $diff = new Differential();
            $diff->setIcon($d['icon']);
            $diff->setTitlePt($d['titlePt']);
            $diff->setTitleEn($d['titleEn']);
            $diff->setTitleEs($d['titleEs']);
            $diff->setDescriptionPt($d['descPt']);
            $diff->setDescriptionEn($d['descEn']);
            $diff->setDescriptionEs($d['descEs']);
            $diff->setPosition($i);
            $diff->setIsActive(true);
            $this->em->persist($diff);
        }
        $this->em->flush();
        $io->text(count($diffs) . ' diferenciais inseridos.');
    }

    private function seedSuppliers(SymfonyStyle $io): void
    {
        $suppliers = [
            ['name' => 'ArcelorMittal', 'url' => 'https://www.arcelormittal.com.br', 'logo' => 'arcelomital.png'],
            ['name' => 'Atos', 'url' => 'https://www.atos.net', 'logo' => 'atos.png'],
            ['name' => 'Gerdau', 'url' => 'https://www.gerdau.com.br', 'logo' => 'gerdau.png'],
            ['name' => 'Gefran', 'url' => 'https://www.gefran.com', 'logo' => 'gerfran.png'],
            ['name' => 'Hercules', 'url' => 'https://www.motoreshercules.com.br', 'logo' => 'hercules.png'],
            ['name' => 'Hydac', 'url' => 'https://www.hydac.com', 'logo' => 'hydac.png'],
            ['name' => 'IFM Electronic', 'url' => 'https://www.ifm.com', 'logo' => 'ifm.png'],
            ['name' => 'Keyence', 'url' => 'https://www.keyence.com.br', 'logo' => 'keyence.png'],
            ['name' => 'Metaltex', 'url' => 'https://www.metaltex.com.br', 'logo' => 'metaltex.png'],
            ['name' => 'P/A Brasil', 'url' => 'https://www.pabrasil.com.br', 'logo' => 'pa brasil.png'],
            ['name' => 'Bosch Rexroth', 'url' => 'https://www.boschrexroth.com', 'logo' => 'rexroth.png'],
            ['name' => 'Schmersal', 'url' => 'https://www.schmersal.com.br', 'logo' => 'schmersal.png'],
            ['name' => 'Sick', 'url' => 'https://www.sick.com', 'logo' => 'sick.png'],
            ['name' => 'Siemens', 'url' => 'https://www.siemens.com', 'logo' => 'siemens.png'],
            ['name' => 'Taktomak', 'url' => 'https://www.taktomak.com.br', 'logo' => 'taktomak.png'],
            ['name' => 'WEG', 'url' => 'https://www.weg.net', 'logo' => 'weg.png'],
        ];

        $destDir = __DIR__ . '/../../public/uploads/suppliers';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        foreach ($suppliers as $i => $s) {
            $supplier = new Supplier();
            $supplier->setName($s['name']);
            $supplier->setWebsiteUrl($s['url']);
            $supplier->setPosition($i);
            $supplier->setIsActive(true);

            $srcFile = __DIR__ . '/../../seed/fornecedores/' . $s['logo'];
            if (file_exists($srcFile)) {
                copy($srcFile, $destDir . '/' . $s['logo']);
                $supplier->setImageName($s['logo']);
            }

            $this->em->persist($supplier);
        }
        $this->em->flush();
        $io->text(count($suppliers) . ' fornecedores inseridos.');
    }

    private function seedQualityCertifications(SymfonyStyle $io): void
    {
        $certs = [
            [
                'titlePt' => 'Segurança NR-12',
                'titleEn' => 'NR-12 Safety Standard',
                'titleEs' => 'Seguridad NR-12',
                'descPt' => 'Todas as prensas saem de fábrica em conformidade total com a norma NR-12.',
                'descEn' => 'All presses leave the factory in full compliance with NR-12 standards.',
                'descEs' => 'Todas las prensas salen de fábrica en total conformidad con la norma NR-12.',
                'logo' => 'nr12.png'
            ],
            [
                'titlePt' => 'Marcação CE',
                'titleEn' => 'CE Marking',
                'titleEs' => 'Marcado CE',
                'descPt' => 'Certificação europeia de segurança, saúde e proteção ambiental.',
                'descEn' => 'European certification for safety, health, and environmental protection.',
                'descEs' => 'Certificación europea de seguridad, salud y protección ambiental.',
                'logo' => 'CE.png'
            ],
            [
                'titlePt' => 'Cartão BNDES',
                'titleEn' => 'BNDES Card',
                'titleEs' => 'Tarjeta BNDES',
                'descPt' => 'Financiamento facilitado em até 48 vezes com taxas atrativas.',
                'descEn' => 'Facilitated financing in up to 48 installments with attractive rates.',
                'descEs' => 'Financiación facilitada en hasta 48 cuotas con tasas atractivas.',
                'logo' => 'bndes.png'
            ],
            [
                'titlePt' => 'Financiamento FINAME',
                'titleEn' => 'FINAME Financing',
                'titleEs' => 'Financiamiento FINAME',
                'descPt' => 'Linha de crédito especial para aquisição de máquinas nacionais.',
                'descEn' => 'Special credit line for purchasing national machines.',
                'descEs' => 'Línea de crédito especial para la adquisición de máquinas nacionales.',
                'logo' => 'finame.png'
            ],
            [
                'titlePt' => 'Exportação ApexBrasil',
                'titleEn' => 'ApexBrasil Export Support',
                'titleEs' => 'Apoyo de Exportación ApexBrasil',
                'descPt' => 'Apoio na promoção de exportações brasileiras de bens e serviços.',
                'descEn' => 'Support in promoting Brazilian exports of goods and services.',
                'descEs' => 'Apoyo en la promoção de exportaciones brasileñas de bienes y servicios.',
                'logo' => 'apex-do-brasil.png'
            ],
            [
                'titlePt' => 'Programa PEIEX',
                'titleEn' => 'PEIEX Program',
                'titleEs' => 'Programa PEIEX',
                'descPt' => 'Qualificação para exportação e inserção competitiva no mercado internacional.',
                'descEn' => 'Qualification for export and competitive insertion in the international market.',
                'descEs' => 'Calificación para la exportación e inserción competitiva en el mercado internacional.',
                'logo' => 'peiex.png'
            ],
        ];

        $destDir = __DIR__ . '/../../public/uploads/quality';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        foreach ($certs as $i => $c) {
            $cert = new QualityCertification();
            $cert->setTitlePt($c['titlePt']);
            $cert->setTitleEn($c['titleEn']);
            $cert->setTitleEs($c['titleEs']);
            $cert->setDescriptionPt($c['descPt']);
            $cert->setDescriptionEn($c['descEn']);
            $cert->setDescriptionEs($c['descEs']);
            $cert->setPosition($i);
            $cert->setIsActive(true);

            $srcFile = __DIR__ . '/../../seed/qualidade/' . $c['logo'];
            if (file_exists($srcFile)) {
                copy($srcFile, $destDir . '/' . $c['logo']);
                $cert->setImageName($c['logo']);
            }

            $this->em->persist($cert);
        }
        $this->em->flush();
        $io->text(count($certs) . ' certificações inseridas.');
    }

    private function seedSuccessCases(SymfonyStyle $io): void
    {
        $cases = [
            ['titlePt' => 'Linha de Estampagem Automotiva', 'titleEn' => 'Automotive Stamping Line', 'titleEs' => 'Línea de Estampado Automotriz', 'descPt' => 'Fornecimento de 3 prensas PMC-ST 160 ton para linha de estampagem progressiva em fábrica automotiva.', 'descEn' => 'Supply of 3 PMC-ST 160 ton presses for progressive stamping line in automotive factory.', 'descEs' => 'Suministro de 3 prensas PMC-ST 160 ton para línea de estampado progresivo en fábrica automotriz.', 'client' => 'Indústria Automotiva', 'industry' => 'Automotivo'],
            ['titlePt' => 'Projeto de Vulcanização de Borracha', 'titleEn' => 'Rubber Vulcanization Project', 'titleEs' => 'Proyecto de Vulcanización de Caucho', 'descPt' => 'Prensa PMH-VB 400 ton customizada para processo de vulcanização de peças técnicas em borracha.', 'descEn' => 'PMH-VB 400 ton press customized for technical rubber parts vulcanization process.', 'descEs' => 'Prensa PMH-VB 400 ton personalizada para proceso de vulcanización de piezas técnicas de caucho.', 'client' => 'Indústria de Borracha', 'industry' => 'Borracha'],
            ['titlePt' => 'Exportação para Turquia', 'titleEn' => 'Export to Turkey', 'titleEs' => 'Exportación a Turquía', 'descPt' => 'Prensas 4 colunas PM4C-ST exportadas para fábrica de conformação metálica na Turquia.', 'descEn' => '4-column PM4C-ST presses exported to metal forming factory in Turkey.', 'descEs' => 'Prensas 4 columnas PM4C-ST exportadas a fábrica de conformado metálico en Turquía.', 'client' => 'Metal Forming Co.', 'industry' => 'Metalúrgico'],
        ];

        foreach ($cases as $i => $c) {
            $sc = new SuccessCase();
            $sc->setTitlePt($c['titlePt']);
            $sc->setTitleEn($c['titleEn']);
            $sc->setTitleEs($c['titleEs']);
            $sc->setDescriptionPt($c['descPt']);
            $sc->setDescriptionEn($c['descEn']);
            $sc->setDescriptionEs($c['descEs']);
            $sc->setClientName($c['client']);
            $sc->setClientIndustry($c['industry']);
            $sc->setPosition($i);
            $sc->setIsActive(true);
            $this->em->persist($sc);
        }
        $this->em->flush();
        $io->text(count($cases) . ' cases de sucesso inseridos.');
    }

    private function seedOrgChart(SymfonyStyle $io): void
    {
        $items = [
            ['icon' => 'fa-solid fa-building', 'titlePt' => 'Diretoria', 'titleEn' => 'Board of Directors', 'titleEs' => 'Directorio', 'descPt' => 'Direção estratégica e tomada de decisões corporativas.', 'descEn' => 'Strategic direction and corporate decision-making.', 'descEs' => 'Dirección estratégica y toma de decisiones corporativas.'],
            ['icon' => 'fa-solid fa-drafting-compass', 'titlePt' => 'Engenharia e Projetos', 'titleEn' => 'Engineering & Design', 'titleEs' => 'Ingeniería y Proyectos', 'descPt' => 'Desenvolvimento de projetos mecânicos, hidráulicos e elétricos.', 'descEn' => 'Mechanical, hydraulic, and electrical project development.', 'descEs' => 'Desarrollo de proyectos mecánicos, hidráulicos y eléctricos.'],
            ['icon' => 'fa-solid fa-industry', 'titlePt' => 'Produção e Montagem', 'titleEn' => 'Production & Assembly', 'titleEs' => 'Producción y Montaje', 'descPt' => 'Fabricação, usinagem, soldagem e montagem final das máquinas.', 'descEn' => 'Manufacturing, machining, welding, and final machine assembly.', 'descEs' => 'Fabricación, mecanizado, soldadura y montaje final de las máquinas.'],
            ['icon' => 'fa-solid fa-bolt', 'titlePt' => 'Elétrica e Automação', 'titleEn' => 'Electrical & Automation', 'titleEs' => 'Eléctrica y Automatización', 'descPt' => 'Montagem de painéis elétricos e programação de CLPs e IHMs.', 'descEn' => 'Electrical panel assembly and PLC/HMI programming.', 'descEs' => 'Montaje de paneles eléctricos y programación de CLPs e IHMs.'],
            ['icon' => 'fa-solid fa-certificate', 'titlePt' => 'Qualidade e Segurança', 'titleEn' => 'Quality & Safety', 'titleEs' => 'Calidad y Seguridad', 'descPt' => 'Controle de qualidade e conformidade com normas NR10, NR12 e ISO.', 'descEn' => 'Quality control and compliance with NR10, NR12, and ISO standards.', 'descEs' => 'Control de calidad y conformidad con normas NR10, NR12 e ISO.'],
            ['icon' => 'fa-solid fa-handshake', 'titlePt' => 'Comercial e Exportação', 'titleEn' => 'Sales & Export', 'titleEs' => 'Comercial y Exportación', 'descPt' => 'Atendimento comercial nacional e internacional.', 'descEn' => 'National and international commercial support.', 'descEs' => 'Atención comercial nacional e internacional.'],
        ];

        foreach ($items as $i => $item) {
            $org = new OrgChartItem();
            $org->setIcon($item['icon']);
            $org->setTitlePt($item['titlePt']);
            $org->setTitleEn($item['titleEn']);
            $org->setTitleEs($item['titleEs']);
            $org->setDescriptionPt($item['descPt']);
            $org->setDescriptionEn($item['descEn']);
            $org->setDescriptionEs($item['descEs']);
            $org->setPosition($i);
            $this->em->persist($org);
        }
        $this->em->flush();
        $io->text(count($items) . ' itens do organograma inseridos.');
    }

    private function seedApplications(SymfonyStyle $io): void
    {
        $srcAppDir = __DIR__ . '/../../seed/aplicacoes';
        $destAppDir = __DIR__ . '/../../public/uploads/applications';
        
        if (!is_dir($destAppDir)) {
            mkdir($destAppDir, 0777, true);
        }

        $apps = [
            ['pt' => 'Compactação', 'en' => 'Compacting', 'es' => 'Compactación', 'file' => 'COMPACTAÇÃO.png', 'dest' => 'compactacao.png'],
            ['pt' => 'Corte', 'en' => 'Cutting', 'es' => 'Corte', 'file' => 'corte.png', 'dest' => 'corte.png'],
            ['pt' => 'Desmontagem', 'en' => 'Disassembly', 'es' => 'Desmontaje', 'file' => 'DESMONTAGEM.png', 'dest' => 'desmontagem.png'],
            ['pt' => 'Dobra', 'en' => 'Bending', 'es' => 'Dobra', 'file' => 'dobra.png', 'dest' => 'dobra.png'],
            ['pt' => 'Duplo efeito', 'en' => 'Double action', 'es' => 'Doble efecto', 'file' => 'duplo efeito.png', 'dest' => 'duplo-efeito.png'],
            ['pt' => 'Endireitamento', 'en' => 'Straightening', 'es' => 'Enderezado', 'file' => 'ENDIREITAMENTO.png', 'dest' => 'endireitamento.png'],
            ['pt' => 'Estampagem', 'en' => 'Stamping', 'es' => 'Estampado', 'file' => 'estampagem.png', 'dest' => 'estampagem.png'],
            ['pt' => 'Forjamento', 'en' => 'Forging', 'es' => 'Forjado', 'file' => 'FORJAMENTO.png', 'dest' => 'forjamento.png'],
            ['pt' => 'Mesa giratória', 'en' => 'Rotary table', 'es' => 'Mesa giratoria', 'file' => 'MESA GIRATÓRIA.png', 'dest' => 'mesa-giratoria.png'],
            ['pt' => 'Moldagem', 'en' => 'Molding', 'es' => 'Moldeo', 'file' => 'MOLDAGEM.png', 'dest' => 'moldagem.png'],
            ['pt' => 'Montagem', 'en' => 'Assembly', 'es' => 'Montaje', 'file' => 'montagem.png', 'dest' => 'montagem.png'],
            ['pt' => 'Progressivo', 'en' => 'Progressive', 'es' => 'Progresivo', 'file' => 'progressivo.png', 'dest' => 'progressivo.png'],
            ['pt' => 'Rebarbação', 'en' => 'Deburring', 'es' => 'Rebabado', 'file' => 'rebarbação.png', 'dest' => 'rebarbacao.png'],
            ['pt' => 'Rebarbação / calibração', 'en' => 'Deburring / calibration', 'es' => 'Rebabado / calibración', 'file' => 'REBARBAÇÃO CALIBRAÇÃO.png', 'dest' => 'rebarbacao-calibracao.png'],
            ['pt' => 'Repuxo', 'en' => 'Drawing', 'es' => 'Embutido', 'file' => 'repuxo.png', 'dest' => 'repuxo.png'],
            ['pt' => 'Repuxo invertido', 'en' => 'Inverted drawing', 'es' => 'Embutido invertido', 'file' => 'REPUXO INVERTIDO.png', 'dest' => 'repuxo-invertido.png'],
            ['pt' => 'Repuxo profundo', 'en' => 'Deep drawing', 'es' => 'Embutido profundo', 'file' => 'REPUXO PROFUNDO.png', 'dest' => 'repuxo-profundo.png'],
            ['pt' => 'Transfer', 'en' => 'Transfer', 'es' => 'Transfer', 'file' => 'TRANSFER.png', 'dest' => 'transfer.png'],
            ['pt' => 'Vulcanização', 'en' => 'Vulcanization', 'es' => 'Vulcanización', 'file' => 'VULCANIZAÇÃO.png', 'dest' => 'vulcanizacao.png'],
        ];

        foreach ($apps as $idx => $a) {
            $entity = new Application();
            $entity->setNamePt($a['pt']);
            $entity->setNameEn($a['en']);
            $entity->setNameEs($a['es']);
            $entity->setPosition($idx);
            $entity->setIsActive(true);

            $srcFile = $this->findFileRecursively($srcAppDir, $a['file']);
            if ($srcFile && file_exists($srcFile)) {
                copy($srcFile, $destAppDir . '/' . $a['dest']);
                $entity->setImageName($a['dest']);
            }

            $this->em->persist($entity);
        }

        $this->em->flush();
        $io->text(count($apps) . ' aplicações globais cadastradas e copiadas.');
    }

    private function seedClientLogos(SymfonyStyle $io): void
    {
        $clients = [
            ['name' => 'Cestari', 'logo' => 'cestari.png'],
            ['name' => 'CNH Industrial', 'logo' => 'cnh.png'],
            ['name' => 'Condutec', 'logo' => 'condutec.png'],
            ['name' => 'Facchini', 'logo' => 'facchini.png'],
            ['name' => 'Joframa', 'logo' => 'joframa.png'],
            ['name' => 'Lonil', 'logo' => 'lonil.png'],
            ['name' => 'Marcon', 'logo' => 'marcon.png'],
            ['name' => 'Maxion', 'logo' => 'maxion.png'],
            ['name' => 'Suporte Rei', 'logo' => 'suporte rei.png'],
        ];

        $destDir = __DIR__ . '/../../public/uploads/clients';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        foreach ($clients as $i => $c) {
            $clientLogo = new ClientLogo();
            $clientLogo->setName($c['name']);
            $clientLogo->setPosition($i);

            $srcFile = __DIR__ . '/../../seed/clientes/' . $c['logo'];
            if (file_exists($srcFile)) {
                copy($srcFile, $destDir . '/' . $c['logo']);
                $clientLogo->setImageName($c['logo']);
            }

            $this->em->persist($clientLogo);
        }
        $this->em->flush();
        $io->text(count($clients) . ' logos de clientes inseridos.');
    }

    private function seedServices(SymfonyStyle $io): void
    {
        $services = [
            [
                'titlePt' => 'Manutenção Industrial',
                'titleEn' => 'Industrial Maintenance',
                'titleEs' => 'Mantenimiento Industrial',
                'shortDescriptionPt' => 'Manutenção preventiva e corretiva especializada para prensas hidráulicas e mecânicas.',
                'shortDescriptionEn' => 'Specialized preventive and corrective maintenance for hydraulic and mechanical presses.',
                'shortDescriptionEs' => 'Mantenimiento preventivo y correctivo especializado para prensas hidráulicas y mecánicas.',
                'descriptionPt' => 'Oferecemos serviços de manutenção industrial de alta precisão. Contamos com engenheiros e técnicos altamente qualificados para garantir a mínima inatividade das suas prensas, realizando diagnósticos rápidos, testes de pressão e substituição de vedações com componentes líderes mundiais.',
                'descriptionEn' => 'We offer high-precision industrial maintenance services. We have highly qualified engineers and technicians to ensure minimum downtime for your presses, performing rapid diagnostics, pressure tests, and seal replacement with world-leading components.',
                'descriptionEs' => 'Ofrecemos servicios de mantenimiento industrial de alta precisión. Contamos con ingenieros y técnicos altamente calificados para asegurar el mínimo tempo de inactividad de sus prensas, realizando diagnósticos rápidos, pruebas de presión y reemplazo de sellos con componentes líderes mundiales.',
                'image' => 'service-maintenance.png'
            ],
            [
                'titlePt' => 'Reforma e Retrofit',
                'titleEn' => 'Retrofitting and Modernization',
                'titleEs' => 'Reforma y Retrofit',
                'shortDescriptionPt' => 'Modernização completa de prensas antigas, com atualização de comandos eletrônicos, hidráulicos e mecânicos.',
                'shortDescriptionEn' => 'Complete modernization of old presses, with upgrades to electronic, hydraulic, and mechanical controls.',
                'shortDescriptionEs' => 'Modernización completa de prensas antiguas, con actualización de comandos electrónicos, hidráulicos e mecánicos.',
                'descriptionPt' => 'O serviço de Retrofit da Pressmatik devolve a produtividade e a precisão do seu equipamento antigo, com custos muito inferiores aos de adquirir uma máquina nova. Atualizamos o sistema CLP, bombas, válvulas de segurança e toda a estrutura mecânica.',
                'descriptionEn' => "Pressmatik's Retrofit service restores the productivity and precision of your old equipment, at a fraction of the cost of acquiring a new machine. We upgrade the PLC system, pumps, safety valves, and the entire mechanical structure.",
                'descriptionEs' => 'El servicio de Retrofit de Pressmatik devuelve la productividad y precisión de su equipo antiguo, a un costo muy inferior al de adquirir una máquina nueva. Actualizamos el sistema PLC, bombas, válvulas de seguridad y toda la mecánica.',
                'image' => 'service-retrofit.png'
            ],
            [
                'titlePt' => 'Adequação à NR12',
                'titleEn' => 'NR12 Safety Compliance',
                'titleEs' => 'Adecuación a la NR12',
                'shortDescriptionPt' => 'Projetos completos de segurança e adequação de prensas industriais de acordo com a norma regulamentadora brasileira.',
                'shortDescriptionEn' => 'Complete safety projects and compliance for industrial presses in accordance with Brazilian regulatory standards.',
                'shortDescriptionEs' => 'Proyectos completos de segurança y adecuación de prensas industriales de acuerdo con la norma reguladora brasileña.',
                'descriptionPt' => 'Garantimos que suas prensas operem em total conformidade legal e com máxima segurança para os operadores. Implementamos cortinas de luz, blocos hidráulicos monitorados de segurança, proteções físicas móveis e laudo técnico final assinado por engenheiros de segurança.',
                'descriptionEn' => 'We ensure your presses operate in full legal compliance and with maximum safety for operators. We implement light curtains, monitored safety hydraulic blocks, physical mobile protections, and a final technical report signed by safety engineers.',
                'descriptionEs' => 'Garantizamos que sus prensas operen em total conformidad legal y con máxima seguridad para los operadores. Implementamos cortinas de luz, bloques hidráulicos monitoreados de seguridad, protecciones físicas móviles y informe técnico final firmado por ingenieros de seguridad.',
                'image' => 'service-safety.png'
            ]
        ];

        $destDir = __DIR__ . '/../../public/uploads/services';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $slugger = new \Symfony\Component\String\Slugger\AsciiSlugger();
        foreach ($services as $i => $s) {
            $item = new Service();
            $item->setTitlePt($s['titlePt']);
            $item->setTitleEn($s['titleEn']);
            $item->setTitleEs($s['titleEs']);
            $item->setShortDescriptionPt($s['shortDescriptionPt']);
            $item->setShortDescriptionEn($s['shortDescriptionEn']);
            $item->setShortDescriptionEs($s['shortDescriptionEs']);
            $item->setDescriptionPt($s['descriptionPt']);
            $item->setDescriptionEn($s['descriptionEn']);
            $item->setDescriptionEs($s['descriptionEs']);
            $item->setIsActive(true);
            $item->setPosition($i);
            $item->setSlugPt($slugger->slug($s['titlePt'])->lower()->toString());
            $item->setSlugEn($slugger->slug($s['titleEn'])->lower()->toString());
            $item->setSlugEs($slugger->slug($s['titleEs'])->lower()->toString());

            $srcFile = __DIR__ . '/../../seed/services/' . $s['image'];
            if (file_exists($srcFile)) {
                copy($srcFile, $destDir . '/' . $s['image']);
                $item->setImageName($s['image']);
            }

            $this->em->persist($item);
        }

        $this->em->flush();
        $io->text(count($services) . ' serviços inseridos.');
    }

    private function seedNews(SymfonyStyle $io): void
    {
        // 1. Create categories
        $catNames = [
            ['pt' => 'Feiras e Eventos', 'en' => 'Fairs & Events', 'es' => 'Ferias y Eventos'],
            ['pt' => 'Tecnologia', 'en' => 'Technology', 'es' => 'Tecnología']
        ];
        $categories = [];
        foreach ($catNames as $cn) {
            $cat = new NewsCategory();
            $cat->setNamePt($cn['pt']);
            $cat->setNameEn($cn['en']);
            $cat->setNameEs($cn['es']);
            $this->em->persist($cat);
            $categories[$cn['pt']] = $cat;
        }
        $this->em->flush();

        // 2. Create News
        $news = [
            [
                'titlePt' => 'Pressmatik na FEIMEC 2026: Inovação em Prensas Servo-Hidráulicas',
                'titleEn' => 'Pressmatik at FEIMEC 2026: Innovation in Servo-Hydraulic Presses',
                'titleEs' => 'Pressmatik en FEIMEC 2026: Innovación en Prensas Servo-Hydráulicas',
                'shortDescriptionPt' => 'Apresentamos nossas soluções de prensas servo-hidráulicas com economia de energia de até 50% na maior feira de máquinas da América Latina.',
                'shortDescriptionEn' => 'We presented our servo-hydraulic press solutions saving up to 50% energy at the largest machinery exhibition in Latin America.',
                'shortDescriptionEs' => 'Presentamos nuestras soluciones de prensas servo-hidráulicas con ahorro de energía de hasta 50% en la mayor feria de maquinaria de América Latina.',
                'fullDescriptionPt' => 'A Pressmatik participou com destaque da FEIMEC 2026, onde apresentou em tempo real o funcionamento da prensa servo-hidráulica da linha ST. O evento foi um sucesso de público e negócios, consolidando nossa posição de liderança tecnológica no setor.',
                'fullDescriptionEn' => 'Pressmatik participated prominently in FEIMEC 2026, demonstrating in real-time the operation of the ST line servo-hydraulic press. The event was a huge success, solidifying our leadership in technological solutions.',
                'fullDescriptionEs' => 'Pressmatik participó de manera destacada en FEIMEC 2026, demostrando en tiempo real el funcionamento de la prensa servo-hidráulica de la línea ST. O evento foi um sucesso de público e negócios, consolidando nossa posição de liderança tecnológica no setor.',
                'image' => 'news-exhibition1.png',
                'isHighlighted' => true,
                'category' => 'Feiras e Eventos',
                'date' => '2026-05-10'
            ],
            [
                'titlePt' => 'Expansão Internacional: Pressmatik expõe em Hannover Messe',
                'titleEn' => 'International Expansion: Pressmatik exhibits at Hannover Messe',
                'titleEs' => 'Expansión Internacional: Pressmatik expone en Hannover Messe',
                'shortDescriptionPt' => 'Levando a tecnologia de conformação de metal brasileira para o mercado global na principal feira industrial do mundo na Alemanha.',
                'shortDescriptionEn' => 'Bringing Brazilian metal forming technology to the global market at the world\'s leading industrial show in Germany.',
                'shortDescriptionEs' => 'Llevando la tecnología de conformación de metal brasileña al mercado global en la principal feria industrial del mundo en Alemania.',
                'fullDescriptionPt' => 'Com orgulho, a Pressmatik marcou presença na Hannover Messe deste ano. Levamos nossas soluções robustas de prensas e alimentadores para o público internacional, abrindo novas fronteiras e parcerias estratégicas na Europa e Ásia.',
                'fullDescriptionEn' => 'Proudly, Pressmatik made its presence felt at the Hannover Messe this year. We took our robust press and feeder solutions to the international stage, opening new frontiers and partnerships in Europe and Asia.',
                'fullDescriptionEs' => 'Com orgulho, a Pressmatik marcou presença na Hannover Messe deste ano. Levamos nossas soluções robustas de prensas e alimentadores para o público internacional, abrindo novas fronteiras e parcerias estratégicas na Europa e Ásia.',
                'image' => 'news-hannover.png',
                'isHighlighted' => false,
                'category' => 'Feiras e Eventos',
                'date' => '2026-04-18'
            ],
            [
                'titlePt' => 'Seminário Técnico sobre Eficiência Energética na Estampagem',
                'titleEn' => 'Technical Seminar on Energy Efficiency in Metal Stamping',
                'titleEs' => 'Seminario Técnico sobre Eficiencia Energética en Estampación',
                'shortDescriptionPt' => 'Compartilhamos estudos de caso sobre como a tecnologia servo-hidráulica reduz o consumo elétrico e aumenta a vida útil das ferramentas.',
                'shortDescriptionEn' => 'We shared case studies on how servo-hydraulic technology reduces electrical consumption and increases tool lifetime.',
                'shortDescriptionEs' => 'Compartimos estudios de caso sobre cómo la tecnología servo-hidráulica reduce el consumo eléctrico y aumenta la vida útil de las herramientas.',
                'fullDescriptionPt' => 'Realizado em nossa sede, o seminário reuniu diretores e engenheiros de grandes metalúrgicas para discutir o futuro da conformação de chapas. Demonstramos na prática os benefícios de economia das bombas controladas por servo-acionamento.',
                'fullDescriptionEn' => 'Held at our headquarters, the seminar brought together directors and engineers from major metalworking companies to discuss the future of sheet metal forming. We demonstrated the saving benefits of servo-driven pumps.',
                'fullDescriptionEs' => 'Realizado em nossa sede, o seminário reuniu diretores e engenheiros de grandes metalúrgicas para discutir o futuro da conformação de chapas. Demonstramos na prática os benefícios de economia das bombas controladas por servo-acionamento.',
                'image' => 'news-seminar.png',
                'isHighlighted' => false,
                'category' => 'Tecnologia',
                'date' => '2026-06-05'
            ]
        ];

        $destDir = __DIR__ . '/../../public/uploads/news';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $slugger = new \Symfony\Component\String\Slugger\AsciiSlugger();
        foreach ($news as $n) {
            $item = new News();
            $item->setTitlePt($n['titlePt']);
            $item->setTitleEn($n['titleEn']);
            $item->setTitleEs($n['titleEs']);
            $item->setShortDescriptionPt($n['shortDescriptionPt']);
            $item->setShortDescriptionEn($n['shortDescriptionEn']);
            $item->setShortDescriptionEs($n['shortDescriptionEs']);
            $item->setFullDescriptionPt($n['fullDescriptionPt']);
            $item->setFullDescriptionEn($n['fullDescriptionEn']);
            $item->setFullDescriptionEs($n['fullDescriptionEs']);
            $item->setIsActive(true);
            $item->setIsHighlighted($n['isHighlighted']);
            $item->setDate(new \DateTime($n['date']));
            $item->setSlugPt($slugger->slug($n['titlePt'])->lower()->toString());
            $item->setSlugEn($slugger->slug($n['titleEn'])->lower()->toString());
            $item->setSlugEs($slugger->slug($n['titleEs'])->lower()->toString());
            $item->addCategory($categories[$n['category']]);

            $srcFile = __DIR__ . '/../../seed/news/' . $n['image'];
            if (file_exists($srcFile)) {
                copy($srcFile, $destDir . '/' . $n['image']);
                $item->setImageName($n['image']);
            }

            $this->em->persist($item);
        }

        $this->em->flush();
        $io->text(count($news) . ' notícias inseridas.');
    }

    private function seedTestimonies(SymfonyStyle $io): void
    {
        $testimonies = [
            [
                'name' => 'Eng. Ricardo M. Souza',
                'company' => 'Metalúrgica Paulista S.A.',
                'rolePt' => 'Diretor de Operações',
                'roleEn' => 'Operations Director',
                'roleEs' => 'Director de Operaciones',
                'textPt' => 'As prensas servo-hidráulicas da Pressmatik revolucionaram nossa linha. O tempo de setup foi reduzido pela metade e o consumo de energia caiu drasticamente. Suporte técnico exemplar!',
                'textEn' => "Pressmatik's servo-hydraulic presses have completely revolutionized our production lines. Setup times were cut in half and energy consumption fell dramatically. Outstanding technical support!",
                'textEs' => 'Las prensas servo-hidráulicas de Pressmatik han revolucionado nuestras líneas de producción. El tiempo de preparación se redujo a la mitad y el consumo de energía cayó drásticamente. ¡Soporte técnico sobresaliente!',
                'logo' => 'client1.jpg'
            ],
            [
                'name' => 'Mariana Silva',
                'company' => 'AutoParts Brasil',
                'rolePt' => 'Gerente de Engenharia',
                'roleEn' => 'Engineering Manager',
                'roleEs' => 'Gerente de Ingeniería',
                'textPt' => 'Equipamento extremamente robusto e de altíssima precisão. O projeto de conformidade com a NR12 nos deu total segurança. Recomendo fortemente.',
                'textEn' => 'Extremely robust equipment with absolute precision. The NR12 compliance safety layout project gave us total peace of mind. Highly recommended.',
                'textEs' => 'Equipo extremadamente robusto y con absoluta precisión. El proyecto de seguridad NR12 nos dio total tranquilidad. Altamente recomendado.',
                'logo' => 'client2.jpg'
            ],
            [
                'name' => 'Carlos H. Ferreira',
                'company' => 'Fundição Sul Ltda.',
                'rolePt' => 'Coordenador de Produção',
                'roleEn' => 'Production Coordinator',
                'roleEs' => 'Coordinador de Producción',
                'textPt' => 'A assistência técnica da Pressmatik é nota 10. Precisamos de uma manutenção emergencial em uma prensa de 160 toneladas e a equipe resolveu em poucas horas. Parabéns pelo comprometimento.',
                'textEn' => "Pressmatik's technical assistance is top-notch. We needed emergency maintenance on a 160-ton press and the team resolved it within hours. Outstanding dedication!",
                'textEs' => 'La asistencia técnica de Pressmatik es excelente. Necesitamos un mantenimiento de emergencia en una prensa de 160 toneladas y el equipo lo resolvió en pocas horas. ¡Excelente compromiso!',
                'logo' => 'client3.jpg'
            ]
        ];

        $destDir = __DIR__ . '/../../public/uploads/testimonies';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        foreach ($testimonies as $i => $t) {
            $item = new Testimony();
            $item->setName($t['name']);
            $item->setCompany($t['company']);
            $item->setRolePt($t['rolePt']);
            $item->setRoleEn($t['roleEn']);
            $item->setRoleEs($t['roleEs']);
            $item->setTextPt($t['textPt']);
            $item->setTextEn($t['textEn']);
            $item->setTextEs($t['textEs']);
            $item->setIsActive(true);
            $item->setPosition($i);

            $srcFile = __DIR__ . '/../../seed/testimonies/' . $t['logo'];
            if (file_exists($srcFile)) {
                copy($srcFile, $destDir . '/' . $t['logo']);
                $item->setImageName($t['logo']);
            }

            $this->em->persist($item);
        }

        $this->em->flush();
        $io->text(count($testimonies) . ' depoimentos inseridos.');
    }

    private function seedMegaMenuCategories(SymfonyStyle $io): void
    {
        $categories = [
            [
                'key' => 'hydraulic',
                'titlePt' => 'Prensas Hidráulicas',
                'titleEn' => 'Hydraulic Presses',
                'titleEs' => 'Prensas Hidráulicas',
                'defaultImg' => '/images/prensa-hidraulica-tipo-c-duplo-linha-st.png',
                'position' => 0
            ],
            [
                'key' => 'servo-hydraulic',
                'titlePt' => 'Prensas Servo-Hidráulicas',
                'titleEn' => 'Servo-Hydraulic Presses',
                'titleEs' => 'Prensas Servo-Hidráulicas',
                'defaultImg' => '/images/pmc-es.png',
                'position' => 1
            ],
            [
                'key' => 'mechanical',
                'titlePt' => 'Prensas Mecânicas',
                'titleEn' => 'Mechanical Presses',
                'titleEs' => 'Prensas Mecánicas',
                'defaultImg' => '/images/prensa-hidraulica-tipo-c-linha-st.png',
                'position' => 2
            ],
            [
                'key' => 'equipments',
                'titlePt' => 'Máquinas e Equipamentos',
                'titleEn' => 'Machinery & Equipment',
                'titleEs' => 'Maquinaria y Equipos',
                'defaultImg' => '/images/tipo-h.png',
                'position' => 3
            ],
            [
                'key' => 'parts',
                'titlePt' => 'Peças e Acessórios',
                'titleEn' => 'Parts & Accessories',
                'titleEs' => 'Piezas y Accesorios',
                'defaultImg' => '/images/especiais.png',
                'position' => 4
            ],
        ];

        foreach ($categories as $cat) {
            $item = new MegaMenuCategory();
            $item->setCategoryKey($cat['key']);
            $item->setTitlePt($cat['titlePt']);
            $item->setTitleEn($cat['titleEn']);
            $item->setTitleEs($cat['titleEs']);
            $item->setDefaultImagePath($cat['defaultImg']);
            $item->setPosition($cat['position']);

            $this->em->persist($item);
        }

        $this->em->flush();
        $io->text(count($categories) . ' categorias do MegaMenu inicializadas.');
    }

    private function findFileRecursively(string $dir, string $targetFilename): ?string
    {
        if (!is_dir($dir)) {
            return null;
        }

        $normalizedTarget = $this->normalizeString($targetFilename);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if ($file->isFile()) {
                $normalizedFile = $this->normalizeString($file->getFilename());
                if ($normalizedFile === $normalizedTarget) {
                    return $file->getPathname();
                }
            }
        }

        return null;
    }

    private function normalizeString(string $str): string
    {
        $str = mb_strtolower($str);
        $str = str_replace(['“', '”', '“', '”', '"', "'", '́', '̃', '̂', '̧'], '', $str);
        $str = strtr($str, [
            'á'=>'a', 'à'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
            'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e',
            'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i',
            'ó'=>'o', 'ò'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o',
            'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'u',
            'ç'=>'c'
        ]);
        $str = preg_replace('/[\s\-_]+/', '', $str);
        return $str;
    }
}
