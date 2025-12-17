<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Unit\Form;

use App\Entity\Tag;
use App\UserBundle\Form\TagType;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Symfony\Component\Form\Test\TypeTestCase;

#[AllowMockObjectsWithoutExpectations]
class TagTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $name = 'Test';
        $formData = ['name' => $name];

        $cookbook = new Tag();

        $form = $this->factory->create(TagType::class, $cookbook);

        $expected = new Tag();
        $expected->setName($name);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $cookbook);
    }
}
