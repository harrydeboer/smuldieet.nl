<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class Purger extends ORMPurger
{
    /**
     * {@inheritDoc}
     */
    public function __construct(
        private readonly EntityManagerInterface $em,
        array $excluded = [])
    {
        parent::__construct($this->em, $excluded);
    }

    /**
     * Purges the MySQL database with temporarily disabled foreign key checks.
     *
     * {@inheritDoc}
     * @throws Exception
     */
    public function purge(): void
    {
        $connection = $this->em->getConnection();

        try {
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');

            parent::purge();
        } catch (Exception) {
        }
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
