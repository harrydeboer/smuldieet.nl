<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Exception;

trait UploadImageTrait
{
    #[ORM\Column(type: "string", nullable: true)]
    protected ?string $imageExtension = null;

    public function getImageExtension(): ?string
    {
        return $this->imageExtension;
    }

    public function setImageExtension(?string $imageExtension): void
    {
        $this->imageExtension = $imageExtension;
    }

    /**
     * The image getter and setter have to exist for the form to work,
     * but the value of getImage is never used because a html input of type file cannot be prefilled.
     * The setter only sets the imageExtension, because it does not save the image.
     * The method moveImage saves the image.
     */
    public function getImage(): void
    {
    }
    public function setImage(?UploadedFile $image): void
    {
        if (!is_null($image)) {
            $this->setImageExtension($image->getClientOriginalExtension());
        }
    }

    /**
     * Get the path of the image with respect to the public folder.
     */
    public function getImagePath(string $appEnv, int $width = null): ?string
    {
        $idString = (string) $this->getId();
        if (!is_null($width)) {
            if (!in_array($width, $this::IMAGE_WIDTHS)) {
                throw new InvalidArgumentException('Specified width is not in entity constant.');
            }
            $widthString = (string) $width;
            $idString = $idString . '_' . $widthString;
        }
        $extraPath = '';
        if ($appEnv === 'test') {
            $extraPath = 'test/';
        }
        $classNameArray = explode('\\', get_class($this));

        if (is_null($this->getImageExtension())) {
            return null;
        }

        return 'uploads/' . strtolower($classNameArray[2]) . '/images/' . $extraPath .
            $idString . '.' . $this->getImageExtension();
    }

    public function unlinkImage(string $appEnv, string $projectDir): void
    {
        if (!is_null($this->getImagePath($appEnv))) {
            @unlink($projectDir . '/public/' . $this->getImagePath($appEnv));
            foreach ($this::IMAGE_WIDTHS as $width) {
                @unlink($projectDir . '/public/' . $this->getImagePath($appEnv, $width));
            }
        }
    }

    /**
     * Move the image and create resized images for IMAGE_WIDTHS property values.
     * @throws Exception
     */
    public function moveImage(string $appEnv, string $projectDir, ?UploadedFile $image, $oldExtension = null): void
    {
        if (!is_null($image)) {
            $id = (string) $this->getId();
            $extraPath = '';
            if ($appEnv === 'test') {
                $extraPath = 'test/';
            }
            if (!is_null($oldExtension)) {
                $newExtension = $this->getImageExtension();
                $this->setImageExtension($oldExtension);
                $this->unlinkImage($appEnv, $projectDir);
                $this->setImageExtension($newExtension);
            }
            $classNameArray = explode('\\', get_class($this));
            $image->move($projectDir . '/public/uploads/' . strtolower($classNameArray[2]) . '/images/' .
                $extraPath,$id . '.' . $image->getClientOriginalExtension());

            $extension = $image->getClientOriginalExtension();
            $path = $projectDir . '/public/' .
                $this->getImagePath($appEnv);
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
                throw new Exception('Geef alstublieft een geldig plaatje ' .
                    '(png, jp(eg), j(f)if, gif, bmp of webp).');
            }
            $x = imagesx($image);
            $y = imagesy($image);
            foreach ($this::IMAGE_WIDTHS as $width) {
                $dst = imagecreatetruecolor($width, (int)($y * $width / $x));
                imagecopyresampled($dst, $image, 0, 0, 0, 0,
                    $width, (int)($y * $width / $x), $x, $y);
                $path = $projectDir . '/public/' .
                    $this->getImagePath($appEnv, $width);
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
                    throw new Exception('Geef alstublieft een geldig plaatje ' .
                        '(png, jp(eg), j(f)if, gif, bmp of webp).');
                }
                imagedestroy($dst);
            }
            imagedestroy($image);
        }
    }
}
