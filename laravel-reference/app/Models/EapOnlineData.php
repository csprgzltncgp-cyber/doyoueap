<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnlineData
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $description
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static Builder|EapOnlineData newModelQuery()
 * @method static Builder|EapOnlineData newQuery()
 * @method static Builder|EapOnlineData query()
 * @method static Builder|EapOnlineData whereCreatedAt($value)
 * @method static Builder|EapOnlineData whereDescription($value)
 * @method static Builder|EapOnlineData whereId($value)
 * @method static Builder|EapOnlineData whereImage($value)
 * @method static Builder|EapOnlineData whereUpdatedAt($value)
 * @method static Builder|EapOnlineData whereUserId($value)
 *
 * @mixin \Eloquent
 */
class EapOnlineData extends Model
{
    protected $table = 'eap_online_datas';

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
