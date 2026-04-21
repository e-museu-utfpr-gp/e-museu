<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

/**
 * Physical or organizational site where catalog items are located (seeded reference data).
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property-read string $localized_label UI label from {@see $code} via `app.catalog.location.codes.*`
 */
class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * @param  string  $value
     */
    public function setCodeAttribute($value): void
    {
        $this->attributes['code'] = strtoupper(trim((string) $value));
    }

    /**
     * @return Attribute<string, never>
     */
    protected function localizedLabel(): Attribute
    {
        return Attribute::get(function (): string {
            $key = 'app.catalog.location.codes.' . $this->code;
            if (Lang::has($key)) {
                return __($key);
            }

            return $this->name;
        });
    }
}
