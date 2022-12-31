<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Unit\Form;

use App\Entity\Rating;
use App\UserBundle\Form\ReviewType;
use Symfony\Component\Form\Test\TypeTestCase;

class ReviewTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $ratingNumber = 8;
        $content = 'Test';
        $formData = [
            'rating' => $ratingNumber,
            'content' => $content,
            ];

        $review = new Rating();

        $form = $this->factory->create(ReviewType::class, $review);

        $expected = new Rating();
        $expected->setRating($ratingNumber);
        $expected->setContent($content);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $review);
    }
}
