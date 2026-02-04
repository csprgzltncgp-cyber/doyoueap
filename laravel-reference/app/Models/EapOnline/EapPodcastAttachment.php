<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\EapOnline\EapPodcastAttachment
 *
 * @property int $id
 * @property int $podcast_id
 * @property string $filename
 * @property string $button_text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EapPodcast|null $eap_podcast
 *
 * @method static Builder|EapPodcastAttachment newModelQuery()
 * @method static Builder|EapPodcastAttachment newQuery()
 * @method static Builder|EapPodcastAttachment query()
 * @method static Builder|EapPodcastAttachment whereButtonText($value)
 * @method static Builder|EapPodcastAttachment whereCreatedAt($value)
 * @method static Builder|EapPodcastAttachment whereFilename($value)
 * @method static Builder|EapPodcastAttachment whereId($value)
 * @method static Builder|EapPodcastAttachment wherePodcastId($value)
 * @method static Builder|EapPodcastAttachment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapPodcastAttachment extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'podcast_attachments';

    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function ($attachment): void {
            Storage::delete('eap-online/podcast-attachments/'.$attachment->filename);
        });
    }

    public function eap_podcast(): BelongsTo
    {
        return $this->belongsTo(EapPodcast::class, 'podcast_id', 'id');
    }
}
