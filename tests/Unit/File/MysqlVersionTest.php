<?php

declare(strict_types=1);

namespace App\Tests\Unit\File;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;

class MysqlVersionTest extends TestCase
{
    public function testMysqlVersionsSameDotenvAndDockerCompose(): void
    {
        $projectDir = dirname(__DIR__, 3);

        $yamlArray = Yaml::parse(file_get_contents($projectDir . '/docker-compose.yml'));
        $imageArray = explode(':', $yamlArray['services']['database']['image']);

        $dotEnv = new Dotenv('dev');
        $envNames = $dotEnv->parse(file_get_contents($projectDir . '/.env.local'));

        $databaseUrlArray = explode('=', $envNames['DATABASE_URL']);

        $this->assertEquals($imageArray[1], $databaseUrlArray[1]);
    }
}
