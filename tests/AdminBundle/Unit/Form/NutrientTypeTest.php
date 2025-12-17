<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Unit\Form;

use App\AdminBundle\Form\NutrientType;
use App\Entity\Nutrient;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Symfony\Component\Form\Test\TypeTestCase;

#[AllowMockObjectsWithoutExpectations]
class NutrientTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $displayName =  'test NL';
        $minRDA =  8.0;
        $maxRDA =  9.0;
        $unit =  'g';
        $decimalPlaces = 2;
        $formData = [
            'display_name' => $displayName,
            'min_rda' => $minRDA,
            'max_rda' => $maxRDA,
            'unit' => $unit,
            'decimal_places' => $decimalPlaces,
        ];

        $comment = new Nutrient();

        $form = $this->factory->create(NutrientType::class, $comment);

        $expected = new Nutrient();
        $expected->setDisplayName($displayName);
        $expected->setMinRDA($minRDA);
        $expected->setMaxRDA($maxRDA);
        $expected->setUnit($unit);
        $expected->setDecimalPlaces($decimalPlaces);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $comment);
    }
}
