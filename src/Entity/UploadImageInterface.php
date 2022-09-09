<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadImageInterface
{
    public function getId(): int;

    public function getImageExtension(): ?string;

    public function setImageExtension(?string $imageExtension): void;

    /**
     * The image getter and setter have to exist for the form to work,
     * but the value of getImage is never used because a html input of type file cannot be prefilled.
     * The setter only sets the imageExtension, because it does not save the image.
     */
    public function getImage(): void;

    public function setImage(?UploadedFile $image): void;

    public function getImageWidths(): array;

    public function getEntityNameSnakeCase(): string;

    public function getImagePath(int $width = null, string $extraPath = ''): ?string;
}
