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
        $offsetMinus = [];
        foreach ($nutrientProperties as $key => $name) {
            $nutrient = new Nutrient();
            $nutrient->setName($name);
            $offsetPlus = 0;

            /**
             * The updated and new nutrients are to be seperated. The diff of properties has to be compared with
             * the diff of the nutrients in the database with the right offset. When a name is in the properties
             * but not in the database the offset is increased. When a name is in the database but not in the
             * properties the offset is lowered.
             */
            foreach ($diffProperties as $keyDiffProperties => $nameDiffProperties) {
                foreach ($diffDb as $keyDiffDb => $nameDiffDb) {
                    if (in_array($nameDiffDb, $offsetMinus)) {
                        continue;
                    }
                    if ($keyDiffDb < $key - $offsetPlus + count($offsetMinus)
                        && $keyDiffDb < $keyDiffProperties - $offsetPlus + count($offsetMinus)
                        && !isset($diffProperties[$keyDiffDb - count($offsetMinus) + $offsetPlus])) {
                        $offsetMinus[$keyDiffDb] = $nameDiffDb;
                    }
                }
                if ($keyDiffProperties < $key
                    && !isset($diffDb[$keyDiffProperties - $offsetPlus + count($offsetMinus)])) {
                    $offsetPlus++;
                }
            }
            $offset = $offsetPlus - count($offsetMinus);

            /**
             * If the name is in the properties diff but not set in the database diff
             * the nutrient is created with default values.
             * If the name is in the properties diff and in the database diff the nutrient is updated.
             * If the name is not in the properties diff the nutrient has not changed and is matched with the database.
             */
            if (in_array($name, $diffProperties) && !isset($diffDb[$key - $offset])) {
                $nutrient->setDisplayName($this->generateUniqueName($nutrientDisplayNamesDb, $name));
                $nutrient->setUnit('g');
                $nutrient->setDecimalPlaces(0);

                if ($nutrient->getName() === 'energy') {
                    $nutrient->setUnit('kcal');
                }
            } elseif (in_array($name, $diffProperties) && isset($diffDb[$key - $offset])) {
                $nutrient->setDisplayName($nutrientsDb[$diffDb[$key - $offset]]->getDisplayName());
                $nutrient->setMinRDA($nutrientsDb[$diffDb[$key - $offset]]->getMinRDA());
                $nutrient->setMaxRDA($nutrientsDb[$diffDb[$key - $offset]]->getMaxRDA());
                $nutrient->setUnit($nutrientsDb[$diffDb[$key - $offset]]->getUnit());
                $nutrient->setDecimalPlaces($nutrientsDb[$diffDb[$key - $offset]]->getDecimalPlaces());
            } else {
                $nutrient->setDisplayName($nutrientsDb[$name]->getDisplayName());
                $nutrient->setMinRDA($nutrientsDb[$name]->getMinRDA());
                $nutrient->setMaxRDA($nutrientsDb[$name]->getMaxRDA());
                $nutrient->setUnit($nutrientsDb[$name]->getUnit());
                $nutrient->setDecimalPlaces($nutrientsDb[$name]->getDecimalPlaces());
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
