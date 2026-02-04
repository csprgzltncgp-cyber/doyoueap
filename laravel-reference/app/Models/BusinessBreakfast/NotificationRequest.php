<?php

namespace App\Models\BusinessBreakfast;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\BusinessBreakfast\NotificationRequest
 *
 * @property int $id
 * @property int $event_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $email
 * @property string|null $phone_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Event $event
 *
 * @method static Builder|NotificationRequest newModelQuery()
 * @method static Builder|NotificationRequest newQuery()
 * @method static Builder|NotificationRequest query()
 * @method static Builder|NotificationRequest whereCreatedAt($value)
 * @method static Builder|NotificationRequest whereEmail($value)
 * @method static Builder|NotificationRequest whereEventId($value)
 * @method static Builder|NotificationRequest whereFirstName($value)
 * @method static Builder|NotificationRequest whereId($value)
 * @method static Builder|NotificationRequest whereLastName($value)
 * @method static Builder|NotificationRequest wherePhoneNumber($value)
 * @method static Builder|NotificationRequest whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class NotificationRequest extends Model
{
    protected $connection = 'mysql_business_breakfast';

    protected $fillable = [];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
