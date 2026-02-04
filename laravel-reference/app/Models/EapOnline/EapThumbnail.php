<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapThumbnail
 *
 * @property int $id
 * @property string $filename
 * @property int $resource_id
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|EapThumbnail newModelQuery()
 * @method static Builder|EapThumbnail newQuery()
 * @method static Builder|EapThumbnail query()
 * @method static Builder|EapThumbnail type($value)
 * @method static Builder|EapThumbnail whereCreatedAt($value)
 * @method static Builder|EapThumbnail whereFilename($value)
 * @method static Builder|EapThumbnail whereId($value)
 * @method static Builder|EapThumbnail whereResourceId($value)
 * @method static Builder|EapThumbnail whereType($value)
 * @method static Builder|EapThumbnail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapThumbnail extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'thumbnails';

    protected $guarded = [];

    public function scopeType($query, $value)
    {
        return $query->where('type', $value);
    }
}
