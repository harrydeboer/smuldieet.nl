<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Recipe;
use PHPUnit\Framework\TestCase;

class RecipeTest extends TestCase
{
    public function testGet()
    {
        $recipe = new Recipe();

        $names = $recipe->getDietNames();

        $dietNames = Recipe::getDietChoices();

        $this->assertEquals(array_keys($dietNames), $names);
    }
}
