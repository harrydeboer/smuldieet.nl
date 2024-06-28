<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\UserRepositoryInterface;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserControllerTest extends AuthAdminWebTestCase
{
    private function getUserRepository(): UserRepositoryInterface
    {
        return static::getContainer()->get(UserRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/gebruikers');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/admin/gebruiker/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Gebruiker opslaan');

        $form = $buttonCrawlerNode->form();

        $tmp = 'secret';

        $form['create_user[username]'] = 'TestTest';
        $form['create_user[first_name]'] = 'Test';
        $form['create_user[last_name]'] = 'Test';
        $form['create_user[email]'] = 'test@test.com';
        $form['create_user[verified]'] = 1;
        $form['create_user[roles]'] = ['ROLE_ADMIN'];
        $form['create_user[birthdate][day]'] = 1;
        $form['create_user[birthdate][month]'] = 1;
        $form['create_user[birthdate][year]'] = 2000;
        $form['create_user[gender]'] = 'man';
        $form['create_user[weight]'] = 70;
        $form['create_user[plain_password]'] = ['first' => $tmp, 'second' => $tmp];

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/gebruikers');

        $userRepository = $this->getContainer()->get(UserRepositoryInterface::class);

        $user = $userRepository->findOneBy(['email' => 'test@test.com']);
        $id = $user->getId();

        $crawler = $this->client->request('GET', '/admin/gebruiker/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $updatedName = 'Test2';
        $form['update_user[username]'] = $updatedName;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/gebruikers');

        $userRepository = $this->getContainer()->get(UserRepositoryInterface::class);

        $user = $userRepository->findOneBy(['email' => 'test@test.com']);

        $this->assertEquals($updatedName, $user->getUsername());

        $crawler = $this->client->request('GET', '/admin/gebruiker/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/gebruikers');

        $userRepository = $this->getUserRepository();

        $this->expectException(NotFoundHttpException::class);

        $userRepository->get($id);
    }
}
