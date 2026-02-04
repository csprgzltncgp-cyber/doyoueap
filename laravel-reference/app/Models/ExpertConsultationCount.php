<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\ExpertConsultationCount
 *
 * @property int $id
 * @property int $user_id
 * @property int $count
 * @property Carbon $month
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|ExpertCurrencyChange newModelQuery()
 * @method static Builder|ExpertCurrencyChange newQuery()
 * @method static Builder|ExpertCurrencyChange query()
 * @method static Builder|ExpertCurrencyChange whereId($value)
 * @method static Builder|ExpertCurrencyChange whereUserId($value)
 * @method static Builder|ExpertCurrencyChange whereMonth($value)
 *
 * @mixin Eloquent
 */
class ExpertConsultationCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'count',
        'month',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
