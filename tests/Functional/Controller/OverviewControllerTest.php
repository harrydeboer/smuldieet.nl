<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\AuthWebTestCase;

class OverviewControllerTest extends AuthWebTestCase
{
    public function testOverview(): void
    {
        $crawler = $this->client->request('GET', '/overzicht');

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Bekijk');

        $form = $buttonCrawlerNode->form();

        $form['start'] = '01-01-2000';
        $form['end'] = '01-02-2000';

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
    }
}
