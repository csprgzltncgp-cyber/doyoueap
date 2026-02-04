<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\OpenInvoicing
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon $until
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static Builder|OpenInvoicing newModelQuery()
 * @method static Builder|OpenInvoicing newQuery()
 * @method static Builder|OpenInvoicing query()
 * @method static Builder|OpenInvoicing whereCreatedAt($value)
 * @method static Builder|OpenInvoicing whereId($value)
 * @method static Builder|OpenInvoicing whereUntil($value)
 * @method static Builder|OpenInvoicing whereUpdatedAt($value)
 * @method static Builder|OpenInvoicing whereUserId($value)
 *
 * @mixin \Eloquent
 */
class OpenInvoicing extends Model
{
    protected $guarded = [];

    protected $casts = [
        'until' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
