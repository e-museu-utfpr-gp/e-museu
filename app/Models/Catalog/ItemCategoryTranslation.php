<?php

namespace App\Models\Catalog;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemCategoryTranslation extends Model
{
    protected $fillable = [
        'item_category_id',
        'language_id',
        'name',
    ];

    protected $table = 'item_category_translations';

    public function itemCategory(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
