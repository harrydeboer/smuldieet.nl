<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\DayRepositoryInterface;
use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;
use DateTime;

class DayControllerTest extends AuthVerifiedWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $foodstuff = static::getContainer()
            ->get(FoodstuffRepositoryInterface::class)
            ->findOneBy(['name' => 'verified']);
        $recipe = static::getContainer()
            ->get(RecipeRepositoryInterface::class)
            ->findOneBy(['pending' => false]);
        $this->client->request('GET', '/dagboek');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/dag/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Dag opslaan');

        $form = $buttonCrawlerNode->form();

        $values = $form->getPhpValues();
        $date = new DateTime();
        $date->setTimestamp(strtotime(date('Y') . '-01-01'));
        $values['day']['date'] = date('Y') . '-01-01';
        $values['day']['foodstuff_weights'][0]['foodstuff_id'] = $foodstuff->getId();
        $values['day']['foodstuff_weights'][0]['value'] = 10;
        $values['day']['foodstuff_weights'][0]['unit'] = 'g';
        $values['day']['recipe_weights'][0]['recipe_id'] = $recipe->getId();
        $values['day']['recipe_weights'][0]['value'] = 2;

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseRedirects('/dagboek');

        $crawler = $this->client->request('GET', '/dag/toevoegen/standaard');

        $buttonCrawlerNode = $crawler->selectButton('Standaard dag opslaan');

        $form = $buttonCrawlerNode->form();

        $values = $form->getPhpValues();
        $values['standard_day']['foodstuff_weights'][0]['foodstuff_id'] = $foodstuff->getId();
        $values['standard_day']['foodstuff_weights'][0]['value'] = 12;
        $values['standard_day']['foodstuff_weights'][0]['unit'] = 'kg';
        $values['standard_day']['recipe_weights'][0]['recipe_id'] = $recipe->getId();
        $values['standard_day']['recipe_weights'][0]['value'] = 3;

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseRedirects('/dagboek');

        $dayRepository = $this->getContainer()->get(DayRepositoryInterface::class);

        $day = $dayRepository->findOneBy(['timestamp' => $date->getTimestamp()]);
        $id = $day->getId();

        $this->client->request('GET', '/dagboek/pagina/1');

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/dag/enkel/' . $id);

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/dag/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig dag');

        $form = $buttonCrawlerNode->form();

        $updatedDate = new DateTime();
        $updatedDate->setTimestamp(strtotime(date('Y') . '-01-02'));
        $form['day[date]'] = date('Y') . '-01-02';

        $this->client->submit($form);

        $this->assertResponseRedirects('/dagboek');

        $day = $dayRepository->findOneBy(['timestamp' => $updatedDate->getTimestamp()]);

        $this->assertEquals($updatedDate, $day->getDate());

        $crawler = $this->client->request('GET', '/dag/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/dagboek');

        $dayRepository = $this->getContainer()->get(DayRepositoryInterface::class);

        $this->assertNull($dayRepository->find($id));
    }
}
