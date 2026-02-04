<?php

namespace App\Models\PrizeGame;

use App\Models\EapOnline\EapTranslation;
use App\Traits\Prizegame\TranslationTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PrizeGame\Document
 *
 * @property int $id
 * @property string $download_button_text
 * @property string $filename
 * @property int $section_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Section|null $section
 *
 * @method static Builder|Document newModelQuery()
 * @method static Builder|Document newQuery()
 * @method static Builder|Document query()
 * @method static Builder|Document whereCreatedAt($value)
 * @method static Builder|Document whereDownloadButtonText($value)
 * @method static Builder|Document whereFilename($value)
 * @method static Builder|Document whereId($value)
 * @method static Builder|Document whereSectionId($value)
 * @method static Builder|Document whereUpdatedAt($value)
 *
 * @property-read Collection<int, EapTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @mixin \Eloquent
 */
class Document extends Model
{
    use TranslationTrait;

    public $guarded = [];

    protected $connection = 'mysql_eap_online';

    protected $table = 'prizegame_documents';

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function (self $document): void {
            $document->translations()->delete();
        });
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
