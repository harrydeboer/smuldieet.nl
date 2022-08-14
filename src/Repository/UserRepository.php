<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

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
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword(
            $this->passwordEncoder->hashPassword($user, $newPassword)
        );

        $this->em->persist($user);
        $this->em->flush();
    }

    public function create(User $user, string $plainPassword): User
    {
        $user->setPassword(
            $this->passwordEncoder->hashPassword($user, $plainPassword)
        );

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function update(): void
    {
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

        $this->em->remove($user);
        $this->em->flush();
    }
}
