<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapRiportValue
 *
 * @property int $id
 * @property int $riport_id
 * @property int $country_id
 * @property string $statistics
 * @property int|null $statistics_type
 * @property int|null $statistics_subtype
 * @property int $count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EapRiport|null $eap_riport
 * @property-read mixed $current_count
 *
 * @method static Builder|EapRiportValue newModelQuery()
 * @method static Builder|EapRiportValue newQuery()
 * @method static Builder|EapRiportValue query()
 * @method static Builder|EapRiportValue whereCount($value)
 * @method static Builder|EapRiportValue whereCountryId($value)
 * @method static Builder|EapRiportValue whereCreatedAt($value)
 * @method static Builder|EapRiportValue whereId($value)
 * @method static Builder|EapRiportValue whereRiportId($value)
 * @method static Builder|EapRiportValue whereStatistics($value)
 * @method static Builder|EapRiportValue whereStatisticsSubtype($value)
 * @method static Builder|EapRiportValue whereStatisticsType($value)
 * @method static Builder|EapRiportValue whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapRiportValue extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'riport_values';

    protected $guarded = [];

    protected $appends = ['current_count'];

    protected $casts = [
        'statistics_type' => 'integer',
        'statistics_subtype' => 'integer',
        'count' => 'integer',
    ];

    public function getCurrentCountAttribute()
    {
        return $this->count;
    }

    public function eap_riport(): BelongsTo
    {
        return $this->belongsTo(EapRiport::class);
    }
}
