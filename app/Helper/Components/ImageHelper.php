<?php

namespace ilhamrhmtkbr\App\Helper\Components;

use ilhamrhmtkbr\App\Exceptions\UploadImageException;

class ImageHelper
{
    public static function uploadCompressedImage(
        array $file,
        $destination = 'User',
        $quality = 15,
        $convertToWebP = true,
        $oldImagePath = null // Path gambar lama
    ) {
        $moveTo = __DIR__ . '/../../../public/assets/img/' . $destination;

        $imagePath = $file['tmp_name'];
        $imageName = uniqid();
        $originalExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $targetExtension = $convertToWebP ? 'webp' : $originalExtension;
        $targetPath = $moveTo . '/' . $imageName . '.' . $targetExtension;

        // Create destination directory if it doesn't exist
        if (!is_dir($moveTo)) {
            mkdir($moveTo, 0755, true);
        }

        // Delete old image if exists
        if ($oldImagePath) {
            $oldImageFullPath = __DIR__ . '/../../../public/assets/img/' . $oldImagePath;
            if (file_exists($oldImageFullPath)) {
                unlink($oldImageFullPath);
            }
        }

        // Compress and save the image
        switch ($file['type']) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($imagePath);
                break;
            default:
                throw new UploadImageException(['image' => 'Unsupported image type.']);
        }

        // Convert to WebP if required
        if ($convertToWebP) {
            if (!imagewebp($image, $targetPath, $quality)) {
                throw new UploadImageException(['image' => 'Failed to save WebP image.']);
            }
        } else {
            // Save in original format
            switch ($file['type']) {
                case 'image/jpeg':
                    imagejpeg($image, $targetPath, $quality);
                    break;
                case 'image/png':
                    imagepng($image, $targetPath, (int)($quality / 10)); // Quality for PNG: 0-9
                    break;
            }
        }

        // Free up memory
        imagedestroy($image);

        return $destination . '/' . $imageName . '.' . $targetExtension; // Return the path of the saved image
    }

    public static function delete(string $filename)
    {
        $fullPath = __DIR__ . '/../../../public/assets/img/' . $filename;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
