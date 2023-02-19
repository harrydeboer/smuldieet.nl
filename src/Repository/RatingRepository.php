<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rating;
use App\Pagination\Paginator;
use App\Service\ProfanityCheckService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

/**
 * @method Rating|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rating|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rating[]    findAll()
 * @method Rating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatingRepository extends ServiceEntityRepository implements RatingRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProfanityCheckService $profanityCheckService,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Rating::class);
    }

    /**
     * @return Rating[]
     */
    public function findAllPendingReviews(): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.content IS NOT NULL');
        $qb->andWhere('r.pending = 1');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findReviewsFromRecipe(int $recipeId, int $page): Paginator
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.content IS NOT NULL');
        $qb->andWhere('r.recipe = ' . $recipeId);
        $qb->andWhere('r.pending = 0');

        return (new Paginator($qb, 3))->paginate($page);
    }

    public function findReviewsFromUser(int $userId): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.content IS NOT NULL');
        $qb->andWhere('r.user = ' . $userId);

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findAllFromUser(int $userId): array
    {
        return $this->findBy(['user' => $userId]);
    }

    public function get(int $id): Rating
    {
        $rating = $this->find($id);

        if (is_null($rating)) {
            throw new NotFoundHttpException('Deze waardering bestaat niet.');
        }

        return $rating;
    }

    public function getFromUser(int $id, int $userId): Rating
    {
        $day = $this->findOneBy(['id' => $id, 'user' => $userId]);

        if (is_null($day)) {
            throw new NotFoundHttpException('Deze waardering bestaat niet of hoort niet bij jou.');
        }

        return $day;
    }

    public function getNotPending(int $id): Rating
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.content IS NOT NULL');
        $qb->andWhere('r.id = ' . $id);
        $qb->andWhere('r.pending = 0');

        $query = $qb->getQuery();

        $reviews = $query->execute();

        if ($reviews === []) {
            throw new NotFoundHttpException('Deze recensie bestaat niet of hoort niet bij jou.');
        }

        return $reviews[0];
    }

    /**
     * @throws Exception
     */
    public function create(Rating $rating): Rating
    {
        $this->profanityCheckService->check($rating->getContent());
        $recipe = $rating->getRecipe();
        $recipeRating = $recipe->getRating();
        $votes = $recipe->getVotes();
        $recipe->setRating(($recipeRating * $votes + $rating->getRating()) / ($votes + 1));
        $recipe->setVotes($votes + 1);

        $this->em->persist($rating);
        $this->em->flush();

        return $rating;
    }

    /**
     * @throws Exception
     */
    public function update(float $oldRating, Rating $rating): void
    {
        $this->profanityCheckService->check($rating->getContent());
        $recipe = $rating->getRecipe();
        $recipeRating = $recipe->getRating();
        $votes = $recipe->getVotes();
        $recipe->setRating(($recipeRating * $votes + $rating->getRating() - $oldRating) / $votes);
        $this->em->flush();
    }

    public function delete(Rating $rating): void
    {
        $recipe = $rating->getRecipe();
        $votes = $recipe->getVotes();
        $recipe->setVotes($votes - 1);
        if ($votes === 1) {
            $recipe->setRating(null);
        } else {
            $recipe->setRating(($recipe->getRating() * $votes - $rating->getRating()) / ($votes - 1));
        }

        $this->em->remove($rating);
        $this->em->flush();
    }
}
