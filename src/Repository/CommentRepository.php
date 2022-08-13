<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

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
        private readonly RecipeRepositoryInterface $recipeRepository,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Comment::class);
    }


    public function create(Comment $comment): Comment
    {
        if (!is_null($recipe = $comment->getRecipe())) {
            $recipe->setTimesReacted($recipe->getTimesReacted() + 1);
            $this->recipeRepository->update($recipe);
        }
        $this->em->persist($comment);
        $this->em->flush();

        return $comment;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(Comment $comment): void
    {
        if (!is_null($recipe = $comment->getRecipe())) {
            $recipe->setTimesReacted($recipe->getTimesReacted() - 1);
            $this->recipeRepository->update($recipe);
        }

        $this->em->remove($comment);
        $this->em->flush();
    }
}
