<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Recipe;
use App\Pagination\Paginator;
use App\Service\ProfanityCheckService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

/**
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository implements RecipeRepositoryInterface
{
    public function __construct(
        private readonly ProfanityCheckService $profanityCheckService,
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
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
            ->andWhere('r.pending = 0 or r.pending = 1 and r.user = :userId')
            ->setParameter('userId', $userId)
            ->setMaxResults(20)
            ->orderBy('r.title', 'ASC');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findAllPending(): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.pending = 1');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function get(int $id): Recipe
    {
        $recipe = $this->findOneBy(['id' => $id]);

        if (is_null($recipe)) {
            throw new NotFoundHttpException('Dit recept bestaat niet.');
        }

        return $recipe;
    }

    /**
     * @throws Exception
     */
    public function create(Recipe $recipe): Recipe
    {
        $this->checkProfanitiesRecipe($recipe);
        $this->addFoodstuffsFromWeights($recipe);
        $recipe->setTimestamp(time());
        $this->em->persist($recipe);
        $this->em->flush();
        $this->em->persist($recipe);
        $this->em->flush();

        return $recipe;
    }

    /**
     * @throws Exception
     */
    public function update(Recipe $recipe): void
    {
        $this->checkProfanitiesRecipe($recipe);
        foreach ($recipe->getFoodstuffs() as $foodstuff) {
            $recipe->removeFoodstuff($foodstuff);
        }
        $this->addFoodstuffsFromWeights($recipe);
        $this->em->flush();
    }

    public function delete(Recipe $recipe): void
    {
        foreach ($recipe->getDays() as $day) {
            $weights = $day->getRecipeWeights();
            unset($weights[$recipe->getId()]);
            $day->setRecipeWeights($weights);
        }
        $this->em->remove($recipe);
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
            ->where('r.pending = 0')
            ->setMaxResults($limit)
            ->orderBy('r.timestamp', 'DESC');

        return (new Paginator($qb, $limit))->paginate();
    }

    public function findBySortAndFilter(int $page, array $formData = null): Paginator|array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->orderBy('r.timestamp', 'DESC');
        $qb->where('r.pending = 0');

        if (!is_null($formData)) {
            if (!is_null($formData['title'])) {
                $qb->andWhere('Lower(r.title) like :title')
                    ->setParameter('title', '%' . $formData['title'] . '%');
            }
            if (!is_null($formData['typeOfDish'])) {
                $qb->andWhere('r.typeOfDish = :typeOfDish')
                    ->setParameter('typeOfDish', $formData['typeOfDish']);
            }
            if (!is_null($formData['cookingTime'])) {
                $qb->andWhere('r.cookingTime = :cookingTime')
                    ->setParameter('cookingTime', $formData['cookingTime']);
            }
            if (!is_null($formData['kitchen'])) {
                $qb->andWhere('r.kitchen = :kitchen')
                    ->setParameter('kitchen', $formData['kitchen']);
            }
            if (!is_null($formData['occasion'])) {
                $qb->andWhere('r.occasion = :occasion')
                    ->setParameter('occasion', $formData['occasion']);
            }
            foreach (Recipe::DIET_CHOICES as $choice) {
                if ($formData[$choice]) {
                    $qb->andWhere('r.' . $choice . ' = 1');
                }
            }
            $filterArray = explode('_', $formData['sort']);
            $qb->orderBy('r.' . $filterArray[0], $filterArray[1]);
        }

        return (new Paginator($qb))->paginate($page);
    }

    private function addFoodstuffsFromWeights(Recipe $recipe): void
    {
        foreach ($recipe->getFoodstuffWeights() as $id => $weight) {
            $foodstuff = $this->foodstuffRepository->get($id);
            $recipe->addFoodstuff($foodstuff);
        }
    }

    /**
     * @throws Exception;
     */
    private function checkProfanitiesRecipe(Recipe $recipe): void
    {
        $this->profanityCheckService->check($recipe->getTitle());
        $this->profanityCheckService->check($recipe->getIngredients());
        $this->profanityCheckService->check($recipe->getNiceStory());
        $this->profanityCheckService->check($recipe->getNiceTips());
        $this->profanityCheckService->check($recipe->getPreparationMethod());
        $this->profanityCheckService->check($recipe->getSource());
        $this->profanityCheckService->check($recipe->getToolsAndKitchenware());
        foreach ($recipe->getTags() as $tag) {
            $this->profanityCheckService->check($tag->getName());
        }
    }
}
