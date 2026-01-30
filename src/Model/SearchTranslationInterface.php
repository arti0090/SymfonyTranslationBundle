<?php

declare(strict_types=1);

namespace Locastic\SymfonyTranslationBundle\Model;

interface SearchTranslationInterface
{
    public function getSearch(): ?string;

    public function setSearch(?string $search): void;
}
