<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapLesson
 *
 * @property int $id
 * @property string $lessonable_type
 * @property int $lessonable_id
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|\Eloquent $lessonable
 *
 * @method static Builder|EapLesson newModelQuery()
 * @method static Builder|EapLesson newQuery()
 * @method static Builder|EapLesson query()
 * @method static Builder|EapLesson whereCreatedAt($value)
 * @method static Builder|EapLesson whereId($value)
 * @method static Builder|EapLesson whereLessonableId($value)
 * @method static Builder|EapLesson whereLessonableType($value)
 * @method static Builder|EapLesson whereUpdatedAt($value)
 * @method static Builder|EapLesson whereValue($value)
 *
 * @mixin \Eloquent
 */
class EapLesson extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'lessons';

    protected $guarded = [];

    public function lessonable(): MorphTo
    {
        return $this->morphTo();
    }
}
