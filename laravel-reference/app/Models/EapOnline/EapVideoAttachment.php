<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\EapOnline\EapVideoAttachment
 *
 * @property int $id
 * @property int $video_id
 * @property string $filename
 * @property int $language_id
 * @property string $button_text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EapLanguage $eap_language
 * @property-read EapVideo|null $eap_video
 *
 * @method static Builder|EapVideoAttachment newModelQuery()
 * @method static Builder|EapVideoAttachment newQuery()
 * @method static Builder|EapVideoAttachment query()
 * @method static Builder|EapVideoAttachment whereButtonText($value)
 * @method static Builder|EapVideoAttachment whereCreatedAt($value)
 * @method static Builder|EapVideoAttachment whereFilename($value)
 * @method static Builder|EapVideoAttachment whereId($value)
 * @method static Builder|EapVideoAttachment whereLanguageId($value)
 * @method static Builder|EapVideoAttachment whereUpdatedAt($value)
 * @method static Builder|EapVideoAttachment whereVideoId($value)
 *
 * @mixin \Eloquent
 */
class EapVideoAttachment extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'video_attachments';

    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function ($attachment): void {
            Storage::delete('eap-online/video-attachments/'.$attachment->filename);
        });
    }

    public function eap_video(): BelongsTo
    {
        return $this->belongsTo(EapVideo::class, 'video_id', 'id');
    }

    public function eap_language(): BelongsTo
    {
        return $this->belongsTo(EapLanguage::class, 'language_id', 'id');
    }
}
