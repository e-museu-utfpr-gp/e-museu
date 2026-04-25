<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Content;

use App\Models\Catalog\ItemCategoryTranslation;
use App\Models\Language;
use App\Support\Content\TranslationResolution;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * {@see TranslationResolution::fromCollection()} with in-memory rows (no DB).
 */
final class TranslationResolutionFromCollectionTest extends TestCase
{
    /** Synthetic ids aligned with typical seed order (pt_BR before en). */
    private const int LANGUAGE_ID_PT_BR = 2;

    private const int LANGUAGE_ID_EN = 3;

    /** Arbitrary ids for custom locale codes outside the seeded set. */
    private const int LANGUAGE_ID_YY_CUSTOM = 98;

    private const int LANGUAGE_ID_ZZ_CUSTOM = 99;

    private string $savedAppLocale = '';

    private string $savedFallbackLocale = '';

    private string $savedRuntimeLocale = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->savedAppLocale = (string) config('app.locale');
        $this->savedFallbackLocale = (string) config('app.fallback_locale');
        $this->savedRuntimeLocale = app()->getLocale();
    }

    protected function tearDown(): void
    {
        config([
            'app.locale' => $this->savedAppLocale,
            'app.fallback_locale' => $this->savedFallbackLocale,
        ]);
        app()->setLocale($this->savedRuntimeLocale);

        parent::tearDown();
    }

    public function test_empty_collection_returns_empty_resolution(): void
    {
        $resolved = TranslationResolution::fromCollection(new Collection());

        $this->assertNull($resolved->translation);
        $this->assertNull($resolved->sourceLanguageCode);
        $this->assertFalse($resolved->isFromAppLocale);
    }

    public function test_prefers_row_matching_app_locale(): void
    {
        config(['app.locale' => 'en', 'app.fallback_locale' => 'pt_BR']);
        app()->setLocale('en');

        $langEn = new Language(['id' => self::LANGUAGE_ID_EN, 'code' => 'en']);
        $langPt = new Language(['id' => self::LANGUAGE_ID_PT_BR, 'code' => 'pt_BR']);
        $tEn = new ItemCategoryTranslation(['language_id' => self::LANGUAGE_ID_EN, 'id' => 2, 'name' => 'En']);
        $tEn->setRelation('language', $langEn);
        $tPt = new ItemCategoryTranslation(['language_id' => self::LANGUAGE_ID_PT_BR, 'id' => 1, 'name' => 'Pt']);
        $tPt->setRelation('language', $langPt);

        /** @var Collection<int, Model> $rows */
        $rows = new EloquentCollection([$tPt, $tEn]);
        $resolved = TranslationResolution::fromCollection($rows);

        $this->assertSame($tEn, $resolved->translation);
        $this->assertSame('en', $resolved->sourceLanguageCode);
        $this->assertTrue($resolved->isFromAppLocale);
    }

    public function test_falls_back_to_sorted_row_when_no_ordered_code_matches(): void
    {
        config(['app.locale' => 'en', 'app.fallback_locale' => 'pt_BR']);
        app()->setLocale('en');

        $langZz = new Language(['id' => self::LANGUAGE_ID_ZZ_CUSTOM, 'code' => 'zz_custom']);
        $tLater = new ItemCategoryTranslation([
            'language_id' => self::LANGUAGE_ID_ZZ_CUSTOM,
            'id' => 5,
            'name' => 'B',
        ]);
        $tLater->setRelation('language', $langZz);
        $langZz2 = new Language(['id' => self::LANGUAGE_ID_YY_CUSTOM, 'code' => 'yy_custom']);
        $tEarlier = new ItemCategoryTranslation([
            'language_id' => self::LANGUAGE_ID_YY_CUSTOM,
            'id' => 4,
            'name' => 'A',
        ]);
        $tEarlier->setRelation('language', $langZz2);

        /** @var Collection<int, Model> $rows */
        $rows = new EloquentCollection([$tLater, $tEarlier]);
        $resolved = TranslationResolution::fromCollection($rows);

        $this->assertSame($tEarlier, $resolved->translation);
        $this->assertSame('yy_custom', $resolved->sourceLanguageCode);
        $this->assertFalse($resolved->isFromAppLocale);
    }
}
