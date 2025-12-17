<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Unit\Form;

use App\AdminBundle\Form\ApproveType;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Symfony\Component\Form\Test\TypeTestCase;

#[AllowMockObjectsWithoutExpectations]
class ApproveTypeTest extends TypeTestCase
{
    public function testApprove(): void
    {
        $formData = [
        ];

        $form = $this->factory->create(ApproveType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
