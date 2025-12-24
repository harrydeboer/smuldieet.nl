<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\User;
use App\Pagination\Paginator;
use App\Service\ProfanityCheckService;
use App\Service\UploadedImageService;
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
        private readonly RecipeFoodstuffWeightRepositoryInterface $recipeFoodstuffWeightRepository,
        private readonly CookbookRecipeWeightRepositoryInterface $cookbookRecipeWeightRepository,
        private readonly DayRecipeWeightRepositoryInterface $dayRecipeWeightRepository,
        private readonly TagRepositoryInterface $tagRepository,
        private readonly ProfanityCheckService $profanityCheckService,
        private readonly EntityManagerInterface $em,
        private readonly UploadedImageService $uploadedImageService,
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

    /**
     * @return Recipe[]
     */
    public function search(string $title, int $userId): array
    {
        $titleOrderBy = str_replace("'", "''", $title);
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.title like :title')
            ->setParameter('title', '%' . addslashes($title) . '%')
            ->andWhere('r.pending = 0 or r.pending = 1 and r.user = :userId')
            ->setParameter('userId', $userId)
            ->setMaxResults(20)
            ->addSelect("(CASE WHEN r.title like '" . $titleOrderBy . " %' THEN 0 WHEN r.title like '" .
                $titleOrderBy . "%' THEN 1 WHEN r.title like '%" . $titleOrderBy . "%' THEN 2 ELSE 3 END)" .
                " AS HIDDEN ORD ")
            ->orderBy('ORD', 'ASC');

        $query = $qb->getQuery();

        return $query->execute();
    }

    /**
     * @return Recipe[]
     */
    public function findAllPending(): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.pending = 1');

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
            ->andWhere('r.pending = 0 or r.pending = 1 and r.user = :userId')
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
        $recipe->setCreatedAt(time());
        $this->em->persist($recipe);
        $this->em->flush();

        $this->uploadedImageService->moveImage($recipe);

        return $recipe;
    }

    /**
     * @throws Exception
     */
    public function update(
        Recipe $recipe,
        Collection $oldFoodstuffWeights,
        Collection $oldTags,
        ?string $oldExtension
    ): void
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

        $this->uploadedImageService->moveImage($recipe, $oldExtension);
    }

    public function delete(Recipe $recipe): void
    {
        foreach ($recipe->getFoodstuffWeights() as $foodstuffWeight) {
            $this->recipeFoodstuffWeightRepository->delete($foodstuffWeight);
        }
        foreach ($recipe->getCookbookRecipeWeights() as $recipeWeight) {
            $this->cookbookRecipeWeightRepository->delete($recipeWeight);
        }
        foreach ($recipe->getDayRecipeWeights() as $recipeWeight) {
            $this->dayRecipeWeightRepository->delete($recipeWeight);
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
        $this->uploadedImageService->unlinkImage($recipe);

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

    public function getRecipesFromUser(int $userId, int $page): Paginator
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('r.createdAt', 'DESC');

        return new Paginator($qb)->paginate($page);
    }

    public function findRecent(int $limit): Paginator
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.pending = 0')
            ->setMaxResults($limit)
            ->orderBy('r.createdAt', 'DESC');

        return new Paginator($qb, $limit)->paginate();
    }

    public function findBySortAndFilter(int $page, ?array $formData = null): Paginator
    {
        $qb = $this->createQueryBuilder('r');
        $qb->orderBy('r.createdAt', 'DESC');
        $qb->where('r.pending = 0');

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

        return new Paginator($qb)->paginate($page);
    }

    private function addTags(Recipe $recipe): void
    {
        $tags = $recipe->getTags();

        foreach ($tags as $tag) {
            $recipe->removeTag($tag);
            $tagDb = $this->tagRepository->findOneBy(['name' => $tag->getName()]);
            if (is_null($tagDb)) {
                $tag->setCreatedAt(time());
                $this->tagRepository->create($tag);
                $recipe->addTag($tag);
            } else {
                $recipe->addTag($tagDb);
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
