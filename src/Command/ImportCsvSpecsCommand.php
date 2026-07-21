<?php

namespace App\Command;

use App\Entity\Subproduct;
use App\Entity\ProductSize;
use App\Entity\ProductSpecValue;
use App\Entity\TechnicalSpecification;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductSpecValueRepository;
use App\Repository\TechnicalSpecificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-csv-specs',
    description: 'Import CSV specs for a subproduct',
)]
class ImportCsvSpecsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProductSizeRepository $sizeRepo,
        private readonly ProductSpecValueRepository $specValueRepo,
        private readonly TechnicalSpecificationRepository $techSpecRepo,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('model', InputArgument::REQUIRED, 'Subproduct model code (e.g. PMH-VB)')
            ->addArgument('csvPath', InputArgument::REQUIRED, 'Path to CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $model = $input->getArgument('model');
        $csvPath = $input->getArgument('csvPath');

        $subproduct = $this->em->getRepository(Subproduct::class)->findOneBy(['model' => $model]);
        if (!$subproduct) {
            $io->error("Subproduct with model '$model' not found.");
            return Command::FAILURE;
        }

        if (!file_exists($csvPath)) {
            $io->error("CSV file not found: $csvPath");
            return Command::FAILURE;
        }

        $content = file_get_contents($csvPath);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content); // Remove BOM
        $content = str_replace("\r\n", "\n", $content);
        $lines = array_filter(explode("\n", $content), fn($l) => trim($l) !== '');

        if (count($lines) < 2) {
            $io->error('CSV file is empty or has no data.');
            return Command::FAILURE;
        }

        // Auto-detect delimiter
        $firstLine = reset($lines);
        $semicolonCount = substr_count($firstLine, ';');
        $commaCount = substr_count($firstLine, ',');
        $delimiter = $semicolonCount >= $commaCount ? ';' : ',';
        $io->note("Detected delimiter: " . ($delimiter === ',' ? 'comma' : 'semicolon') . " (commas=$commaCount, semicolons=$semicolonCount)");

        // Parse header
        $headerCols = str_getcsv(array_shift($lines), $delimiter);
        $io->note("Header columns: " . count($headerCols));
        foreach ($headerCols as $i => $col) {
            $io->text("  [$i] = '$col'");
        }

        $sizeHeaders = array_slice($headerCols, 2);
        if (count($sizeHeaders) === 0) {
            $io->error('CSV has no size columns after "Especificação" and "Unidade".');
            return Command::FAILURE;
        }

        // Parse size config from headers
        $parsedColumns = [];
        $sizeConfig = [];
        foreach ($sizeHeaders as $header) {
            $header = trim($header);
            if ($header === '') continue;

            if (preg_match('/^(.+?)\s*\(V\)$/i', $header, $m)) {
                $sizeName = trim($m[1]);
                $type = 'v';
                $sizeConfig[$sizeName]['hasV'] = true;
            } elseif (preg_match('/^(.+?)\s*\(H\)$/i', $header, $m)) {
                $sizeName = trim($m[1]);
                $type = 'h';
                $sizeConfig[$sizeName]['hasH'] = true;
            } else {
                $sizeName = $header;
                $type = 'single';
                if (!isset($sizeConfig[$sizeName])) {
                    $sizeConfig[$sizeName] = [];
                }
            }

            if (!isset($sizeConfig[$sizeName]['hasV'])) $sizeConfig[$sizeName]['hasV'] = false;
            if (!isset($sizeConfig[$sizeName]['hasH'])) $sizeConfig[$sizeName]['hasH'] = false;

            $parsedColumns[] = ['sizeName' => $sizeName, 'type' => $type];
        }

        $io->note("Parsed " . count($sizeConfig) . " unique sizes and " . count($parsedColumns) . " columns.");

        // Find or create ProductSize entities
        $existingSizes = $this->sizeRepo->findBySubproductOrdered($subproduct->getId());
        $sizesByName = [];
        foreach ($existingSizes as $s) {
            $sizesByName[$s->getName()] = $s;
        }

        $createdSizes = 0;
        $nextPosition = count($existingSizes);
        foreach ($sizeConfig as $sizeName => $config) {
            if (!isset($sizesByName[$sizeName])) {
                $size = new ProductSize();
                $size->setSubproduct($subproduct);
                $size->setName($sizeName);
                $size->setHasVType($config['hasV']);
                $size->setHasHType($config['hasH']);
                $size->setPosition($nextPosition++);
                $this->em->persist($size);
                $sizesByName[$sizeName] = $size;
                $createdSizes++;
                $io->text("  Created size: $sizeName");
            }
        }

        if ($createdSizes > 0) {
            $this->em->flush();
        }

        // Build column mapping
        $columnMapping = [];
        $sizesById = [];
        foreach ($parsedColumns as $col) {
            $size = $sizesByName[$col['sizeName']] ?? null;
            if (!$size) continue;
            $sizesById[$size->getId()] = $size;
            $columnMapping[] = [
                'sizeId' => $size->getId(),
                'type' => $col['type'] === 'h' ? 'h' : 'v',
            ];
        }

        // Index existing spec values
        $existing = $this->specValueRepo->findBySubproductOrdered($subproduct->getId());
        $existingByKey = [];
        foreach ($existing as $val) {
            $key = $val->getSpecification()->getId() . '_' . $val->getProductSize()->getId();
            $existingByKey[$key] = $val;
        }

        // Build specs lookup by name
        $allSpecs = $this->techSpecRepo->findAllOrdered();
        $specsByName = [];
        foreach ($allSpecs as $spec) {
            $specsByName[mb_strtolower(trim($spec->getNamePt()))] = $spec;
        }

        // Import data rows
        $importedCount = 0;
        $createdSpecs = 0;
        foreach ($lines as $position => $line) {
            $cols = str_getcsv($line, $delimiter);
            if (count($cols) < 2) continue;

            $specName = trim($cols[0]);
            $unitName = trim($cols[1] ?? '');
            if (empty($specName)) continue;

            // Find or create TechnicalSpecification
            $specKey = mb_strtolower($specName);
            $spec = $specsByName[$specKey] ?? null;
            if (!$spec) {
                $spec = new TechnicalSpecification();
                $spec->setNamePt($specName);
                $spec->setUnitPt($unitName);
                $spec->setPosition(count($allSpecs) + $createdSpecs);
                $this->em->persist($spec);
                $this->em->flush();
                $specsByName[$specKey] = $spec;
                $createdSpecs++;
                $io->text("  Created spec: $specName ($unitName)");
            }

            // Parse value columns and upsert
            $dataColumns = array_slice($cols, 2);
            $sizeValuesInRow = [];

            foreach ($columnMapping as $colIdx => $map) {
                $cellValue = isset($dataColumns[$colIdx]) ? trim($dataColumns[$colIdx]) : '';
                $sizeId = $map['sizeId'];
                $type = $map['type'];
                $key = $spec->getId() . '_' . $sizeId;

                if (isset($existingByKey[$key])) {
                    $val = $existingByKey[$key];
                    if ($type === 'v') {
                        $val->setVTypeValue($cellValue ?: null);
                    } else {
                        $val->setHTypeValue($cellValue ?: null);
                    }
                    $val->setPosition($position);
                } elseif (isset($sizeValuesInRow[$key])) {
                    $val = $sizeValuesInRow[$key];
                    if ($type === 'v') {
                        $val->setVTypeValue($cellValue ?: null);
                    } else {
                        $val->setHTypeValue($cellValue ?: null);
                    }
                } else {
                    $val = new ProductSpecValue();
                    $val->setSubproduct($subproduct);
                    $val->setSpecification($spec);
                    $val->setProductSize($sizesById[$sizeId]);
                    if ($type === 'v') {
                        $val->setVTypeValue($cellValue ?: null);
                    } else {
                        $val->setHTypeValue($cellValue ?: null);
                    }
                    $val->setPosition($position);
                    $this->em->persist($val);
                    $sizeValuesInRow[$key] = $val;
                    $existingByKey[$key] = $val;
                }
            }
            $importedCount++;
            $io->text("  Row $importedCount: $specName");
        }

        $this->em->flush();

        $io->success("CSV imported successfully! $importedCount rows processed. $createdSizes sizes created. $createdSpecs specs created.");
        return Command::SUCCESS;
    }
}
