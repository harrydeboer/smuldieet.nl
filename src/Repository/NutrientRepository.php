<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Foodstuff;
use App\Entity\Nutrient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Nutrient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nutrient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nutrient[]    findAll()
 * @method Nutrient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NutrientRepository extends ServiceEntityRepository implements NutrientRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Nutrient::class);
    }

    public function get(int $id): Nutrient
    {
        $nutrient = $this->find($id);

        if (is_null($nutrient)) {
            throw new NotFoundHttpException('Deze voedingsstof bestaat niet.');
        }

        return $nutrient;
    }

    public function sync(): bool
    {
        $foodstuff = new Foodstuff();
        $nutrientProperties = $foodstuff->getNutrientNames();

        $nutrientNames = [];
        foreach ($this->findAll() as $nutrient) {
            $nutrientNames[] = $nutrient->getName();
        }

        if ($nutrientNames === $nutrientProperties) {
            return true;
        }

        foreach ($nutrientProperties as $property) {
            $nutrient = new Nutrient();
            $nutrient->setName($property);
            $nutrient->setDisplayName($property);
            $nutrient->setUnit('g');
            $nutrient->setDecimalPlaces(0);

            if ($nutrient->getName() === 'energy') {
                $nutrient->setUnit('kcal');
            }

            $this->create($nutrient);
        }

        return false;
    }

    public function create(Nutrient $nutrient): Nutrient
    {
        $this->em->persist($nutrient);
        $this->em->flush();

        return $nutrient;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(Nutrient $nutrient): void
    {
        $this->em->remove($nutrient);
        $this->em->flush();
    }
}
