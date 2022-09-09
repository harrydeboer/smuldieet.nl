<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FoodstuffsInterface;
use App\Entity\Foodstuff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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
        $this->em->persist($foodstuff);
        $this->em->flush();

        return $foodstuff;
    }

    /**
     * When a foodstuff gets a piece weight or loses a piece weight the day and recipe weight and choices are updated.
     * @throws Exception
     */
    public function update(Foodstuff $foodstuff, ?float $pieceWeightOld): void
    {
        if (is_null($pieceWeightOld) && !is_null($foodstuff->getPieceWeight())) {
            foreach ($foodstuff->getDays() as $day) {
                $this->replaceWeightWithChoice($day, $foodstuff);
            }
            foreach ($foodstuff->getRecipes() as $recipe) {
                $this->replaceWeightWithChoice($recipe, $foodstuff);
            }
        } elseif (!is_null($pieceWeightOld) && is_null($foodstuff->getPieceWeight())) {
            if (!is_null($foodstuff->getPieceName())) {
                throw new Exception('No piece name allowed when there is no piece weight.');
            }
            foreach ($foodstuff->getDays() as $day) {
                $this->replaceChoiceWithWeight($day, $foodstuff, $pieceWeightOld);
            }
            foreach ($foodstuff->getRecipes() as $recipe) {
                $this->replaceChoiceWithWeight($recipe, $foodstuff, $pieceWeightOld);
            }
        }
        $this->checkFirstChar($foodstuff->getName());
        $this->checkWeightsAndEnergy($foodstuff);
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
            $choices = $day->getFoodstuffChoices();
            unset($choices[$foodstuff->getId()]);
            $day->setFoodstuffChoices($choices);
        }
        foreach ($foodstuff->getRecipes() as $recipe) {
            $weights = $recipe->getFoodstuffWeights();
            unset($weights[$recipe->getId()]);
            $recipe->setFoodstuffWeights($weights);
            $choices = $recipe->getFoodstuffChoices();
            unset($choices[$recipe->getId()]);
            $recipe->setFoodstuffChoices($choices);
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

    private function replaceWeightWithChoice(FoodstuffsInterface $entity, Foodstuff $foodstuff): void
    {
        $weights = $entity->getFoodstuffWeights();
        $entity->setFoodstuffChoices($this->roundToNearest($weights[$foodstuff->getId()] /
            $foodstuff->getPieceWeight(), $entity->getFoodstuffChoices(), $foodstuff->getId()));
        unset($weights[$foodstuff->getId()]);
        $entity->setFoodstuffWeights($weights);
    }

    private function replaceChoiceWithWeight(
        FoodstuffsInterface $entity,
        Foodstuff           $foodstuff,
        float               $pieceWeightOld,
    ): void
    {
        $choices = $entity->getFoodstuffChoices();
        $weights = $entity->getFoodstuffWeights();
        $weights[$foodstuff->getId()] = $pieceWeightOld * $choices[$foodstuff->getId()];
        $entity->setFoodstuffWeights($weights);
        unset($choices[$foodstuff->getId()]);
        $entity->setFoodstuffChoices($choices);
    }

    private function roundToNearest(float $number, ArrayCollection $numberOfPieces, int $id): ArrayCollection
    {
        if ($number < 0.125) {
            $numberOfPieces[$id] = 0.25;
        } elseif ($number < 1) {
            $numberOfPieces[$id] = round($number * 4) / 4;
        } elseif ($number <= 2) {
            $numberOfPieces[$id] = round($number * 2) / 2;
        } else {
            $numberOfPieces[$id] = round($number);
        }
        if (!in_array($numberOfPieces[$id], Foodstuff::$foodstuffChoicesArray)) {
            throw new InvalidArgumentException('The rounded value must exist in the piece choices.');
        }

        return $numberOfPieces;
    }

    /**
     * @throws BadRequestException;
     */
    public function checkPieces(FoodstuffsInterface $entity): void
    {
        foreach ($entity->getFoodstuffWeights() as $id => $weight) {
            $foodstuff = $entity->getFoodstuffs()[$id];
            if (!is_null($foodstuff->getPieceWeight())) {
                throw new BadRequestException('The weight foodstuff can not have a piece weight.');
            }
        }
        foreach ($entity->getFoodstuffChoices() as $id => $choice) {
            $foodstuff = $entity->getFoodstuffs()[$id];
            if (!is_null($foodstuff->getPieceWeight()) && $choice > 20) {
                throw new BadRequestException('The number of pieces can not be greater than 20.');
            } elseif (is_null($foodstuff->getPieceWeight())) {
                throw new BadRequestException('The choice foodstuff must have a piece weight.');
            }
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
}
