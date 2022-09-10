<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Entity\Recipe;
use App\Tests\Functional\KernelTestCase;
use App\Service\UploadedImageService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class UploadedImageServiceTest extends KernelTestCase
{
    public function testGetIdString(): void
    {
        $recipe = new Recipe();
        $recipe->setId(1);
        $this->assertNull(UploadedImageService::getIdString($recipe));

        $recipe->setImageExtension('jpg');
        $this->assertEquals('1', UploadedImageService::getIdString($recipe));

        $this->assertEquals('1_100', UploadedImageService::getIdString($recipe, 100));
    }
}
