<?php

declare(strict_types=1);

namespace Locastic\SymfonyTranslationBundle\Utils;

use Locastic\SymfonyTranslationBundle\Model\SearchTranslation;
use Locastic\SymfonyTranslationBundle\Model\TranslationInterface;
use Locastic\SymfonyTranslationBundle\Provider\TranslationsProviderInterface;
use Locastic\SymfonyTranslationBundle\Transformer\TranslationKeyToTranslationTransformerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class SearchTranslationsUtils implements SearchTranslationsUtilsInterface
{
    public function __construct(
        private TranslationsProviderInterface $translationsProvider,
        private TranslationKeyToTranslationTransformerInterface $translationTransformer,
        private string $localeCode,
        private array $locales,
    ) {
    }

    public function searchTranslationsFromRequest(
        Request $request,
        SearchTranslation $search,
        FormInterface $searchForm,
    ): PagerfantaInterface {
        $translations = $this->translationsProvider->getTranslations($this->localeCode, $this->locales);
        $translations = $this->translationsProvider->defineAllKeys($translations, $this->locales);
        $translations = $this->translationTransformer->transformMultiple($translations);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $translations = array_filter($translations, static function (TranslationInterface $translation) use ($search): bool {
                if (false !== mb_strpos((string) $translation->getKey(), (string) $search->getSearch())) {
                    return true;
                }
                foreach ($translation->getValues() as $translationValue) {
                    if (false !== mb_strpos((string) $translationValue->getValue(), (string) $search->getSearch())) {
                        return true;
                    }
                }

                return false;
            });
        }

        $adapter = new ArrayAdapter($translations);
        $pagerFanta = new Pagerfanta($adapter);

        if (null !== $search->getSearch() && $pagerFanta->getNbResults() > 0) {
            $pagerFanta->setMaxPerPage($pagerFanta->getNbResults());
            $request->query->remove('page');
        } else {
            $pagerFanta->setMaxPerPage(50);
        }
        $pagerFanta->setCurrentPage($request->query->getInt('page', 1));

        return $pagerFanta;
    }
}
