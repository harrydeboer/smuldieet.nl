<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Unit\Form;

use App\AdminBundle\Form\ReviewType;
use App\Entity\Rating;
use Symfony\Component\Form\Test\TypeTestCase;

class RatingTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $content = 'testContent';
        $formData = [
            'rating' => 8,
            'content' => $content,
            'pending' => false,
        ];

        $rating = new Rating();

        $form = $this->factory->create(ReviewType::class, $rating);

        $expected = new Rating();
        $expected->setRating(8);
        $expected->setContent($content);
        $expected->setPending(false);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $rating);
    }
}
