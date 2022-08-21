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

        $form['start[day]'] = 1;
        $form['start[month]'] = 1;
        $form['start[year]'] = date('Y') - 3;

        $form['end[day]'] = 1;
        $form['end[month]'] = 2;
        $form['end[year]'] = date('Y') - 2;

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
    }
}
