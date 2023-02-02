<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Foodstuff;
use App\Entity\FoodstuffWeight;
use App\Form\CombineFoodstuffsType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class CombineFoodstuffsTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $foodstuff = new Foodstuff();
        $foodstuff->setId(1);
        $name = 'test';
        $formData = [
            'name' => $name,
            'foodstuff_weights' => [0 => ['foodstuff_id' => $foodstuff->getId(), 'value' => 100, 'unit' => 'g']],
        ];

        $form = $this->factory->create(CombineFoodstuffsType::class);

        $form->submit($formData);
        $weight = new FoodstuffWeight();
        $weight->setFoodstuffId($foodstuff->getId());
        $weight->setUnit('g');
        $weight->setValue(100);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($name, $form->get('name')->getData());
        $this->assertEquals([0 => $weight], $form->get('foodstuff_weights')->getData());
    }

    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }
}
