<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Day;
use App\Entity\Foodstuff;
use App\Entity\FoodstuffWeight;
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
            'foodstuff_weights' => [0 => ['foodstuff_id' => $foodstuff->getId(), 'value' => 20, 'unit' => 'g']],
        ];

        $day = new Day();

        $form = $this->factory->create(StandardDayType::class, $day);

        $expected = new Day();
        $collection = new ArrayCollection();
        $weight = new FoodstuffWeight();
        $weight->setDay($expected);
        $weight->setFoodstuffId($foodstuff->getId());
        $weight->setUnit('g');
        $weight->setValue(20);
        $collection->add($weight);
        $expected->setFoodstuffWeights($collection);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $day);
    }
}
