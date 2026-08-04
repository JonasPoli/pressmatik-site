<?php

namespace App\Command;

use App\Service\ImageOptimizer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:optimize-images',
    description: 'Compresses and resizes images in public/images and public/uploads for maximum PageSpeed performance.'
)]
class OptimizeImagesCommand extends Command
{
    public function __construct(
        private readonly ImageOptimizer $imageOptimizer,
        #[Autowire(param: 'kernel.project_dir')]
        private readonly string $projectDir
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Optimizing Site Images for Google PageSpeed');

        $directories = [
            $this->projectDir . '/public/uploads',
        ];

        $finder = new Finder();
        $finder->files()
            ->in($directories)
            ->name(['*.png', '*.jpg', '*.jpeg', '*.webp']);

        $totalOriginalBytes = 0;
        $totalOptimizedBytes = 0;
        $processedCount = 0;

        foreach ($finder as $file) {
            $filePath = $file->getRealPath();
            $originalSize = $file->getSize();

            // Set max dimensions based on path
            $maxWidth = 1200;
            $maxHeight = 1200;

            if (str_contains($filePath, 'banners')) {
                $maxWidth = 960;
                $maxHeight = 800;
            } elseif (str_contains($filePath, 'megamenu') || str_contains($filePath, 'logos') || str_contains($filePath, 'clients') || str_contains($filePath, 'suppliers')) {
                $maxWidth = 480;
                $maxHeight = 400;
            } elseif (str_contains($filePath, 'services') || str_contains($filePath, 'about')) {
                $maxWidth = 800;
                $maxHeight = 700;
            }

            $success = $this->imageOptimizer->optimize($filePath, $maxWidth, $maxHeight, 78);

            if ($success) {
                clearstatcache(true, $filePath);
                $newSize = filesize($filePath);
                $totalOriginalBytes += $originalSize;
                $totalOptimizedBytes += $newSize;
                $processedCount++;

                if ($originalSize > $newSize + 10240) {
                    $savedKb = round(($originalSize - $newSize) / 1024, 1);
                    $io->writeln(sprintf(
                        ' <info>✔</info> Optimized <comment>%s</comment>: %s → %s (Saved <fg=green>%s KB</fg=green>)',
                        $file->getRelativePathname(),
                        $this->formatBytes($originalSize),
                        $this->formatBytes($newSize),
                        $savedKb
                    ));
                }
            }
        }

        $savedTotalBytes = max(0, $totalOriginalBytes - $totalOptimizedBytes);
        $savedTotalMb = round($savedTotalBytes / 1024 / 1024, 2);

        $io->success(sprintf(
            'Processed %d images. Original: %s → New: %s. Total Saved: %s MB!',
            $processedCount,
            $this->formatBytes($totalOriginalBytes),
            $this->formatBytes($totalOptimizedBytes),
            $savedTotalMb
        ));

        return Command::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }
}
