<?php

declare(strict_types=1);

namespace Locastic\SymfonyTranslationBundle\Controller;

use Locastic\SymfonyTranslationBundle\Saver\TranslationValueSaverInterface;
use Locastic\SymfonyTranslationBundle\Transformer\TranslationValueToFormFieldTransformerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class SaveTranslationsAction
{
    public function __construct(private TranslationValueToFormFieldTransformerInterface $translationValueToFormTransformer, private TranslationValueSaverInterface $translationValueSaver)
    {
    }

    public function __invoke(Request $request): Response
    {
        $translations = $request->request->all('translations');
        $translationValue = $this->translationValueToFormTransformer->reverseTransform($translations);

        $this->translationValueSaver->saveTranslationValue($translationValue);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
