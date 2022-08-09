<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\WebTestCase;

class RegisterSecurityChangePasswordTest extends WebTestCase
{
    public function testRegisterLoginLogout(): void
    {
        $crawler = $this->client->request('GET', '/registreren');

        $buttonCrawlerNode = $crawler->selectButton('Registreer');

        $form = $buttonCrawlerNode->form();

        $form['registration[username]'] = 'John';
        $form['registration[email]'] = 'john@secret.com';
        $form['registration[birthday]'] = '01-01-1990';
        $form['registration[gender]'] = 'man';
        $form['registration[weight]'] = 70;
        $form['registration[plainPassword][first]'] = 'secret';
        $form['registration[plainPassword][second]'] = 'secret';

        $this->client->submit($form);

        $this->assertResponseRedirects('/');

        $this->client->request('GET', '/uitloggen');

        $this->assertResponseRedirects();

        $crawler = $this->client->request('GET', '/inloggen');

        $buttonCrawlerNode = $crawler->selectButton('Inloggen');

        $form = $buttonCrawlerNode->form();

        $form['login[email]'] = 'john@secret.com';
        $form['login[password]'] = 'secret';

        $this->client->submit($form);

        $this->assertResponseRedirects('/');

        $crawler = $this->client->request('GET', '/verander-wachtwoord');

        $buttonCrawlerNode = $crawler->selectButton('Verander wachtwoord');

        $form = $buttonCrawlerNode->form();

        $form['change_password[plainPassword][first]'] = 'newNew';
        $form['change_password[plainPassword][second]'] = 'newNew';

        $this->client->submit($form);

        $this->assertResponseRedirects('/');

        $this->client->request('GET', '/uitloggen');

        $this->assertResponseRedirects();
    }
}
