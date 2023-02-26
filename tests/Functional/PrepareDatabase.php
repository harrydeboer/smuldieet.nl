<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Repository\NutrientRepositoryInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PrepareDatabase
{
    public static function migrateAndSyncDb(ContainerInterface $container): void
    {
        $entityManager = $container
            ->get('doctrine')
            ->getManager();

        $schemaTool = new SchemaTool($entityManager);
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->updateSchema($metaData);

        $nutrientRepository = $container->get(NutrientRepositoryInterface::class);
        $nutrientRepository->sync();
    }

    public static function dropAndCreateDb(ContainerInterface $container): void
    {
        $entityManager = $container
            ->get('doctrine')
            ->getManager();

        $db = $entityManager->getConnection()->getDatabase();
        $entityManager->getConnection()->executeQuery('DROP DATABASE ' . $db);
        $entityManager->getConnection()->executeQuery('CREATE DATABASE ' . $db);

        $entityManager->close();
        $entityManager = null;
    }
}
