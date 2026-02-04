<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\AffiliateSearchAttachment
 *
 * @property int $id
 * @property int $affiliate_search_id
 * @property string $filename
 * @property string $path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AffiliateSearch|null $affiliate_search
 *
 * @method static Builder|AffiliateSearchAttachment newModelQuery()
 * @method static Builder|AffiliateSearchAttachment newQuery()
 * @method static Builder|AffiliateSearchAttachment query()
 * @method static Builder|AffiliateSearchAttachment whereAffiliateSearchId($value)
 * @method static Builder|AffiliateSearchAttachment whereCreatedAt($value)
 * @method static Builder|AffiliateSearchAttachment whereFilename($value)
 * @method static Builder|AffiliateSearchAttachment whereId($value)
 * @method static Builder|AffiliateSearchAttachment wherePath($value)
 * @method static Builder|AffiliateSearchAttachment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AffiliateSearchAttachment extends Model
{
    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();
        static::deleting(function ($attachment): void {
            if (file_exists($attachment->path)) {
                unlink($attachment->path);
            }
        });
    }

    public function affiliate_search(): BelongsTo
    {
        return $this->belongsTo(AffiliateSearch::class);
    }
}
