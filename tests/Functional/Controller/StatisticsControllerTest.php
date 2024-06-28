<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\DayRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;

class StatisticsControllerTest extends AuthVerifiedWebTestCase
{
    public function testOverview(): void
    {
        static::getContainer()
            ->get(DayRepositoryInterface::class)
            ->findOneBy(['timestamp' => strtotime('11-11-2023 00:00:00')]);

        $crawler = $this->client->request('GET', '/statistieken');

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Bekijk');

        $form = $buttonCrawlerNode->form();

        $form['start'] =  '2023-01-01';

        $form['end'] = '2023-12-12';

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
    }
}
