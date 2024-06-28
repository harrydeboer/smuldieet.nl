<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\FoodstuffRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FoodstuffControllerTest extends AuthVerifiedWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $foodstuff = static::getContainer()
            ->get(FoodstuffRepositoryInterface::class)
            ->findOneBy(['name' => 'verified']);
        $foodstuffAnonymous = static::getContainer()
            ->get(FoodstuffRepositoryInterface::class)
            ->findOneBy(['name' => 'anonymous']);

        $this->client->request('GET', '/voedingsmiddelen');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/voedingsmiddel/combineer-voedingsmiddelen');

        $buttonCrawlerNode = $crawler->selectButton('Voedingsmiddel opslaan');

        $form = $buttonCrawlerNode->form();

        $form['combine_foodstuffs[name]'] = 'test2';

        $values = $form->getPhpValues();
        $values['combine_foodstuffs']['foodstuff_weights'][0]['foodstuff_id'] = $foodstuff->getId();
        $values['combine_foodstuffs']['foodstuff_weights'][0]['value'] = 100;
        $values['combine_foodstuffs']['foodstuff_weights'][0]['unit'] = 'g';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $foodstuffCombined = $foodstuffRepository->getByName('test2');

        $this->assertResponseRedirects('/voedingsmiddel/wijzig/' . $foodstuffCombined->getId());

        $this->client->request('GET', '/uitloggen');

        $this->client->request('GET', '/voedingsmiddelen');

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/voedingsmiddelen/letter/B');

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/voedingsmiddel/enkel/' . $foodstuffAnonymous->getId());

        $this->assertResponseIsSuccessful();

        $this->client->loginUser($this->user);

        $this->client->xmlHttpRequest('GET', '/voedingsmiddel/zoeken/test2');

        $this->assertResponseIsSuccessful();

        $foodstuff = $foodstuffRepository->findOneBy(['name' => $foodstuff->getName()]);
        $id = $foodstuff->getId();

        $this->client->request('GET', '/voedingsmiddel/enkel/' . $id);

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/voedingsmiddel/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig voedingsmiddel');

        $form = $buttonCrawlerNode->form();

        $updatedName = 'testUpdate';
        $form['foodstuff[name]'] = $updatedName;

        $this->client->submit($form);

        $this->assertResponseRedirects('/voedingsmiddelen');

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $foodstuff = $foodstuffRepository->findOneBy(['name' => $updatedName]);

        $this->assertEquals($updatedName, $foodstuff->getName());

        $crawler = $this->client->request('GET', '/voedingsmiddel/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/voedingsmiddelen');

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $this->expectException(NotFoundHttpException::class);

        $foodstuffRepository->get($id);
    }
}
