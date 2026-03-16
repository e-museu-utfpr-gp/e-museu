<?php

namespace App\Models\Catalog;

use App\Models\Identity\Lock;
use App\Models\Collaborator\Collaborator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Extra extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'collaborator_id',
        'info',
        'validation',
    ];

    protected $table = 'extras';

    /**
     * Scope for admin index list: joins items and collaborators, selects columns with aliases for the index view.
     *
     * @param  Builder<Extra>  $query
     * @return Builder<Extra>
     */
    public function scopeForAdminList(Builder $query): Builder
    {
        $query->leftJoin('collaborators', 'extras.collaborator_id', '=', 'collaborators.id')
            ->leftJoin('items', 'extras.item_id', '=', 'items.id')
            ->select([
                'extras.id',
                'extras.info',
                'extras.validation AS extra_validation',
                'extras.created_at AS extra_created',
                'extras.updated_at AS extra_updated',
                'extras.item_id',
                'extras.collaborator_id',
                'items.name AS item_name',
                'collaborators.contact AS collaborator_contact',
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
}
