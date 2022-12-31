<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Form;

use App\AdminBundle\Form\CommentType;
use App\Tests\Functional\AuthUserWebTestCase;

class CommentTypeTest extends AuthUserWebTestCase
{
    public function testSubmitModel(): void
    {
        $formData = ['is_pending' => false];

        $form = $this->getContainer()->get('form.factory')->create(CommentType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
