<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UploadImageInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class UploadedImageService
{
    public function __construct(
        private readonly KernelInterface $kernel,
    ) {
    }

    public static function getIdString(UploadImageInterface $entity, int $width = null): ?string
    {
        $idString = (string) $entity->getId();
        if (!is_null($width)) {
            if (!in_array($width, $entity->getImageWidths())) {
                throw new InvalidArgumentException('Specified width is not in entity constant.');
            }
            $widthString = (string) $width;
            $idString = $idString . '_' . $widthString;
        }

        if (is_null($entity->getImageExtension())) {
            return null;
        }

        return $idString;
    }

    public function unlinkImage(UploadImageInterface $entity): void
    {
        $extraPath = '';
        if ($this->kernel->getEnvironment() === 'test') {
            $extraPath = 'test/';
        }
        if (!is_null($entity->getImagePath())) {
            @unlink($this->kernel->getProjectDir() . '/public/' . $entity->getImagePath(null, $extraPath));
            foreach ($entity->getImageWidths() as $width) {
                @unlink($this->kernel->getProjectDir() . '/public/' . $entity->getImagePath($width, $extraPath));
            }
        }
    }

    /**
     * Move the image and create resized images for IMAGE_WIDTHS property values.
     * @throws Exception
     */
    public function moveImage(
        ?UploadedFile $image,
        UploadImageInterface $entity,
        $oldExtension = null,
    ): void
    {
        if (!is_null($image)) {
            $id = (string) $entity->getId();
            $extraPath = '';
            if ($this->kernel->getEnvironment() === 'test') {
                $extraPath = 'test/';
            }
            if (!is_null($oldExtension)) {
                $newExtension = $entity->getImageExtension();
                $entity->setImageExtension($oldExtension);
                $this->unlinkImage($entity);
                $entity->setImageExtension($newExtension);
            }
            $image->move($this->kernel->getProjectDir() . '/public/uploads/' .
                $entity->getEntityNameSnakeCase() . '/images/' .
                $extraPath,$id . '.' . $image->getClientOriginalExtension());

            $extension = $image->getClientOriginalExtension();
            $path = $this->kernel->getProjectDir() . '/public/' . $entity->getImagePath(null, $extraPath);
            if ($extension === 'png') {
                $image = imagecreatefrompng($path);
            } elseif ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'jpe'
                || $extension === 'jfif' || $extension === 'jif') {
                $image = imagecreatefromjpeg($path);
            } elseif ($extension === 'gif') {
                $image = imagecreatefromgif($path);
            } elseif ($extension === 'bmp') {
                $image = imagecreatefrombmp($path);
            } elseif ($extension === 'webp') {
                $image = imagecreatefromwebp($path);
            } else {
                throw new Exception('Geef alsjeblieft een geldig plaatje ' .
                    '(png, jp(eg), j(f)if, gif, bmp of webp).');
            }
            $x = imagesx($image);
            $y = imagesy($image);
            foreach ($entity->getImageWidths() as $width) {
                $dst = imagecreatetruecolor($width, (int)($y * $width / $x));
                imagecopyresampled($dst, $image, 0, 0, 0, 0,
                    $width, (int)($y * $width / $x), $x, $y);
                $path = $this->kernel->getProjectDir() . '/public/' . $entity->getImagePath($width, $extraPath);
                if ($extension === 'png') {
                    imagepng($dst, $path);
                } elseif ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'jpe'
                    || $extension === 'jfif' || $extension === 'jif') {
                    imagejpeg($dst, $path);
                } elseif ($extension === 'gif') {
                    imagegif($dst, $path);
                } elseif ($extension === 'bmp') {
                    imagebmp($dst, $path);
                } elseif ($extension === 'webp') {
                    imagewebp($dst, $path);
                } else {
                    throw new Exception('Geef alsjeblieft een geldig plaatje ' .
                        '(png, jp(eg), j(f)if, gif, bmp of webp).');
                }
                imagedestroy($dst);
            }
            imagedestroy($image);
        }
    }
}
