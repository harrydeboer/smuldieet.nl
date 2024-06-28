<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Nutrient;
use App\Entity\NutrientsInterface;
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
     * This method syncs the nutrients with the foodstuff entity properties.
     * When there are no changes it returns null.
     * If it syncs it returns true.
     * @throws Exception
     */
    public function sync(): ?bool
    {
        $nutrientProperties = NutrientsInterface::NAMES;

        $nutrientsDb = [];
        $nutrientNamesDb = [];
        $nutrientDisplayNamesDb = [];
        foreach ($this->findAll() as $nutrient) {
            $nutrientsDb[$nutrient->getName()] = $nutrient;
            $nutrientNamesDb[] = $nutrient->getName();
            $nutrientDisplayNamesDb[] = $nutrient->getDisplayName();
        }

        if ($nutrientNamesDb === $nutrientProperties) {
            return null;
        }

        $this->em->getConnection()->executeQuery('TRUNCATE table nutrient');

        $diffProperties = array_diff($nutrientProperties, $nutrientNamesDb);
        $diffDb = array_diff($nutrientNamesDb, $nutrientProperties);
        $deletes = [];
        $updates = [];
        $creates = [];
        foreach ($nutrientProperties as $key => $name) {
            $nutrient = new Nutrient();
            $nutrient->setName($name);

            /**
             * Each time a new name is looped there is a loop over the database difference array up till the
             * name of the current loop. When this difference exists in the properties difference array it is
             * an update. When there is no match it is a deletion.
             * After this loop it is checked if the current name is a creation by checking if it exists in
             * the database difference array.
             */
            foreach ($diffDb as $keyDiffDb => $nameDiffDb) {
                if (in_array($nameDiffDb, $deletes) || in_array($nameDiffDb, $updates)) {
                    continue;
                }
                $keyOffset = $keyDiffDb + count($creates) - count($deletes);
                if ($keyOffset <= $key && isset($diffProperties[$keyOffset])) {
                    $updates[$keyDiffDb] = $nameDiffDb;
                } elseif ($keyOffset <= $key) {
                    $deletes[$keyDiffDb] = $nameDiffDb;
                }
            }
            if (in_array($name, $diffProperties) && !isset($diffDb[$key - count($creates) + count($deletes)])) {
                $creates[$key] = $name;
            }

            /**
             * If the name is in the creates array the nutrient is created with default values.
             * In the other case the nutrient properties are taken from the right nutrient from the database.
             * After that the nutrient is created.
             */
            if (in_array($name, $creates)) {
                $nutrient->setDisplayName($this->generateUniqueName($nutrientDisplayNamesDb, $name));
                $nutrient->setUnit('g');
                $nutrient->setDecimalPlaces(0);

                if ($nutrient->getName() === 'energy') {
                    $nutrient->setUnit('kcal');
                }
            } else {
                $oldNutrient = $nutrientsDb[$nutrientNamesDb[$key - count($creates) + count($deletes)]];
                $nutrient->setDisplayName($oldNutrient->getDisplayName());
                $nutrient->setMinRDA($oldNutrient->getMinRDA());
                $nutrient->setMaxRDA($oldNutrient->getMaxRDA());
                $nutrient->setUnit($oldNutrient->getUnit());
                $nutrient->setDecimalPlaces($oldNutrient->getDecimalPlaces());
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

    private function generateUniqueName(array $displayNames, string $name): string
    {
        if (in_array($name, $displayNames)) {
            return $this->generateUniqueName($displayNames, uniqid($name));
        }

        return $name;
    }
}
