<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Entity\Day;
use App\Entity\DayFoodstuffWeight;
use App\Form\StandardDayType;
use App\Repository\FoodstuffRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class StandardDayTypeTest extends AuthVerifiedWebTestCase
{
    public function testSubmitModel(): void
    {
        $foodstuff = static::getContainer()
            ->get(FoodstuffRepositoryInterface::class)
            ->findOneBy(['name' => 'verified']);
        $formData = [
            'foodstuff_weights' => [0 => ['foodstuff_id' => $foodstuff->getId(), 'value' => 20, 'unit' => 'g']],
        ];

        $day = new Day();

        $form = $this->getContainer()->get('form.factory')->create(StandardDayType::class, $day);

        $expected = new Day();
        $collection = new ArrayCollection();
        $weight = new DayFoodstuffWeight();
        $weight->setDay($expected);
        $weight->setFoodstuffId($foodstuff->getId());
        $weight->setUnit('g');
        $weight->setFoodstuff($foodstuff);
        $weight->setValue(20);
        $collection->add($weight);
        $expected->setFoodstuffWeights($collection);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $day);
    }
}
