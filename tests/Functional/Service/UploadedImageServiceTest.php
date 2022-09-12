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
    public function testMoveAndUnlinkImage(): void
    {
        $uploadedImageService = static::getContainer()->get(UploadedImageService::class);
        $kernel = static::getContainer()->get(KernelInterface::class);

        $testImagePath = __DIR__ . '/test.jpg';
        $image = new UploadedFile($testImagePath, 'test.jpg', 'image/jpeg', 0, true);

        copy($testImagePath, __DIR__ . '/test_tmp.jpg');

        $recipe = new Recipe();
        $recipe->setId(1);
        $recipe->setImageExtension('jpg');
        $recipe->setImage($image);

        $uploadedImageService->moveImage($recipe);

        rename(__DIR__ . '/test_tmp.jpg', __DIR__ . '/test.jpg');

        $this->assertTrue(file_exists($kernel->getProjectDir() . '/public/' .
            $recipe->getImageUrl(null, 'test/')));

        $uploadedImageService->unlinkImage($recipe);

        $this->assertFalse(file_exists($kernel->getProjectDir() . '/public/' .
            $recipe->getImageUrl(null, 'test/')));
    }
}
