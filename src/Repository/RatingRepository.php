<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rating|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rating|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rating[]    findAll()
 * @method Rating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatingRepository extends ServiceEntityRepository implements RatingRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Rating::class);
    }

    public function findAllReviews(): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.content IS NOT NULL');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function create(Rating $rating): Rating
    {
        $this->em->persist($rating);
        $this->em->flush();

        return $rating;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(Rating $rating): void
    {
        $this->em->remove($rating);
        $this->em->flush();
    }
}
