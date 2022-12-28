<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Foodstuff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Foodstuff::class);
    }

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

    public function search(string $name, int $userId): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.name like :name')
            ->setParameter('name', '%' . $name . '%')
            ->andWhere('f.user = :userId or f.user IS NULL')
            ->setParameter('userId', $userId)
            ->setMaxResults(10)
            ->addSelect("(CASE WHEN f.name like '" . $name . " %' THEN 0 WHEN f.name like '" . $name . "%' " .
                "THEN 1 WHEN f.name like '%" . $name . "%' THEN 2 ELSE 3 END) AS HIDDEN ORD ")
            ->orderBy('ORD', 'ASC');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function get(int $id): Foodstuff
    {
        $foodstuff = $this->findOneBy(['id' => $id]);

        if (is_null($foodstuff)) {
            throw new NotFoundHttpException('Dit voedingsmiddel bestaat niet.');
        }

        return $foodstuff;
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
        if ($isLiquidOld && !$foodstuff->getIsLiquid()) {
            foreach ($foodstuff->getDays() as $day) {
                $this->transformLiquidUnitsToSolid($day, $foodstuff->getDensity());
            }
            foreach ($foodstuff->getRecipes() as $recipe) {
                $this->transformLiquidUnitsToSolid($recipe, $foodstuff->getDensity());
            }
        }
        $this->em->flush();
    }

    /**
     * When a foodstuff is deleted the weights and choices properties of day and recipe are updated.
     */
    public function delete(Foodstuff $foodstuff): void
    {
        foreach ($foodstuff->getDays() as $day) {
            $weights = $day->getFoodstuffWeights();
            unset($weights[$foodstuff->getId()]);
            $day->setFoodstuffWeights($weights);
            $units = $day->getFoodstuffUnits();
            unset($units[$foodstuff->getId()]);
            $day->setFoodstuffChoices($units);
        }
        foreach ($foodstuff->getRecipes() as $recipe) {
            $weights = $recipe->getFoodstuffWeights();
            unset($weights[$recipe->getId()]);
            $recipe->setFoodstuffWeights($weights);
            $units = $recipe->getFoodstuffUnits();
            unset($units[$recipe->getId()]);
            $recipe->setFoodstuffChoices($units);
        }
        $this->em->remove($foodstuff);
        $this->em->flush();
    }

    /**
     * @throws Exception
     */
    private function checkWeightsAndEnergy(Foodstuff $foodstuff): void
    {
        $sum = 0;
        foreach (Foodstuff::getNutrients() as $key => $nutrient) {
            if ($key === 'energyKcal' || $key === 'saturatedFat' || $key === 'monounsaturatedFat'
                || $key === 'polyunsaturatedFat'|| $key === 'sucre') {
                continue;
            }
            $factor = 1;
            if ($nutrient->getUnit() === 'mg') {
                $factor = 0.001;
            } elseif ($nutrient->getUnit() === 'μg') {
                $factor = 0.000001;
            }
            $sum = $sum + $foodstuff->{'get' . ucfirst($key)}() * $factor;
        }

        if ($sum < 85 || $sum > 115) {
            throw new Exception('De gewichten van het voedingsmiddel moeten samen gelijk ' .
                'aan 100g zijn.');
        }

        if ($foodstuff->getSucre() > $foodstuff->getCarbohydrates()) {
            throw new Exception('Suiker mag niet zwaarder zijn dan koolhydraten.');
        }

        $energy = $foodstuff->getCarbohydrates() * 4 + $foodstuff->getProtein() * 4 +
            $foodstuff->getFat() * 9 + $foodstuff->getAlcohol() * 7 + $foodstuff->getDietaryFiber() * 2;
        $allowed = $energy * 0.12;
        if (abs($foodstuff->getEnergyKcal() - $energy) > $allowed) {
            throw new Exception('De totale energy klopt niet met de energieën uit ' .
                ' koolhydraten, eiwit, vet, alcohol  en vezels.');
        }
    }

    /**
     * @throws Exception
     */
    private function checkFirstChar(string $name)
    {
        if (!preg_match('/[A-Za-zÀ-ÿ]/', substr($name, 0, 1))) {
            throw new Exception('De naam moet beginnen met een letter.');
        }
    }

    /**
     * @throws Exception
     */
    private function checkPieceAndPiecesName(Foodstuff $foodstuff)
    {
        if (!is_null($foodstuff->getPieceName()) && is_null($foodstuff->getPiecesName())) {
            throw new Exception('Stuks naam moet zowel in enkelvoud als meervoud.');
        } elseif (is_null($foodstuff->getPieceName()) && !is_null($foodstuff->getPiecesName())) {
            throw new Exception('Stuks naam moet zowel in enkelvoud als meervoud.');
        } elseif (
            !is_null($foodstuff->getPieceName())
            && !is_null($foodstuff->getPiecesName())
            && is_null($foodstuff->getPieceWeight())
        ) {
            throw new Exception('Stuks naam mag alleen met stuks gewicht.');
        }
    }

    private function transformLiquidUnitsToSolid(FoodstuffWeightsInterface $entity, ?float $density)
    {
        $units = $entity->getFoodstuffUnits();
        $weights = $entity->getFoodstuffWeights();
        if (is_null($density)) {
            $density = 1;
        }

        foreach ($units as $key => $unit) {
            if ($unit === 'l') {
                $units[$key] = 'kg';
                $weights[$key] = $density * $weights[$key];
            }
            if ($unit === 'dl') {
                $units[$key] = 'g';
                $weights[$key] = $density * $weights[$key] * 100;
            }
            if ($unit === 'cl') {
                $units[$key] = 'g';
                $weights[$key] = $density * $weights[$key] * 10;
            }
            if ($unit === 'ml') {
                $units[$key] = 'g';
                $weights[$key] = $density * $weights[$key];
            }
        }

        $entity->setFoodstuffWeights($weights);
    }
}
