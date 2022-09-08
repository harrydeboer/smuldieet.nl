<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Day;
use App\Entity\Foodstuff;
use App\Form\StandardDayType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Test\TypeTestCase;

class StandardDayTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $foodstuff = new Foodstuff();
        $foodstuff->setId(1);
        $formData = [
            'foodstuff_weights' => [$foodstuff->getId() => 20],
        ];

        $day = new Day();

        $form = $this->factory->create(StandardDayType::class, $day);

        $expected = new Day();
        $collection = new ArrayCollection();
        $collection->set((string) ($foodstuff->getId()), 20.0);
        $expected->setFoodstuffWeights($collection);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $day);
    }
}
