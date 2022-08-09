<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Exception;

trait MigrationsTrait
{
    protected ?EntityManager $entityManager;

    protected function migrateDb(): void
    {
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $schemaTool = new SchemaTool($this->entityManager);
        $metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->updateSchema($metaData);
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function dropAndCreateDb(): void
    {
        $db = $this->entityManager->getConnection()->getDatabase();
        $this->entityManager->getConnection()->executeQuery('DROP DATABASE ' . $db);
        $this->entityManager->getConnection()->executeQuery('CREATE DATABASE ' . $db);

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
