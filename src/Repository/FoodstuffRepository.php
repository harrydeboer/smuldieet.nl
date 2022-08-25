<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Foodstuff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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
            ->orderBy('f.name', 'ASC');

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
     * @throws BadRequestException
     */
    public function create(Foodstuff $foodstuff): void
    {
        $this->checkFirstChar($foodstuff->getName());
        $this->checkWeightsAndEnergy($foodstuff);
        $this->em->persist($foodstuff);
        $this->em->flush();
    }

    /**
     * @throws BadRequestException
     */
    public function update(Foodstuff $foodstuff): void
    {
        $this->checkFirstChar($foodstuff->getName());
        $this->checkWeightsAndEnergy($foodstuff);
        $this->em->flush();
    }

    public function delete(Foodstuff $foodstuff): void
    {
        foreach ($foodstuff->getDays() as $day) {
            $weights = $day->getFoodstuffWeights();
            unset($weights[$foodstuff->getId()]);
            $day->setFoodstuffWeights($weights);
        }
        foreach ($foodstuff->getRecipes() as $recipe) {
            $weights = $recipe->getFoodstuffWeights();
            unset($weights[$recipe->getId()]);
            $recipe->setFoodstuffWeights($weights);
        }
        $this->em->remove($foodstuff);
        $this->em->flush();
    }

    /**
     * @throws BadRequestException
     */
    private function checkWeightsAndEnergy(Foodstuff $foodstuff): void
    {
        $sum = 0;
        foreach (Foodstuff::getADH() as $key => $property) {
            if ($key === 'energyKcal' || $key === 'saturatedFat' || $key === 'monounsaturatedFat'
                || $key === 'polyunsaturatedFat'|| $key === 'sucre') {
                continue;
            }
            $factor = 1;
            if ($property[2] === 'mg') {
                $factor = 0.001;
            } elseif ($property[2] === 'μg') {
                $factor = 0.000001;
            }
            $sum = $sum + $foodstuff->{'get' . ucfirst($key)}() * $factor;
        }

        if ($sum < 85 || $sum > 115) {
            throw new BadRequestException('De gewichten van het voedingsmiddel moeten samen gelijk ' .
                'aan 100g zijn.');
        }

        if ($foodstuff->getSucre() > $foodstuff->getCarbohydrates()) {
            throw new BadRequestException('Suiker mag niet zwaarder zijn dan koolhydraten.');
        }

        $energy = $foodstuff->getCarbohydrates() * 4 + $foodstuff->getProtein() * 4 +
            $foodstuff->getFat() * 9 + $foodstuff->getAlcohol() * 7 + $foodstuff->getDietaryFiber() * 2;
        $allowed = $energy * 0.12;
        if (abs($foodstuff->getEnergyKcal() - $energy) > $allowed) {
            throw new BadRequestException('De totale energy klopt niet met de energieën uit ' .
                ' koolhydraten, eiwit, vet, alcohol  en vezels.');
        }
    }

    private function checkFirstChar(string $name)
    {
        if (!preg_match('/[A-Za-zÀ-ÿ]/', substr($name, 0, 1))) {
            throw new BadRequestException('De naam moet beginnen met een letter.');
        }
    }
}
