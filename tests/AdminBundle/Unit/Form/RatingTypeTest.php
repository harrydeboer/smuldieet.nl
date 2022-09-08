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
        $formData = [
            'is_pending' => false,
        ];

        $rating = new Rating();

        $form = $this->factory->create(ReviewType::class, $rating);

        $expected = new Rating();
        $expected->setIsPending(false);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $rating);
    }
}
