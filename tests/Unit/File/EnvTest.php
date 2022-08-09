<?php

declare(strict_types=1);

namespace App\Tests\Unit\File;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

class EnvTest extends TestCase
{
    public function testEnv(): void
    {
        $projectDir = dirname(__DIR__, 3);

	    $dotEnv = new Dotenv('dev');
        $envNames = $dotEnv->parse(file_get_contents($projectDir . '/.env.local'));

        $dotEnv = new Dotenv('dev');
        $envExampleNames = $dotEnv->parse(file_get_contents($projectDir . '/.env.local.example'));

        $this->assertSameSize($envNames, $envExampleNames);

        foreach ($envExampleNames as $key => $value) {
            $value = rtrim($value);
            if ($envNames[$key] === "") {
                $this->assertTrue($value === "");
            } elseif ($key === 'DATABASE_URL') {
                continue;
            } else {
                $this->assertTrue(
                    str_starts_with($envNames[$key], $value),
                    "the first part of $envNames[$key] is not $value",
                );
            }
        }
    }
}
