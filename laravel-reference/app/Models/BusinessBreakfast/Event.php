<?php

namespace App\Models\BusinessBreakfast;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\BusinessBreakfast\Event
 *
 * @property int $id
 * @property string $name
 * @property string $language
 * @property string $location
 * @property Carbon $date
 * @property string $duration
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read Collection<int, Interaction> $interactions
 * @property-read int|null $interactions_count
 * @property-read Collection<int, NotificationRequest> $notification_requests
 * @property-read int|null $notification_requests_count
 *
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereDate($value)
 * @method static Builder|Event whereDuration($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereLanguage($value)
 * @method static Builder|Event whereLocation($value)
 * @method static Builder|Event whereName($value)
 * @method static Builder|Event whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Event extends Model
{
    protected $connection = 'mysql_business_breakfast';

    protected $fillable = [];

    protected $casts = [
        'is_current' => 'boolean',
        'date' => 'date',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    public function notification_requests(): HasMany
    {
        return $this->hasMany(NotificationRequest::class);
    }
}
