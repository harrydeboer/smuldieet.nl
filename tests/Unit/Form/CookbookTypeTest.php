<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\CookbookType;
use Symfony\Component\Form\Test\TypeTestCase;

class CookbookTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = ['title' => 'Test'];

        $form = $this->factory->create(CookbookType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
