<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Purger\ORMPurgerInterface;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class Purger implements ORMPurgerInterface
{
    public ORMPurger $oRMPurger;

    public function __construct(
        private readonly EntityManagerInterface $em,
        array $excluded,
    ){
        $this->oRMPurger = new ORMPurger($em, $excluded);
    }

    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->oRMPurger->setEntityManager($em);
    }

    /**
     * Purges the MySQL database with temporarily disabled foreign key checks.
     *
     * @throws Exception
     */
    public function purge(): void
    {
        $connection = $this->em->getConnection();

        try {
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');

            $this->oRMPurger->purge();
        } catch (Exception) {
        }
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
