<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Cookbook;
use App\Form\CookbookType;
use Symfony\Component\Form\Test\TypeTestCase;

class CookbookTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $title = 'Test';
        $formData = ['title' => $title];

        $cookbook = new Cookbook();

        $form = $this->factory->create(CookbookType::class, $cookbook);

        $expected = new Cookbook();
        $expected->setTitle($title);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $cookbook);
    }
}
