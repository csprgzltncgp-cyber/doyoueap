<?php

namespace App\Models\PrizeGame;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PrizeGame\Image
 *
 * @property int $id
 * @property string $filename
 * @property int $content_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Content|null $content
 *
 * @method static Builder|Image newModelQuery()
 * @method static Builder|Image newQuery()
 * @method static Builder|Image query()
 * @method static Builder|Image whereContentId($value)
 * @method static Builder|Image whereCreatedAt($value)
 * @method static Builder|Image whereFilename($value)
 * @method static Builder|Image whereId($value)
 * @method static Builder|Image whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Image extends Model
{
    public $guarded = [];

    protected $connection = 'mysql_eap_online';

    protected $table = 'prizegame_images';

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
