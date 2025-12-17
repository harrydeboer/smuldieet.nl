<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Unit\Form;

use App\AdminBundle\Form\PageType;
use App\Entity\Page;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Symfony\Component\Form\Test\TypeTestCase;

#[AllowMockObjectsWithoutExpectations]
class PageTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $title = 'testPage';
        $slug = 'testSlug';
        $summary = 'testSummary';
        $content = 'testContent';
        $formData = [
            'title' => $title,
            'slug' => $slug,
            'summary' => $summary,
            'content' => $content,
        ];

        $page = new Page();

        $form = $this->factory->create(PageType::class, $page);

        $expected = new Page();
        $expected->setTitle($title);
        $expected->setSlug($slug);
        $expected->setSummary($summary);
        $expected->setContent($content);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $page);
    }
}
