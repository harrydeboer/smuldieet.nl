<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Pagination\Paginator;
use App\Service\UploadedImageService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Exception;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordEncoder,
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
        private readonly DayRepositoryInterface $dayRepository,
        private readonly RatingRepositoryInterface $ratingRepository,
        private readonly CommentRepositoryInterface $commentRepository,
        private readonly CookbookRepositoryInterface $cookbookRepository,
        private readonly UploadedImageService $uploadedImageService,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, User::class);
    }

    public function get(int $id): User
    {
        $user = $this->find($id);

        if (is_null($user)) {
            throw new NotFoundHttpException('De gebruiker bestaat niet.');
        }

        return $user;
    }

    /**
     * @throws Exception
     */
    public function upgradePassword(
        PasswordAuthenticatedUserInterface $user,
        string $newPassword,
        $oldExtension = null
    ): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword(
            $this->passwordEncoder->hashPassword($user, $newPassword)
        );

        $this->uploadedImageService->moveImage($user, $oldExtension);

        $this->em->persist($user);
        $this->em->flush();
    }

    public function findAllPaginated(int $page): Paginator
    {
        $qb = $this->createQueryBuilder('u');

        return (new Paginator($qb))->paginate($page);
    }

    /**
     * @throws Exception
     */
    public function create(User $user, string $plainPassword): User
    {
        $user->setPassword(
            $this->passwordEncoder->hashPassword($user, $plainPassword)
        );

        $this->em->persist($user);
        $this->em->flush();

        $this->uploadedImageService->moveImage($user);

        return $user;
    }

    /**
     * @throws Exception
     */
    public function update(User $user, ?string $oldExtension): void
    {
        $this->uploadedImageService->moveImage($user, $oldExtension);

        $this->em->flush();
    }

    public function delete(User $user): void
    {
        foreach ($user->getPages() as $page) {
            $this->pageRepository->delete($page);
        }
        foreach ($user->getDays() as $day) {
            $this->dayRepository->delete($day);
        }
        foreach ($user->getCookbooks() as $cookbook) {
            $this->cookbookRepository->delete($cookbook);
        }
        foreach ($user->getRecipes() as $recipe) {
            $this->recipeRepository->delete($recipe);
        }
        foreach ($user->getRatings() as $rating) {
            $this->ratingRepository->delete($rating);
        }
        foreach ($user->getComments() as $comment) {
            $this->commentRepository->delete($comment);
        }
        foreach ($user->getFoodstuffs() as $foodstuff) {
            $this->foodstuffRepository->delete($foodstuff);
        }
        foreach ($user->getSavedRecipes() as $savedRecipe) {
            $user->removeSavedRecipe($savedRecipe);
        }
        $this->uploadedImageService->unlinkImage($user);

        $this->em->remove($user);
        $this->em->flush();
    }
}
