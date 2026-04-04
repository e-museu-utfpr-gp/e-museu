<?php

namespace App\Models\Taxonomy;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagTranslation extends Model
{
    protected $fillable = [
        'tag_id',
        'language_id',
        'name',
    ];

    protected $table = 'tag_translations';

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
