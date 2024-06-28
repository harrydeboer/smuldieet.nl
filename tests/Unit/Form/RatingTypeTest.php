<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Rating;
use App\Form\RatingType;
use Symfony\Component\Form\Test\TypeTestCase;

class RatingTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $ratingNumber = 9;
        $formData = [
            'rating' => $ratingNumber,
        ];

        $rating = new Rating();

        $form = $this->factory->create(RatingType::class, $rating);

        $expected = new Rating();
        $expected->setRating($ratingNumber);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $rating);
    }
}
