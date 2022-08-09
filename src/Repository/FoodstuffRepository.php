<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Foodstuff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Foodstuff|null find($id, $lockMode = null, $lockVersion = null)
 * @method Foodstuff|null findOneBy(array $criteria, array $orderBy = null)
 * @method Foodstuff[]    findAll()
 * @method Foodstuff[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodstuffRepository extends ServiceEntityRepository implements FoodstuffRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Foodstuff::class);
    }

    public function findAllStartingWith(string $char, ?int $userId): array
    {
        $qb = $this->createQueryBuilder("f")
            ->where("Lower(f.name) like :name")
            ->setParameter("name", strtolower($char) . "%");

        if (!is_null($userId)) {
            $qb->andWhere("f.user = " . $userId . " or f.user IS NULL");
        }
        $qb->orderBy("f.name", "ASC");

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findAllFromUser(?int $userId): array
    {
        $qb = $this->createQueryBuilder("f");
        if (is_null($userId)) {
            $qb->where("f.user IS NULL");
        } else {
            $qb->where("f.user = :userId or f.user IS NULL")
                ->setParameter("userId", $userId);
        }
        $qb->orderBy("f.name", "ASC");

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function get(int $id): Foodstuff
    {
        $foodstuff = $this->findOneBy(["id" => $id]);

        if (is_null($foodstuff)) {
            throw new NotFoundHttpException("Dit voedingsmiddel bestaat niet.");
        }

        return $foodstuff;
    }

    public function getByName(string $name): Foodstuff
    {
        $foodstuff = $this->findOneBy(["name" => $name]);

        if (is_null($foodstuff)) {
            throw new NotFoundHttpException("Dit voedingsmiddel bestaat niet of hoort niet bij jou.");
        }

        return $foodstuff;
    }

    public function create(Foodstuff $foodstuff): Foodstuff
    {
        $this->em->persist($foodstuff);
        $this->em->flush();

        return $foodstuff;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(Foodstuff $foodstuff): void
    {
        $this->em->remove($foodstuff);
        $this->em->flush();
    }
}
