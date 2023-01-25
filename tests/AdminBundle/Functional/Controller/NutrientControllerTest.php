<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\NutrientRepositoryInterface;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;
use App\Tests\Factory\NutrientFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NutrientControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $this->getContainer()->get(NutrientFactory::class)->create(['name' => 'protein']);

        $this->client->request('GET', '/admin/voedingsstoffen');

        $this->assertResponseIsSuccessful();

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
    }
}
