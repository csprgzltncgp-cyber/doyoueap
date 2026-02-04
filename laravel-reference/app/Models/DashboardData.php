<?php

namespace App\Models;

use App\Enums\DashboardDataType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DashboardData
 *
 * @property int $id
 * @property string $type
 * @property array $data
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static Builder|DashboardData newModelQuery()
 * @method static Builder|DashboardData newQuery()
 * @method static Builder|DashboardData query()
 * @method static Builder|DashboardData whereCreatedAt($value)
 * @method static Builder|DashboardData whereData($value)
 * @method static Builder|DashboardData whereId($value)
 * @method static Builder|DashboardData whereType($value)
 * @method static Builder|DashboardData whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class DashboardData extends Model
{
    protected $fillable = ['type', 'data'];

    protected $table = 'dashboard_datas';

    protected $casts = [
        'type' => DashboardDataType::class,
        'data' => 'array',
    ];
}
