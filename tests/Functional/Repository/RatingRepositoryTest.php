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
        $rating = static::getContainer()->get(RatingFactory::class)->create(['pending' => false]);
        $reviewPending = static::getContainer()->get(RatingFactory::class)->create([
            'pending' => true,
            'content' => 'test',
        ]);
        $oldRating = $rating->getRating();

        $ratingRepository = static::getContainer()->get(RatingRepositoryInterface::class);

        $this->assertSame($rating, $ratingRepository->find($rating->getId()));

        $updatedRating = 9.0;
        $rating->setRating($updatedRating);

        $ratingRepository->update($oldRating, $rating);

        $this->assertSame($updatedRating, $ratingRepository->findOneBy(['rating' => $updatedRating * 10])->getRating());
        $this->assertSame($rating, $ratingRepository->getFromUser($rating->getId(), $rating->getUser()->getId()));
        $this->assertSame([$reviewPending], $ratingRepository->findAllPendingReviews());

        $id = $rating->getId();
        $ratingRepository->delete($rating);

        $this->assertNull($ratingRepository->find($id));
    }
}
