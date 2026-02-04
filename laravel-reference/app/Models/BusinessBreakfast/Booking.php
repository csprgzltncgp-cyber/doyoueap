<?php

namespace App\Models\BusinessBreakfast;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\BusinessBreakfast\Booking
 *
 * @property int $id
 * @property int $event_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $email
 * @property string|null $phone_number
 * @property bool $newsletter
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Event $event
 *
 * @method static Builder|Booking newModelQuery()
 * @method static Builder|Booking newQuery()
 * @method static Builder|Booking query()
 * @method static Builder|Booking whereCreatedAt($value)
 * @method static Builder|Booking whereEmail($value)
 * @method static Builder|Booking whereEventId($value)
 * @method static Builder|Booking whereFirstName($value)
 * @method static Builder|Booking whereId($value)
 * @method static Builder|Booking whereLastName($value)
 * @method static Builder|Booking whereNewsletter($value)
 * @method static Builder|Booking wherePhoneNumber($value)
 * @method static Builder|Booking whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Booking extends Model
{
    protected $connection = 'mysql_business_breakfast';

    protected $fillable = [];

    protected $casts = [
        'newsletter' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
