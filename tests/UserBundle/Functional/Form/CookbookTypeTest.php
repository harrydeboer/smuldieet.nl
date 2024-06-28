<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Form;

use App\Entity\Cookbook;
use App\Tests\Functional\AuthVerifiedWebTestCase;
use App\UserBundle\Form\CookbookType;

class CookbookTypeTest extends AuthVerifiedWebTestCase
{
    public function testSubmitModel(): void
    {
        $title = 'Test';
        $formData = ['title' => $title];

        $cookbook = new Cookbook();

        $form = $this->getContainer()->get('form.factory')->create(CookbookType::class, $cookbook);

        $expected = new Cookbook();
        $expected->setTitle($title);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $cookbook);
    }
}
