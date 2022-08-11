<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\FoodstuffRepositoryInterface;
use App\Tests\Functional\AuthAdminWebTestCase;

class FoodstuffControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/voedingsmiddel');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/admin/voedingsmiddel/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Voedingsmiddel opslaan');

        $form = $buttonCrawlerNode->form();

        $form['foodstuff[name]'] = 'testTitle';

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/voedingsmiddel');

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $foodstuff = $foodstuffRepository->findOneBy(['name' => 'testTitle']);
        $id = $foodstuff->getId();

        $crawler = $this->client->request('GET', '/admin/voedingsmiddel/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $updatedName = 'testTitle2';
        $form['foodstuff[name]'] = $updatedName;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/voedingsmiddel');

        $foodstuff = $foodstuffRepository->findOneBy(['name' => $updatedName]);

        $this->assertEquals($updatedName, $foodstuff->getName());

        $crawler = $this->client->request('GET', '/admin/voedingsmiddel/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/voedingsmiddel');

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $this->assertNull($foodstuffRepository->find($id));
    }
}
