<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Factory\DayFactory;
use App\Tests\Functional\AuthUserWebTestCase;
use DateTime;

class OverviewControllerTest extends AuthUserWebTestCase
{
    public function testOverview(): void
    {
        $date = new DateTime();
        $date->setDate((int) date('Y'), (int) date('m'), 10);
        static::getContainer()->get(DayFactory::class)->create(['user' => $this->user,'date' => $date]);

        $crawler = $this->client->request('GET', '/overzicht');

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Bekijk');

        $form = $buttonCrawlerNode->form();

        $form['start'] = date('Y') . '-' . date('m') . '-01';

        $form['end'] = date('Y') . '-' . date('m') . '-28';

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
    }
}
