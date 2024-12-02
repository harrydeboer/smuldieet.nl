<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Purger\PurgerFactory as BasePurgerFactory;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
use Doctrine\ORM\EntityManagerInterface;

class PurgerFactory implements BasePurgerFactory
{
    /**
     * Adapted from {@see \Doctrine\Bundle\FixturesBundle\Purger\ORMPurgerFactory}
     * to return a MySQL-specific {@see PurgerInterface}.
     *
     * {@inheritDoc}
     */
    public function createForEntityManager(
        ?string $emName,
        EntityManagerInterface $em,
        array $excluded = [],
        bool $purgeWithTruncate = false
    ): PurgerInterface {
        $purger = new Purger($em, $excluded);
        $purger->oRMPurger->setPurgeMode($purgeWithTruncate ?
            ORMPurger::PURGE_MODE_TRUNCATE :
            ORMPurger::PURGE_MODE_DELETE);

        return $purger;
    }
}
