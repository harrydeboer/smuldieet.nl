<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Form\CookbookType;
use App\Tests\Functional\AuthWebTestCase;

class CookbookTypeTest extends AuthWebTestCase
{
    public function testSubmitModel(): void
    {
        $formData = ['title' => 'Test'];
        $form = $this->getContainer()->get('form.factory')->create(CookbookType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
