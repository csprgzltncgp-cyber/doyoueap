<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Riport
 *
 * @property int $id
 * @property Carbon $from
 * @property Carbon $to
 * @property int $company_id
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Collection<int, RiportValue> $values
 * @property-read int|null $values_count
 *
 * @method static Builder|Riport newModelQuery()
 * @method static Builder|Riport newQuery()
 * @method static Builder|Riport query()
 * @method static Builder|Riport whereCompanyId($value)
 * @method static Builder|Riport whereCreatedAt($value)
 * @method static Builder|Riport whereFrom($value)
 * @method static Builder|Riport whereId($value)
 * @method static Builder|Riport whereIsActive($value)
 * @method static Builder|Riport whereTo($value)
 * @method static Builder|Riport whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Riport extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'from' => 'date',
        'to' => 'date',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class)->withOutGlobalScopes();
    }

    public function values(): HasMany
    {
        return $this->hasMany(RiportValue::class, 'riport_id');
    }
}
