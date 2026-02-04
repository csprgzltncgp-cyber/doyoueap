<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\AffiliateSearch
 *
 * @property int $id
 * @property string $description
 * @property int $from_id
 * @property int $to_id
 * @property int $country_id
 * @property int|null $city_id
 * @property int $permission_id
 * @property int $status
 * @property int $deadline_type
 * @property string $deadline
 * @property int $completed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Permission|null $affiliate_type
 * @property-read Collection<int, AffiliateSearchAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read City|null $city
 * @property-read Collection<int, AffiliateSearchComment> $comments
 * @property-read int|null $comments_count
 * @property-read Collection<int, User> $connected_users
 * @property-read int|null $connected_users_count
 * @property-read Country|null $country
 * @property-read User|null $from
 * @property-read User|null $to
 *
 * @method static Builder|AffiliateSearch newModelQuery()
 * @method static Builder|AffiliateSearch newQuery()
 * @method static Builder|AffiliateSearch query()
 * @method static Builder|AffiliateSearch whereCityId($value)
 * @method static Builder|AffiliateSearch whereCompleted($value)
 * @method static Builder|AffiliateSearch whereCountryId($value)
 * @method static Builder|AffiliateSearch whereCreatedAt($value)
 * @method static Builder|AffiliateSearch whereDeadline($value)
 * @method static Builder|AffiliateSearch whereDeadlineType($value)
 * @method static Builder|AffiliateSearch whereDescription($value)
 * @method static Builder|AffiliateSearch whereFromId($value)
 * @method static Builder|AffiliateSearch whereId($value)
 * @method static Builder|AffiliateSearch wherePermissionId($value)
 * @method static Builder|AffiliateSearch whereStatus($value)
 * @method static Builder|AffiliateSearch whereToId($value)
 * @method static Builder|AffiliateSearch whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AffiliateSearch extends Model
{
    final public const STATUS_CREATED = 1;

    final public const STATUS_OPENED = 2;

    final public const STATUS_SEARCH_STARTED = 3;

    final public const STATUS_AFFILIATE_FOUND = 4;

    final public const STATUS_AFFILIATE_CONTACTED = 5;

    final public const STATUS_CONTRACT_SENT = 6;

    final public const STATUS_CONTRACT_SIGNED = 7;

    final public const STATUS_ACTIVE_ON_DASBOARD = 8;

    final public const STATUS_COMPLETED = 9;

    final public const DEADLINE_TYPE_SOS = 1;

    final public const DEADLINE_TYPE_WITHIN_A_WEEK = 2;

    final public const DEADLINE_TYPE_WITHIN_TWO_WEEKS = 3;

    final public const DEADLINE_TYPE_SELECT_DATE = 4;

    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();
        static::deleting(function ($affiliateSearch): void {
            foreach ($affiliateSearch->comments as $comment) {
                $comment->delete();
            }

            foreach ($affiliateSearch->attachments as $attachment) {
                $attachment->delete();
            }
        });
    }

    public function from(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_id', 'id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function affiliate_type(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(AffiliateSearchComment::class, 'affiliate_search_id', 'id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AffiliateSearchAttachment::class, 'affiliate_search_id', 'id');
    }

    public function connected_users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'affiliate_search_users', 'affiliate_search_id', 'user_id');
    }

    public function has_new_comments(): bool
    {
        return $this->comments()->where('user_id', '!=', auth()->id())->where('seen', false)->count() > 0;
    }

    public function is_new(): bool
    {
        return $this->status == self::STATUS_CREATED;
    }

    public function is_over_deadline(): bool
    {
        return Carbon::parse($this->deadline)->isPast() && ! Carbon::parse($this->deadline)->isCurrentDay() && $this->status != self::STATUS_COMPLETED;
    }

    public function is_last_day(): bool
    {
        return Carbon::parse($this->deadline)->isCurrentDay() && $this->status != self::STATUS_COMPLETED;
    }
}
