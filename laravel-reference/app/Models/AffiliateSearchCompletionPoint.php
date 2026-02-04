<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\AffiliateSearchCompletionPoint
 *
 * @property int $id
 * @property int $user_id
 * @property int $affiliate_search_id
 * @property int $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static Builder|AffiliateSearchCompletionPoint newModelQuery()
 * @method static Builder|AffiliateSearchCompletionPoint newQuery()
 * @method static Builder|AffiliateSearchCompletionPoint query()
 * @method static Builder|AffiliateSearchCompletionPoint whereAffiliateSearchId($value)
 * @method static Builder|AffiliateSearchCompletionPoint whereCreatedAt($value)
 * @method static Builder|AffiliateSearchCompletionPoint whereId($value)
 * @method static Builder|AffiliateSearchCompletionPoint whereType($value)
 * @method static Builder|AffiliateSearchCompletionPoint whereUpdatedAt($value)
 * @method static Builder|AffiliateSearchCompletionPoint whereUserId($value)
 *
 * @mixin \Eloquent
 */
class AffiliateSearchCompletionPoint extends Model
{
    final public const TYPE_OVER_DEADLINE = 1;

    final public const TYPE_LAST_DAY = 2;

    final public const TYPE_WITHIN_DEADLINE = 3;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
