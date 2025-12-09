<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepositoryInterface;
use App\Service\UploadedImageService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\File;

class RegisterSecurityChangePasswordTest extends WebTestCase
{
    public function testRegisterLoginChangePasswordLogout(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/registreren');

        $buttonCrawlerNode = $crawler->selectButton('Registreren');

        $form = $buttonCrawlerNode->form();

        $tmp = 'secret';

        $testImagePath = dirname(__DIR__, 3) . '/public/uploads/test/test.jpg';
        $form['registration[image]'] = new File($testImagePath);
        $form['registration[username]'] = 'John';
        $form['registration[email]'] = 'john@secret.com';
        $form['registration[birthdate][day]'] = 1;
        $form['registration[birthdate][month]'] = 1;
        $form['registration[birthdate][year]'] = 1990;
        $form['registration[gender]'] = 'man';
        $form['registration[weight]'] = 70;
        $form['registration[plain_password][first]'] = $tmp;
        $form['registration[plain_password][second]'] = $tmp;

        $client->submit($form);

        $this->assertResponseRedirects('/');

        $this->assertResponseRedirects('/');

        $client->request('GET', '/uitloggen');

        $crawler = $client->request('GET', '/inloggen');

        $buttonCrawlerNode = $crawler->selectButton('Inloggen');

        $form = $buttonCrawlerNode->form();

        $form['_username'] = 'john@secret.com';
        $form['_password'] = $tmp;

        $client->submit($form);

        $this->assertResponseRedirects();

        $crawler = $client->request('GET', '/verander-wachtwoord');

        $buttonCrawlerNode = $crawler->selectButton('Verander wachtwoord');

        $form = $buttonCrawlerNode->form();

        $tmp = 'newNew';

        $form['change_password[plain_password][first]'] = $tmp;
        $form['change_password[plain_password][second]'] = $tmp;

        $client->submit($form);

        $this->assertResponseRedirects('/');

        $client->request('GET', '/uitloggen');

        $this->assertResponseRedirects();

        $crawler = $client->request('GET', '/inloggen');

        $buttonCrawlerNode = $crawler->selectButton('Inloggen');

        $form = $buttonCrawlerNode->form();

        $form['_username'] = 'john@secret.com';
        $form['_password'] = $tmp;

        $client->submit($form);

        $this->assertResponseRedirects();

        $userRepository = static::getContainer()->get(UserRepositoryInterface::class);
        $uploadedImageService = static::getContainer()->get(UploadedImageService::class);
        $user = $userRepository->findOneBy(['username' => 'John']);
        $uploadedImageService->unlinkImage($user);
    }
}
