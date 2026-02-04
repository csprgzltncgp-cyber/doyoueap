<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\OtherActivityEvent
 *
 * @property int $id
 * @property int $other_activity_id
 * @property int $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read OtherActivity|null $other_activity
 *
 * @method static Builder|OtherActivityEvent newModelQuery()
 * @method static Builder|OtherActivityEvent newQuery()
 * @method static Builder|OtherActivityEvent query()
 * @method static Builder|OtherActivityEvent whereCreatedAt($value)
 * @method static Builder|OtherActivityEvent whereId($value)
 * @method static Builder|OtherActivityEvent whereOtherActivityId($value)
 * @method static Builder|OtherActivityEvent whereType($value)
 * @method static Builder|OtherActivityEvent whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OtherActivityEvent extends Model
{
    final public const TYPE_OTHER_ACTIVITY_PRICE_MODIFIED_BY_ADMIN = 1;

    final public const TYPE_OTHER_ACTIVITY_PRICE_MODIFIED_BY_EXPERT = 2;

    final public const TYPE_OTHER_ACTIVITY_ACCEPTED_BY_ADMIN = 3;

    final public const TYPE_OTHER_ACTIVITY_DENIED_BY_EXPERT = 4;

    protected $guarded = [];

    protected $table = 'other_activity_events';

    public function other_activity(): BelongsTo
    {
        return $this->belongsTo(OtherActivity::class);
    }
}
