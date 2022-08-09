<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\WebTestCase;

class HomepageControllerTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Toon');

        $form = $buttonCrawlerNode->form();

        $form['sort'] = 'timestamp_DESC';

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/pagina/1');

        $this->assertResponseIsSuccessful();
    }
}
