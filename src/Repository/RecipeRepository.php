<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Recipe;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository implements RecipeRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Recipe::class);
    }

    public function getFromUser(int $id, int $userId): Recipe
    {
        $recipe = $this->findOneBy(["id" => $id, "user" => $userId]);

        if (is_null($recipe)) {
            throw new NotFoundHttpException("Dit recept bestaat niet of hoort niet bij jou.");
        }

        return $recipe;
    }

    public function search(string $title): array
    {
        $qb = $this->createQueryBuilder("r");
        $qb->where("r.title like :title")
            ->setParameter("title", "%" . $title . "%")
            ->setMaxResults(20);

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function get(int $id): Recipe
    {
        $recipe = $this->findOneBy(["id" => $id]);

        if (is_null($recipe)) {
            throw new NotFoundHttpException("Dit recept bestaat niet.");
        }

        return $recipe;
    }

    public function create(Recipe $recipe): Recipe
    {
        $recipe->setTimestamp(time());
        $this->em->persist($recipe);
        $this->em->flush();
        $this->makeWeights($recipe);
        $this->em->persist($recipe);
        $this->em->flush();

        return $recipe;
    }

    public function update(Recipe $recipe): void
    {
        $this->makeWeights($recipe);
        $this->em->flush();
    }

    public function delete(Recipe $recipe): void
    {
        $this->em->remove($recipe);
        $this->em->flush();
    }

    public function getRecipesFromUser(int $userId, int $page): Paginator|array
    {
        $qb = $this->createQueryBuilder("r")
            ->where("r.user = :userId")
            ->setParameter("userId", $userId)
            ->orderBy("r.timestamp", "DESC");

        return (new Paginator($qb))->paginate($page);
    }

    public function findBySortAndFilter(int $page, array $formData = null): Paginator|array
    {
        $qb = $this->createQueryBuilder("r");
        $qb->orderBy("r.timestamp", "DESC");
        $qb->where("r.pending = 0");

        if (!is_null($formData)) {
            if (!is_null($formData["cookingTime"])) {
                $qb->andWhere("r.cookingTime = :cookingTime")
                    ->setParameter("cookingTime", $formData["cookingTime"]);
            }
            if (!is_null($formData["kitchen"])) {
                $qb->andWhere("r.kitchen = :kitchen")
                    ->setParameter("kitchen", $formData["kitchen"]);
            }
            if (!is_null($formData["typeOfDish"])) {
                $qb->andWhere("r.typeOfDish = :typeOfDish")
                    ->setParameter("typeOfDish", $formData["typeOfDish"]);
            }
            if (!is_null($formData["votes"])) {
                $qb->andWhere("r.votes >= :votes")
                    ->setParameter("votes", $formData["votes"]);
            }
            if (!is_null($formData["numberOfPersons"])) {
                $qb->andWhere("r.numberOfPersons = :numberOfPersons")
                    ->setParameter("numberOfPersons", $formData["numberOfPersons"]);
            }
            foreach (Recipe::DIET_CHOICES as $choice) {
                if ($formData[$choice]) {
                    $qb->andWhere("r." . $choice . " = 1");
                }
            }
            if (!is_null($formData["title"])) {
                $qb->andWhere("Lower(r.title) like :title")
                    ->setParameter("title", "%" . $formData["title"] . "%");
            }
            $filterArray = explode("_", $formData["sort"]);
            $qb->orderBy("r." . $filterArray[0], $filterArray[1]);
        }

        return (new Paginator($qb))->paginate($page);
    }

    private function makeWeights(Recipe $recipe): void
    {
        $array = [];
        $count = 0;
        foreach ($recipe->getFoodstuffWeights() as $value) {
            $array[$recipe->getFoodstuffs()->toArray()[$count]->getId()] = $value;
            $count++;
        }
        $recipe->setFoodstuffWeights($array);
    }
}
