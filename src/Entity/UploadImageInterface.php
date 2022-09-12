<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadImageInterface
{
    public function getId(): int;

    public function getImageExtension(): ?string;

    public function setImageExtension(?string $imageExtension): void;

    public function getImage(): ?UploadedFile;

    public function setImage(?UploadedFile $image): void;

    public function getImageWidths(): array;

    public function getImageUrl(int $width = null, string $extraPath = ''): ?string;
}
