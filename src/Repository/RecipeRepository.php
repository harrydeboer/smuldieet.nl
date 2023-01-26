<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\User;
use App\Pagination\Paginator;
use App\Service\ProfanityCheckService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;
use Doctrine\Common\Collections\Collection;

/**
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository implements RecipeRepositoryInterface
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
        private readonly CommentRepositoryInterface $commentRepository,
        private readonly FoodstuffWeightRepositoryInterface $foodstuffWeightRepository,
        private readonly RecipeWeightRepositoryInterface $recipeWeightRepository,
        private readonly TagRepositoryInterface $tagRepository,
        private readonly ProfanityCheckService $profanityCheckService,
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Recipe::class);
    }

    public function getFromUser(int $id, int $userId): Recipe
    {
        $recipe = $this->findOneBy(['id' => $id, 'user' => $userId]);

        if (is_null($recipe)) {
            throw new NotFoundHttpException('Dit recept bestaat niet of hoort niet bij jou.');
        }

        return $recipe;
    }

    public function search(string $title, int $userId): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.title like :title')
            ->setParameter('title', '%' . $title . '%')
            ->andWhere('r.isPending = 0 or r.isPending = 1 and r.user = :userId')
            ->setParameter('userId', $userId)
            ->setMaxResults(20)
            ->addSelect("(CASE WHEN r.title like '" . $title . " %' THEN 0 WHEN r.title like '" . $title . "%' " .
                "THEN 1 WHEN r.title like '%" . $title . "%' THEN 2 ELSE 3 END) AS HIDDEN ORD ")
            ->orderBy('ORD', 'ASC');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findAllPending(): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.isPending = 1');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function get(int $id): Recipe
    {
        $recipe = $this->find($id);

        if (is_null($recipe)) {
            throw new NotFoundHttpException('Dit recept bestaat niet.');
        }

        return $recipe;
    }

    public function getNotPendingOrFromUser(int $id, int $userId): Recipe
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.id = ' . $id)
            ->andWhere('r.isPending = 0 or r.isPending = 1 and r.user = :userId')
            ->setParameter('userId', $userId);

        $query = $qb->getQuery();

        return $query->execute()[0];
    }

    /**
     * @throws Exception
     */
    public function create(Recipe $recipe): Recipe
    {
        $this->checkProfanitiesRecipe($recipe);
        $this->addTags($recipe);
        $recipe->setTimestamp(time());
        $this->em->persist($recipe);
        $this->em->flush();

        return $recipe;
    }

    /**
     * @throws Exception
     */
    public function update(Recipe $recipe, Collection $oldFoodstuffWeights, Collection $oldTags): void
    {
        foreach ($oldFoodstuffWeights as $weight) {
            if (!$recipe->getFoodstuffWeights()->contains($weight)) {
                $this->em->remove($weight);
            }
        }
        foreach ($oldTags as $tag) {
            if (!$recipe->getTags()->contains($tag)) {
                $recipe->removeTag($tag);
            }
        }
        $this->checkProfanitiesRecipe($recipe);
        $this->addTags($recipe);
        $this->em->flush();
    }

    public function delete(Recipe $recipe): void
    {
        foreach ($recipe->getFoodstuffWeights() as $foodstuffWeight) {
            $this->foodstuffWeightRepository->delete($foodstuffWeight);
        }
        foreach ($recipe->getRecipeWeights() as $recipeWeight) {
            $this->recipeWeightRepository->delete($recipeWeight);
        }
        foreach ($recipe->getRatings() as $rating) {
            $this->ratingRepository->delete($rating);
        }
        foreach ($recipe->getComments() as $comment) {
            $this->commentRepository->delete($comment);
        }
        foreach ($recipe->getUsers() as $user) {
            $recipe->removeUser($user);
        }
        foreach ($recipe->getTags() as $tag) {
            $recipe->removeTag($tag);
        }
        $this->em->remove($recipe);
        $this->em->flush();
    }

    public function addUser(Recipe $recipe, User $user): void
    {
        $recipe->addUser($user);
        $recipe->setTimesSaved($recipe->getTimesSaved() + 1);
        $this->em->flush();
    }

    public function removeUser(Recipe $recipe, User $user): void
    {
        $recipe->removeUser($user);
        $recipe->setTimesSaved($recipe->getTimesSaved() - 1);
        $this->em->flush();
    }

    public function getRecipesFromUser(int $userId, int $page): Paginator|array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('r.timestamp', 'DESC');

        return (new Paginator($qb))->paginate($page);
    }

    public function findRecent(int $limit): Paginator|array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.isPending = 0')
            ->setMaxResults($limit)
            ->orderBy('r.timestamp', 'DESC');

        return (new Paginator($qb, $limit))->paginate();
    }

    public function findBySortAndFilter(int $page, array $formData = null): Paginator|array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->orderBy('r.timestamp', 'DESC');
        $qb->where('r.isPending = 0');

        if (!is_null($formData)) {
            if (!is_null($formData['title'])) {
                $qb->andWhere('Lower(r.title) like :title')
                    ->setParameter('title', '%' . $formData['title'] . '%');
            }
            if (!is_null($formData['type_of_dish'])) {
                $qb->andWhere('r.typeOfDish = :typeOfDish')
                    ->setParameter('typeOfDish', $formData['type_of_dish']);
            }
            if (!is_null($formData['cooking_time'])) {
                $qb->andWhere('r.cookingTime = :cookingTime')
                    ->setParameter('cookingTime', $formData['cooking_time']);
            }
            if (!is_null($formData['kitchen'])) {
                $qb->andWhere('r.kitchen = :kitchen')
                    ->setParameter('kitchen', $formData['kitchen']);
            }
            if (!is_null($formData['occasion'])) {
                $qb->andWhere('r.occasion = :occasion')
                    ->setParameter('occasion', $formData['occasion']);
            }
            foreach (Recipe::getDietChoices() as $choice => $label) {
                if ($formData[strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $choice))]) {
                    $qb->andWhere('r.' . $choice . ' = 1');
                }
            }
            if (!is_null($formData['sort'])) {
                $filterArray = explode('_', $formData['sort']);
                $qb->orderBy('r.' . $filterArray[0], $filterArray[1]);
            }
        }

        return (new Paginator($qb))->paginate($page);
    }

    private function addTags(Recipe $recipe): void
    {
        $tags = $recipe->getTags();

        foreach ($tags as $tag) {
            $tagDb = $this->tagRepository->findOneBy(['name' => $tag->getName()]);
            if (is_null($tagDb)) {
                $this->tagRepository->create($tag);
                $recipe->addTag($tag);
            }
        }
    }

    /**
     * @throws Exception;
     */
    private function checkProfanitiesRecipe(Recipe $recipe): void
    {
        $this->profanityCheckService->check($recipe->getTitle());
        $this->profanityCheckService->check($recipe->getIngredients());
        $this->profanityCheckService->check($recipe->getPreparationMethod());
        $this->profanityCheckService->check($recipe->getSource());
        foreach ($recipe->getTags() as $tag) {
            $this->profanityCheckService->check($tag->getName());
        }
    }
}
