<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Pagination\Paginator;
use App\Service\ProfanityCheckService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository implements CommentRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProfanityCheckService $profanityCheckService,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Comment::class);
    }

    public function get(int $id): Comment
    {
        $comment = $this->find($id);

        if (is_null($comment)) {
            throw new NotFoundHttpException('Dit commentaar bestaat niet.');
        }

        return $comment;
    }

    /**
     * @return Comment[]
     */
    public function findAllPendingComments(): array
    {
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere('c.pending = 1');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findCommentsFromRecipe(int $recipeId, int $page): Paginator
    {
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere('c.recipe = ' . $recipeId);
        $qb->andWhere('c.pending = 0');

        return (new Paginator($qb, 5))->paginate($page);
    }

    public function findCommentsFromPage(int $pageId, int $page): Paginator
    {
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere('c.page = ' . $pageId);
        $qb->andWhere('c.pending = 0');

        return (new Paginator($qb, 5))->paginate($page);
    }

    /**
     * When the comment is created the times reacted of its recipe is upped by 1.
     * @throws Exception
     */
    public function create(Comment $comment): Comment
    {
        $this->profanityCheckService->check($comment->getContent());
        if (!is_null($comment->getPage()) && !is_null($comment->getRecipe())) {
            throw new InvalidArgumentException('A comment cannot have both a page and a recipe.');
        }
        $this->profanityCheckService->check($comment->getContent());

        if (!is_null($recipe = $comment->getRecipe())) {
            $recipe->setTimesReacted($recipe->getTimesReacted() + 1);
        }

        $this->em->persist($comment);
        $this->em->flush();

        return $comment;
    }

    /**
     * @throws Exception
     */
    public function update(Comment $comment): void
    {
        $this->profanityCheckService->check($comment->getContent());
        $this->em->flush();
    }

    /**
     * When the comment is deleted the times reacted of its recipe is lowered by 1.
     */
    public function delete(Comment $comment): void
    {
        if (!is_null($recipe = $comment->getRecipe())) {
            $recipe->setTimesReacted($recipe->getTimesReacted() - 1);
        }

        $this->em->remove($comment);
        $this->em->flush();
    }
}
