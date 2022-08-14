<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Factory\RatingFactory;
use App\Repository\RatingRepositoryInterface;
use App\Tests\Functional\KernelTestCase;

class RatingRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $rating = static::getContainer()->get(RatingFactory::class)->create();
        $ratingOldRating = $rating->getRating();

        $ratingRepository = static::getContainer()->get(RatingRepositoryInterface::class);

        $this->assertSame($rating, $ratingRepository->find($rating->getId()));

        $updatedRating = 9.0;
        $rating->setRating($updatedRating);

        $ratingRepository->update($ratingOldRating, $rating);

        $this->assertSame($updatedRating, $ratingRepository->findOneBy(['rating' => $updatedRating * 10])->getRating());

        $id = $rating->getId();
        $ratingRepository->delete($rating);

        $this->assertNull($ratingRepository->find($id));
    }
}
