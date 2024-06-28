<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Foodstuff;
use App\Entity\Nutrient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

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
        private readonly DayFoodstuffWeightRepositoryInterface $dayFoodstuffWeightRepository,
        private readonly RecipeFoodstuffWeightRepositoryInterface $recipeFoodstuffWeightRepository,
        private readonly NutrientRepositoryInterface $nutrientRepository,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Foodstuff::class);
    }

    /**
     * @return Foodstuff[]
     */
    public function findAllStartingWith(string $char, ?int $userId): array
    {
        $qb = $this->createQueryBuilder('f')
            ->where('Lower(f.name) like :name')
            ->setParameter('name', strtolower($char) . '%');

        if (!is_null($userId)) {
            $qb->andWhere('f.user = ' . $userId . ' or f.user IS NULL');
        }
        $qb->orderBy('f.name', 'ASC');

        $query = $qb->getQuery();

        return $query->execute();
    }

    /**
     * @return Foodstuff[]
     */
    public function findAllFromUser(?int $userId): array
    {
        $qb = $this->createQueryBuilder('f');
        if (is_null($userId)) {
            $qb->where('f.user IS NULL');
        } else {
            $qb->where('f.user = :userId or f.user IS NULL')
                ->setParameter('userId', $userId);
        }
        $qb->orderBy('f.name', 'ASC');

        $query = $qb->getQuery();

        return $query->execute();
    }

    /**
     * @return Foodstuff[]
     */
    public function search(string $name, int $userId): array
    {
        $nameOrderBy = str_replace("'", "''", $name);
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.name like :name')
            ->setParameter('name', '%' . addslashes($name) . '%')
            ->andWhere('f.user = :userId or f.user IS NULL')
            ->setParameter('userId', $userId)
            ->setMaxResults(10)
            ->addSelect("(CASE WHEN f.name like '" . $nameOrderBy . " %' THEN 0 WHEN f.name like '" . $nameOrderBy .
                "%' THEN 1 WHEN f.name like '%" . $nameOrderBy . "%' THEN 2 ELSE 3 END) AS HIDDEN ORD ")
            ->orderBy('ORD', 'ASC');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function get(int $id): Foodstuff
    {
        $foodstuff = $this->find($id);

        if (is_null($foodstuff)) {
            throw new NotFoundHttpException('Dit voedingsmiddel bestaat niet.');
        }

        return $foodstuff;
    }

    public function getDefaultAndFromUser(int $id, ?int $userId): Foodstuff
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.id = ' . $id);
        if (is_null($userId)) {
            $qb->andWhere('f.user IS NULL');
        } else {
            $qb->andWhere('f.user = :userId or f.user IS NULL')
                ->setParameter('userId', $userId);
        }
        $foodstuffs = $qb->getQuery()->execute();

        if (empty($foodstuffs)) {
            throw new NotFoundHttpException('Dit voedingsmiddel bestaat niet.');
        }

        return $foodstuffs[0];
    }

    public function getFromUser(int $id, int $userId): Foodstuff
    {
        $foodstuff = $this->findOneBy(['id' => $id, 'user' => $userId]);

        if (is_null($foodstuff)) {
            throw new NotFoundHttpException('Dit voedingsmiddel bestaat niet.');
        }

        return $foodstuff;
    }

    public function getByName(string $name): Foodstuff
    {
        $foodstuff = $this->findOneBy(['name' => $name]);

        if (is_null($foodstuff)) {
            throw new NotFoundHttpException('Dit voedingsmiddel bestaat niet of hoort niet bij jou.');
        }

        return $foodstuff;
    }

    /**
     * @throws Exception
     */
    public function create(Foodstuff $foodstuff): Foodstuff
    {
        $this->checkFirstChar($foodstuff->getName());
        $this->checkWeightsAndEnergy($foodstuff);
        $this->checkPieceAndPiecesName($foodstuff);
        $this->em->persist($foodstuff);
        $this->em->flush();

        return $foodstuff;
    }

    /**
     * @throws Exception
     */
    public function update(Foodstuff $foodstuff, bool $isLiquidOld): void
    {
        $this->checkFirstChar($foodstuff->getName());
        $this->checkWeightsAndEnergy($foodstuff);
        $this->checkPieceAndPiecesName($foodstuff);
        if ($isLiquidOld && !$foodstuff->isLiquid()) {
            $this->transformLiquidUnitsToSolid($foodstuff->getDayFoodstuffWeights(), $foodstuff->getDensity());
            $this->transformLiquidUnitsToSolid($foodstuff->getRecipeFoodstuffWeights(), $foodstuff->getDensity());
        }
        $this->em->flush();
    }

    /**
     * When a foodstuff is deleted the weights and choices properties of day and recipe are updated.
     */
    public function delete(Foodstuff $foodstuff): void
    {
        foreach ($foodstuff->getDayFoodstuffWeights() as $weight) {
            $this->dayFoodstuffWeightRepository->delete($weight);
        }
        foreach ($foodstuff->getRecipeFoodstuffWeights() as $weight) {
            $this->recipeFoodstuffWeightRepository->delete($weight);
        }
        $this->em->remove($foodstuff);
        $this->em->flush();
    }

    public function transformUnit(string $oldUnit, Nutrient $nutrient, array $factors): void
    {
        foreach ($this->findAll() as $foodstuff) {
            $value = $foodstuff->{'get' . ucfirst($nutrient->getName())}();
            $foodstuff->{'set' . ucfirst($nutrient->getName())}(
                $value * $factors[$oldUnit] / $factors[$nutrient->getUnit()]
            );
        }

        $this->em->flush();
    }

    /**
     * @throws Exception
     */
    private function checkWeightsAndEnergy(Foodstuff $foodstuff): void
    {
        $sum = 0;
        $energy = 0;
        foreach ($this->nutrientRepository->findAll() as $nutrient) {
            $key = $nutrient->getName();
            if ($key === 'energy' || $key === 'saturatedFat' || $key === 'monounsaturatedFat'
                || $key === 'polyunsaturatedFat'|| $key === 'sucre') {
                continue;
            }
            $units = array_merge(Nutrient::SOLID_UNITS, Nutrient::LIQUID_UNITS, Nutrient::VITAMIN_MINERAL_UNITS);

            $sum += $foodstuff->{'get' . ucfirst($key)}() * $units[$nutrient->getUnit()];

            switch ($key) {
                case 'carbohydrates':
                    $energy += $foodstuff->getCarbohydrates() * $units[$nutrient->getUnit()] * 4;
                    break;
                case 'protein':
                    $energy += $foodstuff->getProtein() * $units[$nutrient->getUnit()] * 4;
                    break;
                case 'fat':
                    $energy += $foodstuff->getFat() * $units[$nutrient->getUnit()] * 9;
                    break;
                case 'alcohol':
                    $energy += $foodstuff->getAlcohol() * $units[$nutrient->getUnit()] * 7;
                    break;
                case 'dietaryFiber':
                    $energy += $foodstuff->getDietaryFiber() * $units[$nutrient->getUnit()] * 2;
                    break;
            }
        }
        if ($sum < 85 || $sum > 115) {
            throw new Exception('De gewichten van het voedingsmiddel moeten samen gelijk aan 100g zijn.');
        }

        if ($foodstuff->getSucre() > $foodstuff->getCarbohydrates()) {
            throw new Exception('Suiker mag niet zwaarder zijn dan koolhydraten.');
        }

        $allowed = $energy * 0.12;
        if (abs($foodstuff->getEnergy() - $energy) > $allowed) {
            throw new Exception('De totale energy klopt niet met de energieën uit ' .
                ' koolhydraten, eiwit, vet, alcohol  en vezels.');
        }
    }

    /**
     * @throws Exception
     */
    private function checkFirstChar(string $name): void
    {
        if (!preg_match('/[A-Za-zÀ-ÿ]/', substr($name, 0, 1))) {
            throw new Exception('De naam moet beginnen met een letter.');
        }
    }

    /**
     * @throws Exception
     */
    private function checkPieceAndPiecesName(Foodstuff $foodstuff): void
    {
        if (!is_null($foodstuff->getPieceName()) && is_null($foodstuff->getPiecesName())) {
            throw new Exception('Stuks naam moet zowel in enkelvoud als meervoud.');
        } elseif (is_null($foodstuff->getPieceName()) && !is_null($foodstuff->getPiecesName())) {
            throw new Exception('Stuks naam moet zowel in enkelvoud als meervoud.');
        } elseif (is_null($foodstuff->getPieceWeight())
            && !is_null($foodstuff->getPieceName())
            && !in_array($foodstuff->getPieceName(), array_keys(Nutrient::SOLID_UNITS))
            && !in_array($foodstuff->getPieceName(), array_keys(Nutrient::LIQUID_UNITS))) {
            throw new Exception('Stuks naam moet een geldige eenheid zijn als stuks gewicht ontbreekt.');
        }
    }

    private function transformLiquidUnitsToSolid(Collection $weights, ?float $density): void
    {
        if (is_null($density)) {
            $density = 1;
        }

        foreach ($weights as $weight) {
            if ($weight->getUnit() === 'l') {
                $weight->setUnit('kg');
                $weight->setValue($density * $weight->getValue());
            } else {
                foreach (Nutrient::LIQUID_UNITS as $unit => $factor) {
                    if ($unit === $weight->getUnit()) {
                        $weight->setValue($density * $weight->getValue() * $factor);
                        $weight->setUnit('g');
                    }
                }
            }
        }
    }
}
