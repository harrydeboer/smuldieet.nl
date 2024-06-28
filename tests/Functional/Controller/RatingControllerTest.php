<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\RatingRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RatingControllerTest extends WebTestCase
{
    public function testSingle(): void
    {
        $client = self::createClient();

        $review = static::getContainer()->get(RatingRepositoryInterface::class)
            ->findOneBy(['content' => 'test']);

        $client->request('GET', '/recensie/enkel/' . $review->getId());

        $this->assertResponseIsSuccessful();
    }
}
