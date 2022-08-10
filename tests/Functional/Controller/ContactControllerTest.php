<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testContactPage(): void
    {
        $crawler = $this->client->request('GET', '/contact');

        $buttonCrawlerNode = $crawler->selectButton('Send');

        $form = $buttonCrawlerNode->form();

        $form['contact[name]'] = 'John';
        $form['contact[subject]'] = 'Test';
        $form['contact[email]'] = 'test@test.com';
        $form['contact[message]'] = 'test message';

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
    }
}
