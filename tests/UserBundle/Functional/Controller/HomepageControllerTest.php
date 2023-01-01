<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Repository\UserRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;

class HomepageControllerTest extends AuthVerifiedWebTestCase
{
    public function testHomepage(): void
    {
        $crawler = $this->client->request('GET', '/user/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', 'Kookboeken');

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $firstName = 'test';
        $lastName = 'tester';
        $form['user[first_name]'] = $firstName;
        $form['user[last_name]'] = $lastName;

        $this->client->submit($form);

        $this->assertResponseRedirects('/user/');

        $userRepository = $this->getContainer()->get(UserRepositoryInterface::class);
        $user = $userRepository->findOneBy(['id' => $this->user->getId()]);

        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($lastName, $user->getLastName());
    }
}
