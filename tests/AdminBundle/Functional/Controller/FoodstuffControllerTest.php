<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Factory\FoodstuffFactory;
use App\Repository\FoodstuffRepositoryInterface;
use App\Tests\Functional\AuthAdminWebTestCase;

class FoodstuffControllerTest extends AuthAdminWebTestCase
{
    public function testUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/voedingsmiddelen');

        $this->assertResponseIsSuccessful();

        $foodstuff = static::getContainer()->get(FoodstuffFactory::class)->create();

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $id = $foodstuff->getId();

        $crawler = $this->client->request('GET', '/admin/voedingsmiddel/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $updatedName = 'testTitle2';
        $form['foodstuff[name]'] = $updatedName;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/voedingsmiddelen');

        $foodstuff = $foodstuffRepository->findOneBy(['name' => $updatedName]);

        $this->assertEquals($updatedName, $foodstuff->getName());

        $crawler = $this->client->request('GET', '/admin/voedingsmiddel/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/voedingsmiddelen');

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $this->assertNull($foodstuffRepository->find($id));
    }
}
