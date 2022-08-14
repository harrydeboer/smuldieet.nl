<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Day;
use App\Pagination\Paginator;
use App\Service\DateCheckService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Day|null find($id, $lockMode = null, $lockVersion = null)
 * @method Day|null findOneBy(array $criteria, array $orderBy = null)
 * @method Day[]    findAll()
 * @method Day[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DayRepository extends ServiceEntityRepository implements DayRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Day::class);
    }

    public function getFromUser(int $id, int $userId): Day
    {
        $day = $this->findOneBy(['id' => $id, 'user' => $userId]);

        if (is_null($day)) {
            throw new NotFoundHttpException('Deze dag bestaat niet of hoort niet bij jou.');
        }

        return $day;
    }

    public function create(Day $day): ?string
    {
        if (is_null($error = $this->checkCount($day))) {
            $this->em->persist($day);
            $this->em->flush();
            $this->makeWeights($day);
            $this->em->persist($day);
            $this->em->flush();

            return null;
        }

        return $error;
    }

    public function update(Day $day): ?string
    {
        if (is_null($error = $this->checkCount($day))) {
            $this->makeWeights($day);
            $this->em->flush();

            return null;
        }

        return $error;
    }

    public function delete(Day $day): void
    {
        $this->em->remove($day);
        $this->em->flush();
    }

    public function findBetween(string $start, string $end, int $userId): Collection|array
    {
        $qb = $this->createQueryBuilder('d');
        if (DateCheckService::checkDate($start) && DateCheckService::checkDate($end)) {
            $qb->where('d.timestamp >= :timestampStart')
                ->andWhere('d.timestamp <= :timestampEnd')
                ->andWhere('d.user = :userId')
                ->setParameter('timestampStart', strtotime($start))
                ->setParameter('timestampEnd', strtotime($end))
                ->setParameter('userId', $userId);
        } else {
            throw new InvalidArgumentException('Date not in right format.');
        }

        return $qb->getQuery()->execute();
    }

    public function findFromUserSorted(int $userId, int $page): Paginator|array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.user = :userId')
            ->andWhere('d.timestamp IS NOT NULL')
            ->setParameter('userId', $userId)
            ->orderBy('d.timestamp', 'DESC');

        return (new Paginator($qb))->paginate($page);
    }

    private function checkCount(Day $day): ?string
    {
        if (count($day->getFoodstuffWeights()) === count($day->getFoodstuffs())
            && count($day->getRecipeWeights()) === count($day->getRecipes())) {
            return null;
        }

        return 'Het aantal gewichten is niet gelijk aan het aantal elementen.';
    }

    private function makeWeights(Day $day): void
    {
        $array = [];
        $count = 0;
        foreach ($day->getFoodstuffWeights() as $value) {
            $array[$day->getFoodstuffs()->toArray()[$count]->getId()] = $value;
            $count++;
        }
        $day->setFoodstuffWeights($array);
        $array = [];
        $count = 0;
        foreach ($day->getRecipeWeights() as $value) {
            $array[$day->getRecipes()->toArray()[$count]->getId()] = $value;
            $count++;
        }
        $day->setRecipeWeights($array);
    }
}
