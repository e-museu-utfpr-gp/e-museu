<?php

namespace App\Models\Catalog;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraTranslation extends Model
{
    protected $fillable = [
        'extra_id',
        'language_id',
        'info',
    ];

    protected $table = 'extra_translations';

    public function extra(): BelongsTo
    {
        return $this->belongsTo(Extra::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
