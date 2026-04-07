<?php

namespace App\Models\Taxonomy;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagCategoryTranslation extends Model
{
    protected $fillable = [
        'tag_category_id',
        'language_id',
        'name',
    ];

    protected $table = 'tag_category_translations';

    public function tagCategory(): BelongsTo
    {
        return $this->belongsTo(TagCategory::class, 'tag_category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
