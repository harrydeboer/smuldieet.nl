<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UploadImageInterface;
use Exception;
use GdImage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class UploadedImageService
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function unlinkImage(UploadImageInterface $entity): void
    {
        $extraPath = '';
        if ($this->parameterBag->get('kernel.environment') === 'test') {
            $extraPath = 'test/';
        }
        if (!is_null($entity->getImageUrl())) {
            @unlink($this->parameterBag->get('kernel.project_dir') . '/public/' .
                $entity->getImageUrl(null, $extraPath));
            foreach ($entity->getImageWidths() as $width) {
                @unlink($this->parameterBag->get('kernel.project_dir') . '/public/' .
                    $entity->getImageUrl($width, $extraPath));
            }
        }
    }

    /**
     * Move the image and create resized images for the getImageWidths entity method values.
     * @throws Exception
     */
    public function moveImage(
        UploadImageInterface $entity,
        ?string $oldExtension = null,
    ): void
    {
        $image = $entity->getImage();
        if (!is_null($image)) {
            $id = (string) $entity->getId();
            $extraPath = '';
            if ($this->parameterBag->get('kernel.environment') === 'test') {
                $extraPath = 'test/';
            }
            if (!is_null($oldExtension) && !is_null($entity->getImage())) {
                $newExtension = $entity->getImageExtension();
                $entity->setImageExtension($oldExtension);
                $this->unlinkImage($entity);
                $entity->setImageExtension($newExtension);
            }
            $filePath = dirname($this->parameterBag->get('kernel.project_dir') . '/public/' .
                $entity->getImageUrl(null, $extraPath));
            $fileName = $id . '.' . strtolower($image->getClientOriginalExtension());
            $image->move($filePath, $fileName);

            $extension = strtolower($image->getClientOriginalExtension());
            $path = $this->parameterBag->get('kernel.project_dir') . '/public/' .
                $entity->getImageUrl(null, $extraPath);
            if ($extension === 'png') {
                $image = imagecreatefrompng($path);
            } elseif ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'jpe'
                || $extension === 'jfif' || $extension === 'jif') {
                $image = imagecreatefromjpeg($path);
                $exifData = exif_read_data($filePath . '/' . $fileName);
                if ($exifData !== false) {
                    $image = $this->correctImageOrientation($exifData, $image);
                }
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
                $path = $this->parameterBag->get('kernel.project_dir') . '/public/' .
                    $entity->getImageUrl($width, $extraPath);
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
            }
        }

        $entity->setImage(null);
    }

    private function correctImageOrientation(array $exifData, GdImage $image): GdImage
    {
        if(isset($exifData['Orientation'])) {
            $orientation = $exifData['Orientation'];
            if($orientation != 1){
                $deg = 0;
                switch ($orientation) {
                    case 3:
                        $deg = 180;
                        break;
                    case 6:
                        $deg = 270;
                        break;
                    case 8:
                        $deg = 90;
                        break;
                }
                if ($deg) {
                    return imagerotate($image, $deg, 0);
                }
            }
        }

        return $image;
    }
}
