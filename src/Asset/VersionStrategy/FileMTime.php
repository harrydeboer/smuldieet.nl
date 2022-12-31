<?php

declare(strict_types=1);

namespace App\Asset\VersionStrategy;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Exception;

readonly class FileMTime implements VersionStrategyInterface
{
    public function __construct(
        private KernelInterface $kernel,
    ) {
    }

    public function getVersion(string $path): string
    {
        try {
            $version = (string) filemtime($this->kernel->getProjectDir() . '/public/' . $path);
        } catch (Exception) {
            return '';
        }

        return $version;
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
