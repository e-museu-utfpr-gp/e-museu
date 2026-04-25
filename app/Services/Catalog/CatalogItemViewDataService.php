<?php

declare(strict_types=1);

namespace App\Services\Catalog;

use App\Enums\Content\ContentLanguage;
use App\Models\Language;
use App\Services\Taxonomy\TagCategoryService;
use App\Services\LocationService;
use App\Support\Content\ContentLocaleFallback;
use Illuminate\Http\Request;

/**
 * View payloads for public catalog item index, create, and show.
 *
 * Keeps {@see \App\Http\Controllers\Catalog\ItemController} coupling low for PHPMD.
 */
final class CatalogItemViewDataService
{
    /**
     * @var array{
     *     contributionLanguages: \Illuminate\Database\Eloquent\Collection<int, Language>,
     *     defaultContentLocale: string
     * }|null
     */
    private ?array $localeFormOptionsCache = null;

    public function __construct(
        private readonly ItemCategoryService $itemCategoryService,
        private readonly TagCategoryService $tagCategoryService,
        private readonly ItemService $itemService,
        private readonly LocationService $locationService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function forIndex(Request $request): array
    {
        $data = $this->itemService->getPaginatedItemsForCatalogIndex($request);
        $itemCategories = $this->itemCategoryService->getForIndex();
        $categories = $this->tagCategoryService->getForIndex();

        return [
            'items' => $data['items'],
            'categoryName' => $data['categoryName'],
            'itemCategories' => $itemCategories,
            'categories' => $categories,
            'locations' => $this->locationService->orderedForForms(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forCreate(): array
    {
        $categories = $this->tagCategoryService->getForIndex();
        $itemCategories = $this->itemCategoryService->getForIndex();
        $localeForm = $this->localeFormOptions();
        $locationForm = $this->locationService->forItemCreateForms();

        return [
            'categories' => $categories,
            'itemCategories' => $itemCategories,
            'contributionLanguages' => $localeForm['contributionLanguages'],
            'defaultContributionContentLocale' => $localeForm['defaultContentLocale'],
            'locations' => $locationForm['locations'],
            'defaultCatalogLocationId' => $locationForm['defaultCatalogLocationId'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forShow(string $id): array
    {
        $item = $this->itemService->getPublicItemForShow($id);

        $itemCategories = $this->itemCategoryService->getForIndex();
        $categories = $this->tagCategoryService->getForIndex();

        $seriesCategoryId = $item->loadCatalogSeriesTimelineForShow();

        $localeForm = $this->localeFormOptions();

        return [
            'item' => $item,
            'itemCategories' => $itemCategories,
            'categories' => $categories,
            'seriesCategoryId' => $seriesCategoryId,
            'contributionLanguages' => $localeForm['contributionLanguages'],
            'defaultExtraContentLocale' => $localeForm['defaultContentLocale'],
        ];
    }

    /**
     * @return array{
     *     contributionLanguages: \Illuminate\Database\Eloquent\Collection<int, Language>,
     *     defaultContentLocale: string
     * }
     */
    private function localeFormOptions(): array
    {
        if ($this->localeFormOptionsCache !== null) {
            return $this->localeFormOptionsCache;
        }

        $contributionLanguages = Language::forCatalogContentForms();
        $defaultContentLocale = ContentLocaleFallback::normalizedAppLocaleCode();
        if (Language::tryIdForCode($defaultContentLocale) === null) {
            $defaultContentLocale = ContentLanguage::defaultForForms()->value;
        }

        $this->localeFormOptionsCache = [
            'contributionLanguages' => $contributionLanguages,
            'defaultContentLocale' => $defaultContentLocale,
        ];

        return $this->localeFormOptionsCache;
    }
}
