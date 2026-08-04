<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class ImageOptimizer
{
    public function __construct(
        private readonly ?LoggerInterface $logger = null
    ) {}

    /**
     * Optimizes an image in-place (resizes if exceeds maxWidth/maxHeight and compresses quality).
     */
    public function optimize(string $filePath, int $maxWidth = 1200, int $maxHeight = 1200, int $quality = 82): bool
    {
        if (!file_exists($filePath) || !is_file($filePath)) {
            return false;
        }

        $imageInfo = @getimagesize($filePath);
        if (!$imageInfo) {
            return false;
        }

        [$width, $height, $type] = $imageInfo;

        if ($width <= 0 || $height <= 0) {
            return false;
        }

        // Determine if resizing is needed
        $needsResize = ($width > $maxWidth || $height > $maxHeight);

        // Load image resource
        $srcImage = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($filePath),
            IMAGETYPE_PNG => @imagecreatefrompng($filePath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($filePath),
            default => null,
        };

        if (!$srcImage) {
            return false;
        }

        // Calculate new dimensions
        if ($needsResize) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = (int) round($width * $ratio);
            $newHeight = (int) round($height * $ratio);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Create target image
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Handle transparency for PNG and WebP
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resample
        imagecopyresampled(
            $dstImage,
            $srcImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $width,
            $height
        );

        // Save back to original format and generate WebP sibling
        $saved = false;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $saved = imagejpeg($dstImage, $filePath, $quality);
                break;
            case IMAGETYPE_PNG:
                imagesavealpha($dstImage, true);
                $saved = imagepng($dstImage, $filePath, 9);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagewebp')) {
                    $saved = imagewebp($dstImage, $filePath, $quality);
                }
                break;
        }

        // Always generate WebP version if imagewebp is available
        if (function_exists('imagewebp') && $type !== IMAGETYPE_WEBP) {
            $webpPath = pathinfo($filePath, PATHINFO_DIRNAME) . '/' . pathinfo($filePath, PATHINFO_FILENAME) . '.webp';
            @imagewebp($dstImage, $webpPath, $quality);
        }

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return $saved;
    }
}
