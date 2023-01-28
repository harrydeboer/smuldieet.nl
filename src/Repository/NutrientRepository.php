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
        $news = [];
        foreach ($nutrientProperties as $key => $name) {
            $nutrient = new Nutrient();
            $nutrient->setName($name);

            /**
             * The updated and new nutrients are to be seperated. The diff of properties has to be compared with
             * the diff of the nutrients in the database with the right offset. When a name is in the properties
             * but not in the database the offset is increased. When a name is in the database but not in the
             * properties the offset is lowered. The deletes are also kept as to be able to link the new nutrient to
             * the nutrient from the database.
             */
            foreach ($diffProperties as $keyDiffProperties => $nameDiffProperties) {
                if (in_array($nameDiffProperties, $news)) {
                    continue;
                }
                foreach ($diffDb as $keyDiffDb => $nameDiffDb) {
                    if (in_array($nameDiffDb, $deletes) || in_array($nameDiffDb, $updates)) {
                        continue;
                    }
                    if ($keyDiffDb <= $key - count($news) + count($deletes)
                        && $keyDiffDb <= $keyDiffProperties - count($news) + count($deletes)
                        && !isset($diffProperties[$keyDiffDb + count($news) - count($deletes)])) {
                        $deletes[$keyDiffDb] = $nameDiffDb;
                    } elseif ($keyDiffDb <= $key - count($news) + count($deletes)
                        && $keyDiffDb <= $keyDiffProperties - count($news) + count($deletes)
                        && isset($diffProperties[$keyDiffDb - count($deletes) + count($news)])) {
                        $updates[$keyDiffDb] = $nameDiffDb;
                    }
                }
                if ($keyDiffProperties <= $key
                    && !isset($diffDb[$keyDiffProperties - count($news) + count($deletes)])) {
                    $news[$keyDiffProperties] = $nameDiffProperties;
                }
            }

            /**
             * If the name is in the news array the nutrient is created with default values.
             * In the other case the nutrient properties are taken from the right nutrient from the database.
             * After that the nutrient is created.
             */
            if (in_array($name, $news)) {
                $nutrient->setDisplayName($this->generateUniqueName($nutrientDisplayNamesDb, $name));
                $nutrient->setUnit('g');
                $nutrient->setDecimalPlaces(0);

                if ($nutrient->getName() === 'energy') {
                    $nutrient->setUnit('kcal');
                }
            } else {
                $oldNutrient = $nutrientsDb[$nutrientNamesDb[$key - count($news) + count($deletes)]];
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
