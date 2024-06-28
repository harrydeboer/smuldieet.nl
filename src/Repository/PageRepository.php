<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository implements PageRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CommentRepositoryInterface $commentRepository,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Page::class);
    }

    public function get(int $id): Page
    {
        $page = $this->find($id);

        if (is_null($page)) {
            throw new NotFoundHttpException('Deze pagina bestaat niet.');
        }

        return $page;
    }

    public function getByTitle(string $title): Page
    {
        $page = $this->findOneBy(['title' => $title]);

        if (is_null($page)) {
            throw new NotFoundHttpException('Deze pagina bestaat niet of hoort niet bij jou.');
        }

        return $page;
    }

    public function getBySlug(string $slug): Page
    {
        $page = $this->findOneBy(['slug' => $slug]);

        if (is_null($page)) {
            throw new NotFoundHttpException('Deze pagina bestaat niet of hoort niet bij jou.');
        }

        return $page;
    }

    public function create(Page $page): Page
    {
        $this->em->persist($page);
        $this->em->flush();

        return $page;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(Page $page): void
    {
        foreach ($page->getComments() as $comment) {
            $this->commentRepository->delete($comment);
        }
        $this->em->remove($page);
        $this->em->flush();
    }
}
