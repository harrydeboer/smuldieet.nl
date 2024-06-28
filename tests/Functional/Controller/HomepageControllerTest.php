<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\PageRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomepageControllerTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();

        $page = static::getContainer()->get(PageRepositoryInterface::class)->findOneBy(['title' => 'Home']);

        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Show');

        $form = $buttonCrawlerNode->form();

        $form['sort'] = 'createdAt_DESC';

        $client->submit($form);

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/pagina/' . $page->getId());

        $this->assertResponseIsSuccessful();
    }
}
