<?php

declare(strict_types=1);

namespace App\Asset\VersionStrategy;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * The class adds a modification time to the assets. This way the cache is busted when the asset changes.
 */
readonly class FileMTime implements VersionStrategyInterface
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function getVersion(string $path): string
    {
        try {
            $version = (string) filemtime($this->parameterBag->get('kernel.project_dir') . '/public/' . $path);
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
