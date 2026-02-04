<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\CloseTelusCase
 *
 * @property int $id
 * @property int $case_id
 * @property Carbon|null $closeable_after
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|City newModelQuery()
 * @method static Builder|City newQuery()
 * @method static Builder|City onlyTrashed()
 * @method static Builder|City query()
 * @method static Builder|City whereCaseId($value)
 * @method static Builder|City whereCreatedAt($value)
 * @method static Builder|City whereUpdatedAt($value)
 * @method static Builder|City whereId($value)
 * @method static Builder|City whereCloseableAfter($value)
 *
 * @mixin \Eloquent
 */
class CloseTelusCase extends Model
{
    protected $fillable = [
        'case_id',
        'closeable_after',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(Cases::class);
    }
}
