<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository implements TagRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Tag::class);
    }

    public function create(Tag $tag, bool $isFlushed = true): Tag
    {
        $this->em->persist($tag);
        if ($isFlushed) {
            $this->em->flush();
        }

        return $tag;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(Tag $tag): void
    {
        foreach ($tag->getRecipes() as $recipe) {
            $tag->removeRecipe($recipe);
        }
        $this->em->remove($tag);
        $this->em->flush();
    }
}
