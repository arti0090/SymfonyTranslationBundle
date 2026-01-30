<?php

declare(strict_types=1);

namespace Locastic\SymfonyTranslationBundle\Provider;

final readonly class DefaultTranslationDirectoryProvider implements DefaultTranslationDirectoryProviderInterface
{
    public function __construct(private string $translationsDirectory)
    {
    }

    public function getDefaultDirectory(): string
    {
        return $this->translationsDirectory;
    }
}
