<?php

namespace App\Command;

use App\Entity\AboutUs;
use App\Entity\HistoryTimeline;
use App\Entity\ProductConfigItem;
use App\Entity\ProductSize;
use App\Entity\ProductSpecValue;
use App\Entity\ProductVideo;
use App\Entity\TechnicalSpecification;
use App\Entity\User;
use App\Entity\Testimony;
use App\Entity\ClientLogo;
use App\Entity\NewsCategory;
use App\Entity\News;
use App\Entity\Service;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-data',
    description: 'Seed initial data including admin user, products technical specs, configurations, operation videos, and institutional pages in 3 languages',
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

        // 2. Limpar dados anteriores (opcional, para evitar duplicações no seed)
        $io->section('2. Limpando dados anteriores de produtos & institucional');
        
        $this->em->createQuery('DELETE FROM App\Entity\ProductSpecValue')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\ProductSize')->execute();
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
        
        $io->text('Tabelas limpas com sucesso.');

        // 3. Cadastrar Especificações Técnicas Base
        $io->section('3. Cadastrando Especificações Técnicas');
        
        $specsData = [
            [
                'namePt' => 'Força de Prensagem', 'nameEn' => 'Pressing Force', 'nameEs' => 'Fuerza de Prensado',
                'unitPt' => 't', 'unitEn' => 't', 'unitEs' => 't', 'position' => 1
            ],
            [
                'namePt' => 'Força de Retorno', 'nameEn' => 'Return Force', 'nameEs' => 'Fuerza de Retorno',
                'unitPt' => 't', 'unitEn' => 't', 'unitEs' => 't', 'position' => 2
            ],
            [
                'namePt' => 'Velocidade de Avanço Rápido', 'nameEn' => 'Fast Forward Speed', 'nameEs' => 'Velocidad de Avance Rápido',
                'unitPt' => 'mm/s', 'unitEn' => 'mm/s', 'unitEs' => 'mm/s', 'position' => 3
            ],
            [
                'namePt' => 'Velocidade de Trabalho', 'nameEn' => 'Working Speed', 'nameEs' => 'Velocidad de Trabajo',
                'unitPt' => 'mm/s', 'unitEn' => 'mm/s', 'unitEs' => 'mm/s', 'position' => 4
            ],
            [
                'namePt' => 'Velocidade de Retorno', 'nameEn' => 'Return Speed', 'nameEs' => 'Velocidad de Retorno',
                'unitPt' => 'mm/s', 'unitEn' => 'mm/s', 'unitEs' => 'mm/s', 'position' => 5
            ],
            [
                'namePt' => 'Abertura Mesa x Martelo', 'nameEn' => 'Daylight', 'nameEs' => 'Apertura Mesa x Martillo',
                'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 6
            ],
            [
                'namePt' => 'Curso do Cilindro', 'nameEn' => 'Cylinder Stroke', 'nameEs' => 'Carrera del Cilindro',
                'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 7
            ],
            [
                'namePt' => 'Mesa (Frente x Profundidade)', 'nameEn' => 'Table (Front x Depth)', 'nameEs' => 'Mesa (Frente x Profundidad)',
                'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 8
            ],
            [
                'namePt' => 'Saída de Cavaco', 'nameEn' => 'Chip Exit', 'nameEs' => 'Salida de Viruta',
                'unitPt' => 'Ø', 'unitEn' => 'Ø', 'unitEs' => 'Ø', 'position' => 9
            ],
            [
                'namePt' => 'Martelo (Frente x Profundidade)', 'nameEn' => 'Hammer (Front x Depth)', 'nameEs' => 'Martillo (Frente x Profundidad)',
                'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 10
            ],
            [
                'namePt' => 'Furo para Espiga', 'nameEn' => 'Shank Holder', 'nameEs' => 'Agujero para Espiga',
                'unitPt' => 'Ø', 'unitEn' => 'Ø', 'unitEs' => 'Ø', 'position' => 11
            ],
            [
                'namePt' => 'Centro do Pistão até a Estrutura', 'nameEn' => 'Throat', 'nameEs' => 'Centro del Pistón a la Estructura',
                'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 12
            ],
            [
                'namePt' => 'Vão Frontal da Grade (LxA)', 'nameEn' => 'Front Grille Gap (WxH)', 'nameEs' => 'Apertura de la Rejilla Frontal (LxA)',
                'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 13
            ],
            [
                'namePt' => 'Motor', 'nameEn' => 'Motor', 'nameEs' => 'Motor',
                'unitPt' => 'CV', 'unitEn' => 'HP', 'unitEs' => 'CV', 'position' => 14
            ],
            [
                'namePt' => 'Capacidade de Óleo (AW68)', 'nameEn' => 'Oil Capacity (AW68)', 'nameEs' => 'Capacidad de Aceite (AW68)',
                'unitPt' => 'l', 'unitEn' => 'l', 'unitEs' => 'l', 'position' => 15
            ],
            [
                'namePt' => 'Largura (Frontal)', 'nameEn' => 'Width (Front)', 'nameEs' => 'Ancho (Frontal)',
                'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 16
            ],
            [
                'namePt' => 'Profundidade (Lateral)', 'nameEn' => 'Depth (Side)', 'nameEs' => 'Profundidad (Lateral)',
                'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 17
            ],
            [
                'namePt' => 'Altura', 'nameEn' => 'Height', 'nameEs' => 'Altura',
                'unitPt' => 'mm', 'unitEn' => 'mm', 'unitEs' => 'mm', 'position' => 18
            ],
            [
                'namePt' => 'Peso Aproximado', 'nameEn' => 'Approximate Weight', 'nameEs' => 'Peso Aproximado',
                'unitPt' => 'Kg', 'unitEn' => 'Kg', 'unitEs' => 'Kg', 'position' => 19
            ]
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

        // 4. Cadastrar Subprodutos (Tamanhos) e Seus Parâmetros Técnicos (Tabelas)
        $io->section('4. Cadastrando Tamanhos de Produtos & Tabela de Especificações');

        $jsonPath = __DIR__ . '/../../seed_data.json';
        if (!file_exists($jsonPath)) {
            throw new \RuntimeException('Arquivo seed_data.json não encontrado em: ' . $jsonPath);
        }

        $jsonData = json_decode(file_get_contents($jsonPath), true);

        foreach ($jsonData as $slug => $productData) {
            $io->text("Processando tamanhos para: {$slug}");
            $sizesList = $productData['sizes'] ?? [];
            $valuesList = $productData['values'] ?? [];

            $dbSizes = [];
            $pos = 0;
            foreach ($sizesList as $s) {
                $size = new ProductSize();
                $size->setProductSlug($slug);
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
                    // Se a especificação não existir, cria na hora!
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
                    $val->setProductSlug($slug);
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

        // Criar dados simples para as subcategorias adicionais que não estão no seed_data.json
        $otherProducts = [
            'prensas-hidraulicas-especiais' => ['Sob Consulta'],
            'prensas-servo-hidraulicas-servo-bombas' => ['Sob Consulta'],
            'prensas-mecanicas-mecanicas' => ['ST-Series C-Frame', 'STC-Series', 'STB-Series', 'STV-Series', 'STD-Series'],
            'prensas-mecanicas-servo' => ['STA-Series', 'STC-Series', 'STD-Series', 'STE-Series'],
            'prensas-mecanicas-alta-velocidade' => ['STS-Series', 'MARX-Series', 'MDH-Series', 'DDH-Series']
        ];

        foreach ($otherProducts as $slug => $sizesList) {
            $p = 0;
            foreach ($sizesList as $sName) {
                $size = new ProductSize();
                $size->setProductSlug($slug);
                $size->setName($sName);
                $size->setHasVType(true);
                $size->setHasHType(false);
                $size->setPosition($p++);
                $this->em->persist($size);

                $val1 = new ProductSpecValue();
                $val1->setProductSlug($slug);
                $val1->setSpecification($specsEntities['Força de Prensagem'] ?? ($specsEntities['Capacidade da Prensa'] ?? null));
                if ($val1->getSpecification()) {
                    $val1->setProductSize($size);
                    $val1->setVTypeValue('Sob Consulta');
                    $val1->setPosition(0);
                    $this->em->persist($val1);
                }
            }
        }

        $this->em->flush();
        $io->success('Tamanhos e Parâmetros Técnicos cadastrados para todos os produtos.');

        // 5. Cadastrar Equipamentos Standard e Opcionais
        $io->section('5. Cadastrando Equipamentos Standard e Opcionais');

        $configs = [
            // Standard
            ['type' => 'standard', 'pt' => 'Bloco Manifold monitorado - cat.4', 'en' => 'Monitored Manifold Block - Cat. 4', 'es' => 'Bloque Manifold monitoreado - cat.4', 'pos' => 0],
            ['type' => 'standard', 'pt' => 'CLP de segurança', 'en' => 'Safety PLC', 'es' => 'CLP de seguridad', 'pos' => 1],
            ['type' => 'standard', 'pt' => 'Calço de segurança mecânico monitorado', 'en' => 'Monitored mechanical safety block', 'es' => 'Calzo de seguridad mecánico monitoreado', 'pos' => 2],
            ['type' => 'standard', 'pt' => 'Cortina de luz de segurança', 'en' => 'Safety light curtain', 'es' => 'Cortina de luz de seguridad', 'pos' => 3],
            ['type' => 'standard', 'pt' => 'Grade de proteção lateral e traseira', 'en' => 'Side and rear protective guard', 'es' => 'Rejilla de protección lateral y trasera', 'pos' => 4],
            ['type' => 'standard', 'pt' => 'Console bimanual com simultaneidade', 'en' => 'Two-handed control console with simultaneity', 'es' => 'Consola bimanual con simultaneidad', 'pos' => 5],
            
            // Optional
            ['type' => 'optional', 'pt' => 'CLP+IHM (Programação de receitas)', 'en' => 'PLC+HMI (Recipe programming)', 'es' => 'CLP+IHM (Programación de recetas)', 'pos' => 0],
            ['type' => 'optional', 'pt' => 'Válvulas proporcionais de vazão e pressão', 'en' => 'Proportional flow and pressure valves', 'es' => 'Válvulas proporcionales de caudal y presión', 'pos' => 1],
            ['type' => 'optional', 'pt' => 'Célula de carga (Controle de força)', 'en' => 'Load cell (Force control)', 'es' => 'Célula de carga (Control de fuerza)', 'pos' => 2],
            ['type' => 'optional', 'pt' => 'Cilindro extrator ou almofada hidráulica', 'en' => 'Extractor cylinder or hydraulic cushion', 'es' => 'Cilindro extractor o cojín hidráulico', 'pos' => 3],
            ['type' => 'optional', 'pt' => 'Mesa móvel ou mesa giratória', 'en' => 'Mobile table or rotating table', 'es' => 'Mesa móvil o mesa giratoria', 'pos' => 4],
        ];

        foreach ($configs as $c) {
            foreach (['prensas-hidraulicas-tipo-c', 'prensas-hidraulicas-tipo-c-duplo', 'prensas-hidraulicas-4-colunas', 'prensas-hidraulicas-tipo-h'] as $productSlug) {
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

        // 6. Cadastrar Vídeos de Operação
        $io->section('6. Cadastrando Vídeos de Operação');
        
        $videos = [
            [
                'titlePt' => 'Demonstração Prensa Hidráulica Tipo C', 
                'titleEn' => 'C-Frame Hydraulic Press Demonstration', 
                'titleEs' => 'Demostración Prensa Hidráulica Tipo C',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
            ],
            [
                'titlePt' => 'Operação de Estampagem Industrial', 
                'titleEn' => 'Industrial Stamping Operation', 
                'titleEs' => 'Operación de Estampado Industrial',
                'url' => 'https://www.qiaosenpresses.com/uploads/Deep-Throat-Presses.mp4'
            ]
        ];

        foreach ($videos as $idx => $v) {
            foreach (['prensas-hidraulicas-tipo-c', 'prensas-hidraulicas-tipo-c-duplo', 'prensas-hidraulicas-4-colunas', 'prensas-hidraulicas-tipo-h'] as $productSlug) {
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

        // 7. Cadastrar Conteúdo Institucional "Quem Somos"
        $io->section('7. Cadastrando Quem Somos');
        
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
        $about->setValuesEs("• Innovación tecnológica continua\n• Seguridad operacional absoluta (NR10 / NR12)\n• Compromiso con la calidad y plazos\n• Alianza a largo plazo con clientes y proveedores\n• Integridad y ética en todas las relaciones");

        // 4 vantagens
        $about->setAdvantage1TitlePt('Segurança Total (NR10 & NR12)');
        $about->setAdvantage1TitleEn('Total Safety (NR10 & NR12)');
        $about->setAdvantage1TitleEs('Seguridad Total (NR10 y NR12)');
        $about->setAdvantage1DescPt('Sistemas projetados de fábrica sob estrita conformidade de segurança para proteção total dos operadores.');
        $about->setAdvantage1DescEn('Systems designed from the ground up under strict safety compliance for complete operator protection.');
        $about->setAdvantage1DescEs('Sistemas diseñados desde la fábrica bajo estricto cumplimiento de seguridad para la protección total del operador.');
        $about->setAdvantage1Icon('bi bi-shield-lock-fill');

        $about->setAdvantage2TitlePt('Componentes Premium');
        $about->setAdvantage2TitleEn('Premium Components');
        $about->setAdvantage2TitleEs('Componentes Premium');
        $about->setAdvantage2DescPt('Integração com tecnologia de líderes mundiais como Bosch Rexroth, Siemens e Weg.');
        $about->setAdvantage2DescEn('Integration with technology from world leaders like Bosch Rexroth, Siemens, and Weg.');
        $about->setAdvantage2DescEs('Integración con tecnología de líderes mundiales como Bosch Rexroth, Siemens y Weg.');
        $about->setAdvantage2Icon('bi bi-cpu');

        $about->setAdvantage3TitlePt('Alta Performance');
        $about->setAdvantage3TitleEn('High Performance');
        $about->setAdvantage3TitleEs('Alto Rendimiento');
        $about->setAdvantage3DescPt('Substituição de tecnologia mecânica antiga por sistemas servo-hidráulicos de setup rápido.');
        $about->setAdvantage3DescEn('Efficient replacement of legacy mechanical technology with fast-setup servo-hydraulic systems.');
        $about->setAdvantage3DescEs('Reemplazo eficiente de tecnología mecánica antigua por sistemas servo-hidráulicos de configuración rápida.');
        $about->setAdvantage3Icon('bi bi-speedometer2');

        $about->setAdvantage4TitlePt('Exportação Global');
        $about->setAdvantage4TitleEn('Global Export');
        $about->setAdvantage4TitleEs('Exportación Global');
        $about->setAdvantage4DescPt('Atendimento às rigorosas exigências mundiais, exportando para USA, Europa e América Latina.');
        $about->setAdvantage4DescEn('Meeting strict global standards, exporting to the USA, Europe, and Latin America.');
        $about->setAdvantage4DescEs('Cumpliendo con los estrictos estándares mundiales, exportando a USA, Europa y América Latina.');
        $about->setAdvantage4Icon('bi bi-globe');

        $about->setBannerImageName('default-about-banner.jpg');
        $this->em->flush();
        $io->text('Quem Somos configurado.');

        // 8. Cadastrar Linha do Tempo
        $io->section('8. Cadastrando Linha do Tempo');
        
        $timelineData = [
            [
                'year' => '2010',
                'titlePt' => 'Fundação da Pressmatik',
                'titleEn' => 'Founding of Pressmatik',
                'titleEs' => 'Fundación de Pressmatik',
                'descPt' => 'Início das atividades em Araraquara-SP, focada em manutenção e reforma de prensas excêntricas e hidráulicas.',
                'descEn' => 'Beginning of activities in Araraquara-SP, focusing on maintenance and retrofit of eccentric and hydraulic presses.',
                'descEs' => 'Inicio de actividades en Araraquara-SP, enfocada en mantenimiento y reforma de prensas excéntricas e hidráulicas.',
                'pos' => 0
            ],
            [
                'year' => '2018',
                'titlePt' => 'Lançamento da Linha de Prensas Tipo C',
                'titleEn' => 'Launch of C-Frame Presses Line',
                'titleEs' => 'Lanzamiento de la línea de Prensas Tipo C',
                'descPt' => 'Desenvolvimento e comercialização da linha PMC-ST, com foco em segurança NR12.',
                'descEn' => 'Development and commercialization of the PMC-ST line, focusing on NR12 safety regulations.',
                'descEs' => 'Desarrollo y comercialización de la línea PMC-ST, con enfoque en la seguridad NR12.',
                'pos' => 1
            ],
            [
                'year' => '2026',
                'titlePt' => 'Parceria Qiaosen e P/A Brasil',
                'titleEn' => 'Qiaosen and P/A Brazil Partnership',
                'titleEs' => 'Alianza con Qiaosen y P/A Brasil',
                'descPt' => 'Consolidação como distribuidor de prensas mecânicas Qiaosen e sistemas de alimentação P/A no Brasil.',
                'descEn' => 'Consolidation as distributor of Qiaosen mechanical presses and P/A feed systems in Brazil.',
                'descEs' => 'Consolidación como distribuidor de prensas mecánicas Qiaosen y sistemas de alimentación P/A en Brasil.',
                'pos' => 2
            ]
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

        $io->success('Seeding completo! Todos os dados inseridos com sucesso.');
        return Command::SUCCESS;
    }
}
