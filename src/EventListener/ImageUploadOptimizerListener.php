<?php

namespace App\EventListener;

use App\Service\ImageOptimizer;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Events::POST_UPLOAD, method: 'onPostUpload')]
class ImageUploadOptimizerListener
{
    public function __construct(
        private readonly ImageOptimizer $imageOptimizer
    ) {}

    public function onPostUpload(Event $event): void
    {
        $mapping = $event->getMapping();
        $object = $event->getObject();
        $file = $mapping->getFile($object);

        if (!$file) {
            return;
        }

        $filePath = $file->getRealPath() ?: ($mapping->getUploadDestination() . '/' . $mapping->getFileName($object));

        if (!file_exists($filePath)) {
            return;
        }

        $mime = mime_content_type($filePath);
        if ($mime && str_starts_with($mime, 'image/')) {
            // Determine max dimensions based on mapping context
            $mappingName = $mapping->getMappingName();
            $maxWidth = 1200;
            $maxHeight = 1200;

            if (str_contains($mappingName, 'banner')) {
                $maxWidth = 1600;
                $maxHeight = 1200;
            } elseif (str_contains($mappingName, 'megamenu') || str_contains($mappingName, 'logo') || str_contains($mappingName, 'supplier') || str_contains($mappingName, 'client')) {
                $maxWidth = 600;
                $maxHeight = 600;
            }

            $this->imageOptimizer->optimize($filePath, $maxWidth, $maxHeight, 82);
        }
    }
}
