<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\RatingRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RatingRepositoryTest extends KernelTestCase
{
    private function getRatingRepository(): RatingRepositoryInterface
    {
        return static::getContainer()->get(RatingRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $ratingRepository = $this->getRatingRepository();

        $rating = $ratingRepository->findOneBy(['content' => null]);
        $reviewPending = $ratingRepository->findOneBy(['content' => 'testPending']);
        $reviewNotPending = $ratingRepository->findOneBy(['content' => 'test']);
        $oldRating = $rating->getRating();

        $this->assertSame($rating, $ratingRepository->get($rating->getId()));
        $this->assertSame($reviewNotPending, $ratingRepository->getNotPending($reviewNotPending->getId()));

        $updatedRating = 9.0;
        $rating->setRating($updatedRating);

        $ratingRepository->update($oldRating, $rating);

        $this->assertSame($updatedRating, $ratingRepository->findOneBy(['rating' => $updatedRating * 10])->getRating());
        $this->assertSame($rating, $ratingRepository->getFromUser($rating->getId(), $rating->getUser()->getId()));
        $this->assertTrue(in_array($reviewPending, $ratingRepository->findAllPendingReviews()));
        $this->assertTrue(in_array($reviewNotPending,
            $ratingRepository->findReviewsFromUser($reviewNotPending->getUser()->getId())));
        $this->assertTrue(in_array($reviewNotPending, $ratingRepository->findReviewsFromRecipe(
            $reviewNotPending->getRecipe()->getId(), 1)->getResults()));
        $this->assertTrue(in_array($reviewNotPending, $ratingRepository
            ->findAllFromUser($reviewNotPending->getUser()->getId())));

        $id = $rating->getId();
        $ratingRepository->delete($rating);

        $this->expectException(NotFoundHttpException::class);

        $ratingRepository->get($id);
    }
}
