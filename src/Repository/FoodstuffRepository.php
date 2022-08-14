<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Foodstuff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
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

    public function create(Foodstuff $foodstuff): ?string
    {
        if (is_null($error = $this->checkWeightsAndEnergy($foodstuff))) {
            $this->em->persist($foodstuff);
            $this->em->flush();
        }

        return $error;
    }

    public function update(Foodstuff $foodstuff): ?string
    {
        if (is_null($error = $this->checkWeightsAndEnergy($foodstuff))) {
            $this->em->flush();
        }

        return $error;
    }

    public function delete(Foodstuff $foodstuff): void
    {
        $this->em->remove($foodstuff);
        $this->em->flush();
    }

    private function checkWeightsAndEnergy(Foodstuff $foodstuff): ?string
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
            return 'De gewichten van het voedingsmiddel moeten samen gelijk aan 100g zijn.';
        }

        if ($foodstuff->getSucre() > $foodstuff->getCarbohydrates()) {
            return 'Suiker mag niet zwaarder zijn dan koolhydraten.';
        }

        $energy = $foodstuff->getCarbohydrates() * 4 + $foodstuff->getProtein() * 4 +
            $foodstuff->getFat() * 9 + $foodstuff->getAlcohol() * 7 + $foodstuff->getDietaryFiber() * 2;
        $allowed = $energy * 0.12;
        if (abs($foodstuff->getEnergyKcal() - $energy) > $allowed) {
            return 'De totale energy klopt niet met de energieën uit koolhydraten, eiwit, vet, alcohol  en vezels.';
        }

        return null;
    }
}
