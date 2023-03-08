<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\NutrientsInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class NutrientsInterfaceTest extends TestCase
{
    public function testConstAndMethodsInSync()
    {
        $methods = get_class_methods(NutrientsInterface::class);
        $names = NutrientsInterface::NAMES;

        foreach ($names as $name) {
            $this->assertTrue(in_array('get' . ucfirst($name), $methods));
            $this->assertTrue(in_array('set' . ucfirst($name), $methods));
        }

        foreach ($methods as $method) {
            if (str_starts_with($method, 'get')) {
                $this->assertTrue(in_array(lcfirst(substr($method, 3)), $names));
            } elseif (str_starts_with($method, 'set')) {
                $this->assertTrue(in_array(lcfirst(substr($method, 3)), $names));
            } else {
                throw new Exception('Method must start with get or set.');
            }
        }
    }
}
