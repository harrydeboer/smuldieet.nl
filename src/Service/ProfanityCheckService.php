<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ProfanityRepositoryInterface;
use Exception;

/**
 * This service checks if a string contains profanities.
 */
readonly class ProfanityCheckService
{
    public function __construct(
        private ProfanityRepositoryInterface $profanityRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function check(?string $content): void
    {
        if (is_null($content)) {
            return;
        } else {
            $content = preg_replace('/[^A-Za-zÀ-ÿ\s]/', '', $content);
        }

        $contentArray = explode(' ', strtolower($content));
        foreach ($this->profanityRepository->findAll() as $profanity) {
            if (in_array(strtolower($profanity->getName()), $contentArray)) {
                throw new Exception('Geen gevloek toegestaan.');
            }
        }
    }
}
