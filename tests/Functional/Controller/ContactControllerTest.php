<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testContactPage(): void
    {
        $crawler = $this->client->request('GET', '/contact');

        $buttonCrawlerNode = $crawler->selectButton('Verzenden');

        $form = $buttonCrawlerNode->form();

        $form['contact[name]'] = 'John';
        $form['contact[subject]'] = 'Test';
        $form['contact[email]'] = 'test@test.com';
        $form['contact[message]'] = 'test message';
        $form['contact[re_captcha_token]'] = 'test token';

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
    }
}
