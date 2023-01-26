<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\NutrientRepositoryInterface;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;

class NutrientControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/voedingsstoffen');

        $this->assertResponseIsSuccessful();

        $nutrientRepository = $this->getContainer()->get(NutrientRepositoryInterface::class);

        $nutrient = $nutrientRepository->findOneBy(['name' => 'protein']);
        $id = $nutrient->getId();

        $crawler = $this->client->request('GET', '/admin/voedingsstof/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $updatedDisplayName = 'testDisplay';
        $form['nutrient[display_name]'] = $updatedDisplayName;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/voedingsstoffen');

        $nutrient = $nutrientRepository->findOneBy(['displayName' => $updatedDisplayName]);

        $this->assertEquals($updatedDisplayName, $nutrient->getDisplayName());
    }
}
