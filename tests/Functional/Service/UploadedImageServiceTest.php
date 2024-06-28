<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Entity\Recipe;
use App\Service\UploadedImageService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class UploadedImageServiceTest extends KernelTestCase
{
    public function testMoveAndUnlinkImage(): void
    {
        $uploadedImageService = static::getContainer()->get(UploadedImageService::class);
        $kernel = static::getContainer()->get(KernelInterface::class);

        $testImagePath = dirname(__DIR__, 3) . '/public/uploads/test/test.jpg';
        $image = new UploadedFile($testImagePath, 'test.jpg', 'image/jpeg', 0, true);

        copy($testImagePath, dirname(__DIR__, 3) . '/public/uploads/test/test_tmp.jpg');

        $recipe = new Recipe();
        $recipe->setId(1);
        $recipe->setImageExtension('jpg');
        $recipe->setImage($image);

        $uploadedImageService->moveImage($recipe);

        rename(dirname(__DIR__, 3) . '/public/uploads/test/test_tmp.jpg',
            dirname(__DIR__, 3) . '/public/uploads/test/test.jpg');

        $this->assertTrue(file_exists($kernel->getProjectDir() . '/public/' .
            $recipe->getImageUrl(null, 'test/')));

        $uploadedImageService->unlinkImage($recipe);

        $this->assertFalse(file_exists($kernel->getProjectDir() . '/public/' .
            $recipe->getImageUrl(null, 'test/')));
    }
}
