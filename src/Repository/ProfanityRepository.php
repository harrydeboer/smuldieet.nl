<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Profanity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Profanity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profanity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profanity[]    findAll()
 * @method Profanity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfanityRepository extends ServiceEntityRepository implements ProfanityRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Profanity::class);
    }


    public function create(Profanity $profanity): Profanity
    {
        $this->em->persist($profanity);
        $this->em->flush();

        return $profanity;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(Profanity $profanity): void
    {
        $this->em->remove($profanity);
        $this->em->flush();
    }
}
