<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepositoryInterface;
use App\Service\UploadedImageService;
use App\Tests\Functional\WebTestCase;
use Symfony\Component\HttpFoundation\File\File;

class RegisterSecurityChangePasswordTest extends WebTestCase
{
    public function testRegisterLoginLogout(): void
    {
        $crawler = $this->client->request('GET', '/registreren');

        $buttonCrawlerNode = $crawler->selectButton('Registreren');

        $form = $buttonCrawlerNode->form();

        $testImagePath = __DIR__ . '/test.jpg';
        $form['registration[image]'] = new File($testImagePath);
        $form['registration[username]'] = 'John';
        $form['registration[email]'] = 'john@secret.com';
        $form['registration[birthdate][day]'] = 1;
        $form['registration[birthdate][month]'] = 1;
        $form['registration[birthdate][year]'] = 1990;
        $form['registration[gender]'] = 'man';
        $form['registration[weight]'] = 70;
        $form['registration[plain_password][first]'] = 'secret';
        $form['registration[plain_password][second]'] = 'secret';

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

        $form['change_password[plain_password][first]'] = 'newNew';
        $form['change_password[plain_password][second]'] = 'newNew';

        $this->client->submit($form);

        $this->assertResponseRedirects('/');

        $this->client->request('GET', '/uitloggen');

        $this->assertResponseRedirects();

        $userRepository = static::getContainer()->get(UserRepositoryInterface::class);
        $uploadedImageService = static::getContainer()->get(UploadedImageService::class);
        $user = $userRepository->find(1);
        $uploadedImageService->unlinkImage($user);
    }
}
