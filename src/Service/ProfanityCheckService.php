<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ProfanityRepositoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ProfanityCheckService
{
    public function __construct(
        private readonly ProfanityRepositoryInterface $profanityRepository,
    ) {
    }

    public function check(?string $content): void
    {
        if (is_null($content)) {
            return;
        } else {
            $content = preg_replace('/[^A-zÀ-ÿ\s]/', '', $content);
        }

        $contentArray = explode(' ', strtolower($content));
        foreach ($this->profanityRepository->findAll() as $profanity) {
            if (in_array(strtolower($profanity->getName()), $contentArray)) {
                throw new BadRequestException('Geen gevloek toegestaan.');
            }
        }
    }
}
