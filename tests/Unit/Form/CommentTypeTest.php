<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

class CommentTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $content =  'test';
        $formData = [
            'content' => $content,
        ];

        $comment = new Comment();

        $form = $this->factory->create(CommentType::class, $comment);

        $expected = new Comment();
        $expected->setContent($content);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $comment);
    }
}
