<?php

declare(strict_types=1);

namespace App\Asset\VersionStrategy;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class FileMTime implements VersionStrategyInterface
{
    public function __construct(
        private readonly KernelInterface $kernel,
    ) {
    }

    public function getVersion(string $path): string
    {
        return (string) filemtime($this->kernel->getProjectDir() . '/public/' . $path);
    }

    public function applyVersion(string $path): string
    {
        $version = $this->getVersion($path);

        if ('' === $version) {
            return $path;
        }

        return $path . '?' . $version;
    }
}
