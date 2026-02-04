<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapChapter
 *
 * @property int $id
 * @property string $chapterable_type
 * @property int $chapterable_id
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|\Eloquent $chapterable
 *
 * @method static Builder|EapChapter newModelQuery()
 * @method static Builder|EapChapter newQuery()
 * @method static Builder|EapChapter query()
 * @method static Builder|EapChapter whereCreatedAt($value)
 * @method static Builder|EapChapter whereId($value)
 * @method static Builder|EapChapter whereChapterableId($value)
 * @method static Builder|EapChapter whereChapterableType($value)
 * @method static Builder|EapChapter whereUpdatedAt($value)
 * @method static Builder|EapChapter whereValue($value)
 *
 * @mixin \Eloquent
 */
class EapChapter extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'chapters';

    protected $guarded = [];

    public function chapterable(): MorphTo
    {
        return $this->morphTo();
    }
}
