<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\AffiliateSearchComment
 *
 * @property int $id
 * @property int $affiliate_search_id
 * @property int $user_id
 * @property string $value
 * @property bool $seen
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AffiliateSearch|null $affiliate_search
 * @property-read User|null $user
 *
 * @method static Builder|AffiliateSearchComment newModelQuery()
 * @method static Builder|AffiliateSearchComment newQuery()
 * @method static Builder|AffiliateSearchComment query()
 * @method static Builder|AffiliateSearchComment whereAffiliateSearchId($value)
 * @method static Builder|AffiliateSearchComment whereCreatedAt($value)
 * @method static Builder|AffiliateSearchComment whereId($value)
 * @method static Builder|AffiliateSearchComment whereSeen($value)
 * @method static Builder|AffiliateSearchComment whereUpdatedAt($value)
 * @method static Builder|AffiliateSearchComment whereUserId($value)
 * @method static Builder|AffiliateSearchComment whereValue($value)
 *
 * @mixin \Eloquent
 */
class AffiliateSearchComment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'seen' => 'boolean',
    ];

    public function affiliate_search(): BelongsTo
    {
        return $this->belongsTo(AffiliateSearch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function is_from_creator(): bool
    {
        return $this->user->id == $this->affiliate_search->from_id;
    }
}
