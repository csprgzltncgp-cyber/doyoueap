<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\EapOnline\EapSectionAttachment
 *
 * @property int $id
 * @property int $section_id
 * @property string|null $filename
 * @property int $language_id
 * @property int|null $same
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EapLanguage $eap_language
 * @property-read EapSection|null $eap_section
 *
 * @method static Builder|EapSectionAttachment newModelQuery()
 * @method static Builder|EapSectionAttachment newQuery()
 * @method static Builder|EapSectionAttachment query()
 * @method static Builder|EapSectionAttachment whereCreatedAt($value)
 * @method static Builder|EapSectionAttachment whereFilename($value)
 * @method static Builder|EapSectionAttachment whereId($value)
 * @method static Builder|EapSectionAttachment whereLanguageId($value)
 * @method static Builder|EapSectionAttachment whereSame($value)
 * @method static Builder|EapSectionAttachment whereSectionId($value)
 * @method static Builder|EapSectionAttachment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapSectionAttachment extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'section_attachments';

    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function ($attachment): void {
            Storage::delete('eap-online/section-attachments/'.$attachment->filename);
        });
    }

    public function eap_section(): BelongsTo
    {
        return $this->belongsTo(EapSection::class, 'section_id', 'id');
    }

    public function eap_language(): BelongsTo
    {
        return $this->belongsTo(EapLanguage::class, 'language_id', 'id');
    }
}
