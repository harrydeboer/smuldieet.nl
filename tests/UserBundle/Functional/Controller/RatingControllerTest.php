<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Tests\Factory\RatingFactory;
use App\Tests\Functional\AuthVerifiedWebTestCase;

class RatingControllerTest extends AuthVerifiedWebTestCase
{
    public function testRatings(): void
    {
        $this->client->request('GET', '/user/waarderingen');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', 'Waarderingen');
    }

    public function testSingle(): void
    {
        $rating = static::getContainer()->get(RatingFactory::class)->create(['user' => $this->user]);

        $this->client->request('GET', '/user/recensie/enkel/' . $rating->getId());

        $this->assertResponseIsSuccessful();
    }
}
