<?php

namespace App\Models\Catalog;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Database column limits: `name` varchar(255); `description`, `history`, `detail` text.
 * Public/admin forms cap `name` at 200 chars — treat that as the supported UX limit unless rules change.
 */
class ItemTranslation extends Model
{
    protected $fillable = [
        'item_id',
        'language_id',
        'name',
        'description',
        'history',
        'detail',
    ];

    protected $table = 'item_translations';

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
