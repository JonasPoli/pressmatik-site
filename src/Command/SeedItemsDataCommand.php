<?php

namespace App\Command;

use App\Entity\ApplicationListItem;
use App\Entity\OptionalItem;
use App\Entity\StandardItem;
use App\Entity\Subproduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-items-data',
    description: 'Seed Itens de Série, Aplicações (Listagem) e Itens Opcionais and establish relationships with Subproducts without modifying existing products',
)]
class SeedItemsDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Iniciando Seeding de Itens de Série, Aplicações (Listagem) e Itens Opcionais');

        $jsonPath = __DIR__ . '/../../docs/items_data.json';
        if (!file_exists($jsonPath)) {
            $io->error('Arquivo docs/items_data.json não encontrado.');
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        if (!$data || !isset($data['masters']) || !isset($data['mappings'])) {
            $io->error('Arquivo docs/items_data.json inválido.');
            return Command::FAILURE;
        }

        // Helper for string normalization
        $normalize = function (string $str): string {
            $str = mb_strtolower(trim($str));
            $str = preg_replace('/[^\p{L}\p{N}]/u', '', $str);
            return $str;
        };

        // 1. Seed Master Standard Items
        $io->section('1. Cadastrando / Atualizando Itens de Série');
        $standardRepo = $this->em->getRepository(StandardItem::class);
        $standardMap = []; // normKey => StandardItem
        $countStandard = 0;

        foreach ($data['masters']['standard'] as $item) {
            $normKey = $normalize($item['namePt']);
            $entity = $standardRepo->findOneBy(['namePt' => $item['namePt']]) ?? new StandardItem();
            $entity->setIcon($item['icon']);
            $entity->setNamePt($item['namePt']);
            $entity->setNameEn($item['nameEn'] ?? $item['namePt']);
            $entity->setNameEs($item['nameEs'] ?? $item['namePt']);
            $entity->setPosition($item['position']);
            $entity->setIsActive(true);

            $this->em->persist($entity);
            $standardMap[$normKey] = $entity;
            $countStandard++;
        }
        $this->em->flush();
        $io->success("{$countStandard} Itens de Série mestre processados.");

        // 2. Seed Master ApplicationListItems
        $io->section('2. Cadastrando / Atualizando Aplicações (Listagem)');
        $appRepo = $this->em->getRepository(ApplicationListItem::class);
        $appMap = []; // normKey => ApplicationListItem
        $countApp = 0;

        foreach ($data['masters']['application'] as $item) {
            $normKey = $normalize($item['namePt']);
            $entity = $appRepo->findOneBy(['namePt' => $item['namePt']]) ?? new ApplicationListItem();
            $entity->setIcon($item['icon']);
            $entity->setNamePt($item['namePt']);
            $entity->setNameEn($item['nameEn'] ?? $item['namePt']);
            $entity->setNameEs($item['nameEs'] ?? $item['namePt']);
            $entity->setPosition($item['position']);
            $entity->setIsActive(true);

            $this->em->persist($entity);
            $appMap[$normKey] = $entity;
            $countApp++;
        }
        $this->em->flush();
        $io->success("{$countApp} Aplicações (Listagem) mestre processadas.");

        // 3. Seed Master Optional Items
        $io->section('3. Cadastrando / Atualizando Itens Opcionais');
        $optionalRepo = $this->em->getRepository(OptionalItem::class);
        $optionalMap = []; // normKey => OptionalItem
        $countOpt = 0;

        foreach ($data['masters']['optional'] as $item) {
            $normKey = $normalize($item['namePt']);
            $entity = $optionalRepo->findOneBy(['namePt' => $item['namePt']]) ?? new OptionalItem();
            $entity->setIcon($item['icon']);
            $entity->setNamePt($item['namePt']);
            $entity->setNameEn($item['nameEn'] ?? $item['namePt']);
            $entity->setNameEs($item['nameEs'] ?? $item['namePt']);
            $entity->setPosition($item['position']);
            $entity->setIsActive(true);

            $this->em->persist($entity);
            $optionalMap[$normKey] = $entity;
            $countOpt++;
        }
        $this->em->flush();
        $io->success("{$countOpt} Itens Opcionais mestre processados.");

        // 4. Process Mappings for Subproducts
        $io->section('4. Vinculando Itens aos Subprodutos');
        $subproductRepo = $this->em->getRepository(Subproduct::class);
        $subproducts = $subproductRepo->findAll();
        $subByModel = [];
        foreach ($subproducts as $sub) {
            $subByModel[$sub->getModel()] = $sub;
        }

        $mappedCount = 0;
        foreach ($data['mappings'] as $map) {
            $model = $map['model'];
            $cat = $map['category']; // 'standard', 'application', 'optional'
            $namePt = $map['namePt'];
            $normKey = $normalize($namePt);

            $subproduct = $subByModel[$model] ?? null;
            if (!$subproduct) {
                continue;
            }

            if ($cat === 'standard') {
                $item = $standardMap[$normKey] ?? null;
                if (!$item) {
                    $item = new StandardItem();
                    $item->setIcon('fa-solid fa-check');
                    $item->setNamePt($namePt);
                    $item->setNameEn($map['nameEn'] ?? $namePt);
                    $item->setNameEs($map['nameEs'] ?? $namePt);
                    $item->setPosition(count($standardMap));
                    $item->setIsActive(true);
                    $this->em->persist($item);
                    $this->em->flush();
                    $standardMap[$normKey] = $item;
                }
                $subproduct->addStandardItem($item);
                $mappedCount++;
            } elseif ($cat === 'application') {
                $item = $appMap[$normKey] ?? null;
                if (!$item) {
                    $item = new ApplicationListItem();
                    $item->setIcon('fa-solid fa-industry');
                    $item->setNamePt($namePt);
                    $item->setNameEn($map['nameEn'] ?? $namePt);
                    $item->setNameEs($map['nameEs'] ?? $namePt);
                    $item->setPosition(count($appMap));
                    $item->setIsActive(true);
                    $this->em->persist($item);
                    $this->em->flush();
                    $appMap[$normKey] = $item;
                }
                $subproduct->addApplicationListItem($item);
                $mappedCount++;
            } elseif ($cat === 'optional') {
                $item = $optionalMap[$normKey] ?? null;
                if (!$item) {
                    $item = new OptionalItem();
                    $item->setIcon('fa-solid fa-plus-circle');
                    $item->setNamePt($namePt);
                    $item->setNameEn($map['nameEn'] ?? $namePt);
                    $item->setNameEs($map['nameEs'] ?? $namePt);
                    $item->setPosition(count($optionalMap));
                    $item->setIsActive(true);
                    $this->em->persist($item);
                    $this->em->flush();
                    $optionalMap[$normKey] = $item;
                }
                $subproduct->addOptionalItem($item);
                $mappedCount++;
            }
        }

        $this->em->flush();
        $io->success("🎉 Injeção de dados concluída com sucesso! {$mappedCount} relacionamentos estabelecidos.");
        return Command::SUCCESS;
    }
}
