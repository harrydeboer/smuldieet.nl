<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

trait ImageTrait
{
    #[
        ORM\Id,
        ORM\Column(type: "integer"),
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $imageExtension = null;

    public function getImageExtension(): ?string
    {
        return $this->imageExtension;
    }

    public function setImageExtension(?string $imageExtension): void
    {
        $this->imageExtension = $imageExtension;
    }

    /**
     * The image getter and setter have to exist for the wine form to work,
     * but the value of getLabel is never used because a html input of type file cannot be prefilled.
     * The setter only sets the imageExtension, because it does not save the image.
     * The function moveLabel saves the image.
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

    public function moveImage(?UploadedFile $image, string $appEnv, string $projectDir): void
    {
        if (!is_null($image)) {
            $id = (string) $this->getId();
            $extraPath = '';
            if ($appEnv === 'test') {
                $extraPath = 'test/';
            }
            $classNameArray = explode('\\', get_class($this));
            $image->move(
                $projectDir . '/public/uploads/' . strtolower($classNameArray[2]) . '/images/' . $extraPath,
                $id . '.' . $image->getClientOriginalExtension()
            );
        }
    }

    public function getImagePath(string $appEnv, int $width = null): ?string
    {
        $idString = (string) $this->getId();
        if (!is_null($width)) {
            if (!in_array($width, self::IMAGE_WIDTHS)) {
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
            foreach (self::IMAGE_WIDTHS as $width) {
                @unlink($projectDir . '/public/' . $this->getImagePath($appEnv, $width));
            }
        }
    }
}
