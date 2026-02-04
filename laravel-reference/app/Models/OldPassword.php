<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\OldPassword
 *
 * @property int $id
 * @property int $user_id
 * @property string $password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|OldPassword newModelQuery()
 * @method static Builder|OldPassword newQuery()
 * @method static Builder|OldPassword onlyTrashed()
 * @method static Builder|OldPassword query()
 * @method static Builder|OldPassword whereCreatedAt($value)
 * @method static Builder|OldPassword whereDeletedAt($value)
 * @method static Builder|OldPassword whereId($value)
 * @method static Builder|OldPassword whereLanguageId($value)
 * @method static Builder|OldPassword whereTranslatableId($value)
 * @method static Builder|OldPassword whereTranslatableType($value)
 * @method static Builder|OldPassword whereUpdatedAt($value)
 * @method static Builder|OldPassword whereValue($value)
 * @method static Builder|OldPassword withTrashed()
 * @method static Builder|OldPassword withoutTrashed()
 *
 * @mixin \Eloquent
 */
class OldPassword extends Model
{
    protected $fillable = [
        'user_id',
        'password',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
