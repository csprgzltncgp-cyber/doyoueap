<?php

namespace App\Models\EapOnline;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapRiport
 *
 * @property int $id
 * @property Carbon $from
 * @property Carbon $to
 * @property int $company_id
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Collection<int, EapRiportValue> $eap_riport_values
 * @property-read int|null $eap_riport_values_count
 *
 * @method static Builder|EapRiport newModelQuery()
 * @method static Builder|EapRiport newQuery()
 * @method static Builder|EapRiport query()
 * @method static Builder|EapRiport whereCompanyId($value)
 * @method static Builder|EapRiport whereCreatedAt($value)
 * @method static Builder|EapRiport whereFrom($value)
 * @method static Builder|EapRiport whereId($value)
 * @method static Builder|EapRiport whereIsActive($value)
 * @method static Builder|EapRiport whereTo($value)
 * @method static Builder|EapRiport whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapRiport extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'riports';

    protected $guarded = [];

    protected $casts = [
        'from' => 'date',
        'to' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $riport): void {
            $riport->is_active = false;
        });
    }

    public function company(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Company::class)->withoutGlobalScopes();
    }

    public function eap_riport_values(): HasMany
    {
        return $this->setConnection('mysql_eap_online')->hasMany(EapRiportValue::class, 'riport_id', 'id');
    }
}
