<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cookbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Cookbook|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cookbook|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cookbook[]    findAll()
 * @method Cookbook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CookbookRepository extends ServiceEntityRepository implements CookbookRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Cookbook::class);
    }

    public function getFromUser(int $id, int $userId): Cookbook
    {
        $cookbook = $this->findOneBy(['id' => $id, 'user' => $userId]);

        if (is_null($cookbook)) {
            throw new NotFoundHttpException('Dit kookboek bestaat niet of hoort niet bij jou.');
        }

        return $cookbook;
    }

    public function create(Cookbook $cookbook): Cookbook
    {
        $this->em->persist($cookbook);
        $this->em->flush();

        return $cookbook;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(Cookbook $cookbook): void
    {
        $this->em->remove($cookbook);
        $this->em->flush();
    }
}
