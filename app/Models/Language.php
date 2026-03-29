<?php

namespace App\Models;

use App\Enums\Content\ContentLanguage;
use App\Support\Content\ContentLocaleFallback;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $code Unique locale key (matches {@see ContentLanguage} values in use).
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
     * @return HasMany<\App\Models\Catalog\ItemTranslation, $this>
     */
    public function itemTranslations(): HasMany
    {
        return $this->hasMany(\App\Models\Catalog\ItemTranslation::class, 'language_id');
    }
}
