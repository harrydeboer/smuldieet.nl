<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Entity\FoodstuffWeight;
use App\Form\CombineFoodstuffsType;
use App\Repository\FoodstuffRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class CombineFoodstuffsTypeTest extends AuthVerifiedWebTestCase
{
    public function testSubmitModel(): void
    {
        $foodstuff = static::getContainer()
            ->get(FoodstuffRepositoryInterface::class)
            ->findOneBy(['name' => 'verified']);
        $name = 'test';
        $formData = [
            'name' => $name,
            'foodstuff_weights' => [0 => ['foodstuff_id' => $foodstuff->getId(), 'value' => 100, 'unit' => 'g']],
        ];

        $form = $this->getContainer()->get('form.factory')->create(CombineFoodstuffsType::class);

        $form->submit($formData);
        $weight = new FoodstuffWeight();
        $weight->setFoodstuffId($foodstuff->getId());
        $weight->setFoodstuff($foodstuff);
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
