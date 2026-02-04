<?php

namespace App\Models\BusinessBreakfast;

use App\Enums\BusinessBreakfast\InteractionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\BusinessBreakfast\Interaction
 *
 * @property int $id
 * @property int $event_id
 * @property InteractionType $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Event $event
 *
 * @method static Builder|Interaction newModelQuery()
 * @method static Builder|Interaction newQuery()
 * @method static Builder|Interaction query()
 * @method static Builder|Interaction whereCreatedAt($value)
 * @method static Builder|Interaction whereEventId($value)
 * @method static Builder|Interaction whereId($value)
 * @method static Builder|Interaction whereType($value)
 * @method static Builder|Interaction whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Interaction extends Model
{
    protected $connection = 'mysql_business_breakfast';

    protected $fillable = [];

    protected $casts = [
        'type' => InteractionType::class,
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
