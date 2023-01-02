<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Tests\Factory\RatingFactory;
use App\Repository\RatingRepositoryInterface;
use App\Tests\Functional\KernelTestCase;

class RatingRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $rating = static::getContainer()->get(RatingFactory::class)->create(['isPending' => false]);
        $reviewPending = static::getContainer()->get(RatingFactory::class)->create([
            'isPending' => true,
            'content' => 'test1',
        ]);
        $reviewNotPending = static::getContainer()->get(RatingFactory::class)->create([
            'isPending' => false,
            'content' => 'test2',
        ]);
        $oldRating = $rating->getRating();

        $ratingRepository = static::getContainer()->get(RatingRepositoryInterface::class);

        $this->assertSame($rating, $ratingRepository->get($rating->getId()));
        $this->assertSame($reviewNotPending, $ratingRepository->getNotPending($reviewNotPending->getId()));

        $updatedRating = 9.0;
        $rating->setRating($updatedRating);

        $ratingRepository->update($oldRating, $rating);

        $this->assertSame($updatedRating, $ratingRepository->findOneBy(['rating' => $updatedRating * 10])->getRating());
        $this->assertSame($rating, $ratingRepository->getFromUser($rating->getId(), $rating->getUser()->getId()));
        $this->assertSame([$reviewPending], $ratingRepository->findAllPendingReviews());
        $this->assertSame([$reviewNotPending],
            $ratingRepository->findReviewsFromUser($reviewNotPending->getUser()->getId()));
        $this->assertSame($reviewNotPending, $ratingRepository->findReviewsFromRecipe(
            $reviewNotPending->getRecipe()->getId(), 1)->getResults()[0]);
        $this->assertSame([$reviewNotPending], $ratingRepository
            ->findAllFromUser($reviewNotPending->getUser()->getId()));

        $id = $rating->getId();
        $ratingRepository->delete($rating);

        $this->assertNull($ratingRepository->find($id));
    }
}
