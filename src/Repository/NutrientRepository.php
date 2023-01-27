<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Foodstuff;
use App\Entity\Nutrient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
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

    /**
     * This method syncs the nutrients with the foodstuff class.
     * When there are no changes it returns null.
     * If it syncs it returns true.
     * @throws Exception
     */
    public function sync(): ?bool
    {
        $foodstuff = new Foodstuff();
        $nutrientProperties = $foodstuff->getNutrientNames();

        $nutrientNamesDb = [];
        $nutrientsDb = [];
        foreach ($this->findAll() as $nutrient) {
            $nutrientNamesDb[] = $nutrient->getName();
            $nutrientsDb[$nutrient->getName()] = $nutrient;
        }

        if ($nutrientNamesDb === $nutrientProperties) {
            return null;
        }

        $nutrientsOld = [];
        $diff = array_diff($nutrientProperties, $nutrientNamesDb);
        $diffReversed = array_diff($nutrientNamesDb, $nutrientProperties);
        foreach ($diffReversed as $key => $name) {
            $offset = 0;
            foreach ($diff as $keyDiff => $nameDiff) {
                if ($keyDiff < $key + $offset && !isset($diffReversed[$keyDiff - $offset])) {
                    $offset++;
                }
            }
            foreach ($diffReversed as $keyDiffReversed => $nameDiffReversed) {
                if ($keyDiffReversed < $key && !isset($diff[$keyDiffReversed + $offset])) {
                    $offset--;
                }
            }
            if (!isset($diff[$key + $offset])) {
                array_splice($nutrientProperties, $key + $offset, 0, $name);
            }
        }
        $offset = 0;
        foreach ($nutrientProperties as $key => $name) {
            if (in_array($name, $diff)) {
                if (isset($diffReversed[$key - $offset])) {
                    $nutrientsOld[$name] = $nutrientsDb[$diffReversed[$key - $offset]];
                } else {
                    $nutrientsOld[$name] = null;
                    $offset++;
                }
            } elseif (in_array($name, $diffReversed)) {
                continue;
            } else {
                $nutrientsOld[$name] = $nutrientsDb[$name];
            }
        }

        $connection = $this->em->getConnection();

        $connection->executeQuery('TRUNCATE table nutrient');

        foreach ($nutrientProperties as $property) {
            $nutrient = new Nutrient();
            $nutrient->setName($property);
            if (!is_null($nutrientsOld[$property])) {
                $nutrient->setDisplayName($nutrientsOld[$property]->getDisplayName());
                $nutrient->setMinRDA($nutrientsOld[$property]->getMinRDA());
                $nutrient->setMaxRDA($nutrientsOld[$property]->getMaxRDA());
                $nutrient->setUnit($nutrientsOld[$property]->getUnit());
                $nutrient->setDecimalPlaces($nutrientsOld[$property]->getDecimalPlaces());

                $this->create($nutrient);
            } else {
                $nutrient->setDisplayName($property);
                $nutrient->setUnit('g');
                $nutrient->setDecimalPlaces(0);

                if ($nutrient->getName() === 'energy') {
                    $nutrient->setUnit('kcal');
                }
            }
            $this->create($nutrient);
        }

        return true;
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
