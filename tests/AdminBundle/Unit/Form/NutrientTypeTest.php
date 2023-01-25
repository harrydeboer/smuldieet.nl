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
        $nameNL =  'testNL';
        $minRDA =  8.0;
        $maxRDA =  9.0;
        $unit =  'g';
        $decimalPlaces = 2;
        $formData = [
            'name_nl' => $nameNL,
            'min_rda' => $minRDA,
            'max_rda' => $maxRDA,
            'unit' => $unit,
            'decimal_places' => $decimalPlaces,
        ];

        $comment = new Nutrient();

        $form = $this->factory->create(NutrientType::class, $comment);

        $expected = new Nutrient();
        $expected->setNameNL($nameNL);
        $expected->setMinRDA($minRDA);
        $expected->setMaxRDA($maxRDA);
        $expected->setUnit($unit);
        $expected->setDecimalPlaces($decimalPlaces);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $comment);
    }
}
