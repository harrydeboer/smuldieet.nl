<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\VerifyType;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Symfony\Component\Form\Test\TypeTestCase;

#[AllowMockObjectsWithoutExpectations]
class VerifyTypeTest extends TypeTestCase
{
    public function testVerify(): void
    {
        $formData = [
        ];

        $form = $this->factory->create(VerifyType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
