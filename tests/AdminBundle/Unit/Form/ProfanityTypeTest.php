<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Unit\Form;

use App\AdminBundle\Form\ProfanityType;
use App\Entity\Profanity;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Symfony\Component\Form\Test\TypeTestCase;

#[AllowMockObjectsWithoutExpectations]
class ProfanityTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $name = 'scheldwoord';
        $formData = [
            'name' => $name,
        ];

        $profanity = new Profanity();

        $form = $this->factory->create(ProfanityType::class, $profanity);

        $expected = new Profanity();
        $expected->setName($name);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $profanity);
    }
}
