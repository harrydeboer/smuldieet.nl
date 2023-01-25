<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\NutrientRepositoryInterface;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NutrientControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/voedingsstoffen');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/admin/voedingsstof/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Voedingsstof opslaan');

        $form = $buttonCrawlerNode->form();

        $form['nutrient[name]'] = 'protein';
        $form['nutrient[name_nl]'] = 'eiwit';
        $form['nutrient[min_rda]'] = 7.0;
        $form['nutrient[max_rda]'] = 10.0;
        $form['nutrient[unit]'] = 'g';
        $form['nutrient[decimal_places]'] = 2;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/voedingsstoffen');

        $nutrientRepository = $this->getContainer()->get(NutrientRepositoryInterface::class);

        $nutrient = $nutrientRepository->findOneBy(['name' => 'protein']);
        $id = $nutrient->getId();

        $crawler = $this->client->request('GET', '/admin/voedingsstof/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $updatedName = 'testNameNL2';
        $form['nutrient[name_nl]'] = $updatedName;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/voedingsstoffen');

        $nutrient = $nutrientRepository->findOneBy(['nameNL' => 'testNameNL2']);

        $this->assertEquals($updatedName, $nutrient->getNameNL());

        $crawler = $this->client->request('GET', '/admin/voedingsstof/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/voedingsstoffen');

        $nutrientRepository = $this->getContainer()->get(NutrientRepositoryInterface::class);

        $this->expectException(NotFoundHttpException::class);

        $nutrientRepository->get($id);
    }
}
