<?php

namespace App\Models\Feedback;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Feedback\Feedback
 *
 * @property int $id
 * @property string $company
 * @property string|null $email
 * @property string|null $consultation
 * @property string|null $expert
 * @property int $type
 * @property string|null $viewed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Message> $messages
 * @property-read int|null $messages_count
 *
 * @method static Builder|Feedback newModelQuery()
 * @method static Builder|Feedback newQuery()
 * @method static Builder|Feedback query()
 * @method static Builder|Feedback whereCompany($value)
 * @method static Builder|Feedback whereConsultation($value)
 * @method static Builder|Feedback whereCreatedAt($value)
 * @method static Builder|Feedback whereEmail($value)
 * @method static Builder|Feedback whereExpert($value)
 * @method static Builder|Feedback whereId($value)
 * @method static Builder|Feedback whereType($value)
 * @method static Builder|Feedback whereUpdatedAt($value)
 * @method static Builder|Feedback whereViewedAt($value)
 *
 * @mixin \Eloquent
 */
class Feedback extends Model
{
    final public const TYPE_POSITIVE = 1;

    final public const TYPE_NEGATIVE = 2;

    protected $connection = 'mysql_feedback';

    protected $table = 'feedback';

    protected $fillable = ['viewed_at'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Feedback $feedback): void {
            $feedback->messages()->delete();
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
