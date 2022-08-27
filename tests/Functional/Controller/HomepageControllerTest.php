<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Factory\PageFactory;
use App\Tests\Functional\WebTestCase;

class HomepageControllerTest extends WebTestCase
{
    public function testHomepage(): void
    {
        static::getContainer()->get(PageFactory::class)->create(['title' => 'Home', 'slug' => 'home']);

        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Show');

        $form = $buttonCrawlerNode->form();

        $form['sort'] = 'timestamp_DESC';

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/pagina/1');

        $this->assertResponseIsSuccessful();
    }
}
