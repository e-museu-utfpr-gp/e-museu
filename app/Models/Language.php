<?php

namespace App\Models;

    use App\Enums\Content\ContentLanguage;
    use App\Support\Content\ContentLocaleFallback;
    use Illuminate\Database\Eloquent\{Collection as EloquentCollection, Model};
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Support\Collection;
use RuntimeException;

/**
 * @property string $code Unique locale key (matches {@see ContentLanguage} values in use).
 *
 * **IDs by code:** {@see idForCode()} throws if the row is missing; id lookup is cached until a {@see Language}
 * save/delete clears it. For untrusted or optional input, use {@see tryIdForCode()}.
 * Keep `languages` seeds aligned with {@see ContentLanguage}.
 */
class Language extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    protected static function booted(): void
    {
        static::saved(static fn () => static::forgetIdCache());
        static::deleted(static fn () => static::forgetIdCache());
    }

    /**
     * @var array<string, int>
     */
    private static array $idByCodeCache = [];

    /**
     * @throws RuntimeException When no row exists for {@code $code}.
     */
    public static function idForCode(string $code): int
    {
        if (! isset(self::$idByCodeCache[$code])) {
            $id = static::query()->where('code', $code)->value('id');
            if ($id === null) {
                throw new RuntimeException("Unknown language code: {$code}");
            }
            self::$idByCodeCache[$code] = (int) $id;
        }

        return self::$idByCodeCache[$code];
    }

    public static function tryIdForCode(string $code): ?int
    {
        try {
            return self::idForCode($code);
        } catch (RuntimeException) {
            return null;
        }
    }

    /**
     * Language row for forms: current app locale when it exists in `languages`, else
     * {@see ContentLanguage::defaultForForms()}.
     */
    public static function idForPreferredFormLocale(): int
    {
        $id = self::tryIdForCode(ContentLocaleFallback::normalizedAppLocaleCode());
        if ($id !== null) {
            return $id;
        }

        return self::idForCode(ContentLanguage::defaultForForms()->value);
    }

    public static function forgetIdCache(): void
    {
        self::$idByCodeCache = [];
    }

    /**
     * Whether Laravel has a `lang/{code}/` directory so `__()` / `app()->setLocale()` work for this code.
     */
    public function hasUiTranslationPack(): bool
    {
        return is_dir(lang_path($this->code));
    }

    /**
     * Session locale is stored only for rows in `languages` that also have UI translation files.
     */
    public static function isValidSessionUiLocale(string $code): bool
    {
        $language = static::query()->where('code', $code)->first();

        return $language !== null && $language->hasUiTranslationPack();
    }

    /**
     * Languages listed in public/admin locale switchers (excludes neutral; ordered by display name).
     *
     * @return Collection<int, Language>
     */
    public static function forLocaleSwitcher(): Collection
    {
        return static::query()
            ->where('code', '!=', ContentLanguage::NEUTRAL->value)
            ->orderBy('name')
            ->get();
    }

    /**
     * Languages for translatable catalog content: admin create/edit forms and public contribution
     * (includes neutral). Order: neutral → pt_BR → en, then any other rows by name.
     *
     * @return EloquentCollection<int, Language>
     */
    public static function forCatalogContentForms(): EloquentCollection
    {
        $ordered = ContentLanguage::orderedCodesForAdminForms();
        $caseParts = [];
        $bindings = [];
        foreach ($ordered as $i => $code) {
            $caseParts[] = 'WHEN ? THEN ' . ($i + 1);
            $bindings[] = $code;
        }
        $caseSql = 'CASE code ' . implode(' ', $caseParts) . ' ELSE 99 END';

        /** @var list<Language> $rows */
        $rows = static::query()
            ->orderByRaw($caseSql . ', name', $bindings)
            ->get()
            ->all();

        return new EloquentCollection($rows);
    }

    /**
     * @return HasMany<\App\Models\Catalog\ItemTranslation, $this>
     */
    public function itemTranslations(): HasMany
    {
        return $this->hasMany(\App\Models\Catalog\ItemTranslation::class, 'language_id');
    }
}
