<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Factory\RatingFactory;
use App\Tests\Functional\WebTestCase;

class RatingControllerTest extends WebTestCase
{
    public function testSingle(): void
    {
        $rating = static::getContainer()->get(RatingFactory::class)->create();

        $this->client->request('GET', '/waardering/enkel/' . $rating->getId());

        $this->assertResponseIsSuccessful();
    }
}
