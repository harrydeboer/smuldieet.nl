<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\AuthUserWebTestCase;

class OverviewControllerTest extends AuthUserWebTestCase
{
    public function testOverview(): void
    {
        $crawler = $this->client->request('GET', '/overzicht');

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Bekijk');

        $form = $buttonCrawlerNode->form();

        $form['start'] = date('Y') . '-01-01';

        $form['end'] = date('Y') . '-02-01';

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
    }
}
