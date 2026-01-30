<?php

declare(strict_types=1);

namespace Locastic\SymfonyTranslationBundle\Provider;

use Locastic\SymfonyTranslationBundle\Model\TranslationValueInterface;

final readonly class TranslationFilePathProvider implements TranslationFilePathProviderInterface
{
    public function __construct(private DefaultTranslationDirectoryProviderInterface $defaultTranslationDirectoryProvider)
    {
    }

    public function getFilePath(TranslationValueInterface $translationValue): string
    {
        return $this->defaultTranslationDirectoryProvider->getDefaultDirectory();
    }
}
