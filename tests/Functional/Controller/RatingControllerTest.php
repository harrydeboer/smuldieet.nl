<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Factory\RatingFactory;
use App\Tests\Functional\WebTestCase;

class RatingControllerTest extends WebTestCase
{
    public function testSingle(): void
    {
        $review = static::getContainer()->get(RatingFactory::class)
            ->create(['content' => 'test', 'pending' => false]);

        $this->client->request('GET', '/recensie/enkel/' . $review->getId());

        $this->assertResponseIsSuccessful();
    }
}
