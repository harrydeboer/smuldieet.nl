<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Unit\Form;

use App\AdminBundle\Form\NutrientType;
use App\Entity\Nutrient;
use Symfony\Component\Form\Test\TypeTestCase;

class NutrientTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $name =  'test';
        $nameNL =  'testNL';
        $minRDA =  8.0;
        $maxRDA =  9.0;
        $unit =  'g';
        $formData = [
            'name' => $name,
            'name_nl' => $nameNL,
            'min_rda' => $minRDA,
            'max_rda' => $maxRDA,
            'unit' => $unit,
        ];

        $comment = new Nutrient();

        $form = $this->factory->create(NutrientType::class, $comment);

        $expected = new Nutrient();
        $expected->setName($name);
        $expected->setNameNL($nameNL);
        $expected->setMinRDA($minRDA);
        $expected->setMaxRDA($maxRDA);
        $expected->setUnit($unit);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $comment);
    }
}
