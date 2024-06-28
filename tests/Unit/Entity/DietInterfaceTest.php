<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\DietInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class DietInterfaceTest extends TestCase
{
    public function testConstAndMethodsInSync()
    {
        $methods = get_class_methods(DietInterface::class);
        $choices = DietInterface::CHOICES;

        foreach ($choices as $choice => $displayName) {
            $this->assertTrue(in_array('is' . ucfirst($choice), $methods));
            $this->assertTrue(in_array('set' . ucfirst($choice), $methods));
        }

        foreach ($methods as $method) {
            if (str_starts_with($method, 'is')) {
                $this->assertTrue(in_array(lcfirst(substr($method, 2)), array_keys($choices)));
            } elseif (str_starts_with($method, 'set')) {
                $this->assertTrue(in_array(lcfirst(substr($method, 3)), array_keys($choices)));
            } else {
                throw new Exception('Method must start with is or set.');
            }
        }
    }
}
