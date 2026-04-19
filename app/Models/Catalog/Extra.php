<?php

namespace App\Models\Catalog;

use App\Models\Language;
use App\Models\Collaborator\Collaborator;
use App\Models\Identity\Lock;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Support\Database\SqlExpr;
use App\Support\Content\{ResolvedTranslation, TranslationDisplaySql, TranslationResolution};
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, MorphMany};

class Extra extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'collaborator_id',
        'validation',
    ];

    protected $table = 'extras';

    protected $casts = [
        'validation' => 'boolean',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(ExtraTranslation::class, 'extra_id')
            ->orderBy('language_id')
            ->orderBy('id');
    }

    /**
     * @param  array{info: string}  $fields
     */
    public function syncPrimaryLocaleTranslation(array $fields): void
    {
        $this->syncTranslationForLanguage(Language::idForPreferredFormLocale(), $fields);
    }

    /**
     * @param  array{info: string}  $fields
     */
    public function syncTranslationForLanguage(int $languageId, array $fields): void
    {
        $this->translations()->updateOrCreate(
            ['language_id' => $languageId],
            $fields
        );
    }

    /**
     * @param  array<string, array<string, mixed>|null>  $translationsByCode
     */
    public function syncTranslationsFromAdminForm(array $translationsByCode): void
    {
        foreach (Language::forCatalogContentForms() as $lang) {
            $code = $lang->code;
            $block = $translationsByCode[$code] ?? [];
            $info = trim((string) ($block['info'] ?? ''));
            if ($info === '') {
                $this->translations()->where('language_id', $lang->id)->delete();

                continue;
            }

            $this->translations()->updateOrCreate(
                ['language_id' => $lang->id],
                ['info' => $info]
            );
        }

        if ($this->relationLoaded('translations')) {
            $this->unsetRelation('translations');
        }
    }

    public function resolveTranslation(): ResolvedTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        return TranslationResolution::fromCollection($this->translations);
    }

    public function resolvedTranslation(): ?ExtraTranslation
    {
        $t = $this->resolveTranslation()->translation;

        return $t instanceof ExtraTranslation ? $t : null;
    }

    /**
     * @param  Builder<Extra>  $query
     * @return Builder<Extra>
     */
    public function scopeForAdminList(Builder $query): Builder
    {
        $infoSql = TranslationDisplaySql::extraInfoSubquerySql('extras');
        $itemNameSql = TranslationDisplaySql::itemNameSubquerySql('items');

        $query->leftJoin('collaborators', 'extras.collaborator_id', '=', 'collaborators.id')
            ->leftJoin('items', 'extras.item_id', '=', 'items.id')
            ->select([
                'extras.id',
                SqlExpr::raw("({$infoSql}) AS info"),
                'extras.validation AS extra_validation',
                'extras.created_at AS extra_created',
                'extras.updated_at AS extra_updated',
                'extras.item_id',
                'extras.collaborator_id',
                SqlExpr::raw("({$itemNameSql}) AS item_name"),
                'collaborators.email AS collaborator_email',
            ]);

        return $query;
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(Collaborator::class);
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }

    /**
     * @return Attribute<string, never>
     */
    protected function info(): Attribute
    {
        return Attribute::get(function (): string {
            if (array_key_exists('info', $this->attributes) && $this->attributes['info'] !== null) {
                return (string) $this->attributes['info'];
            }

            return (string) ($this->resolvedTranslation()?->info ?? '');
        });
    }
}
