<?php

declare(strict_types=1);

namespace Locastic\SymfonyTranslationBundle\Provider;

final class LocalesProvider implements LocalesProviderInterface
{
    public function getLocalesFromCode(string $localeCode): array
    {
        if (preg_match('/^[a-z]{2}$/', $localeCode)) {
            return [$localeCode];
        }
        $localeStart = mb_substr($localeCode, 0, 2);

        return [
            $localeStart,
            $localeCode,
        ];
    }
}
