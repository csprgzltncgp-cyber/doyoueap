<?php

namespace App\Models\PrizeGame;

use App\Models\EapOnline\EapTranslation;
use App\Traits\Prizegame\TranslationTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\PrizeGame\Section
 *
 * @property int $id
 * @property int $type
 * @property string $value
 * @property int $block
 * @property int $content_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Content|null $content
 * @property-read Document|null $documents
 *
 * @method static Builder|Section newModelQuery()
 * @method static Builder|Section newQuery()
 * @method static Builder|Section query()
 * @method static Builder|Section whereBlock($value)
 * @method static Builder|Section whereContentId($value)
 * @method static Builder|Section whereCreatedAt($value)
 * @method static Builder|Section whereId($value)
 * @method static Builder|Section whereType($value)
 * @method static Builder|Section whereUpdatedAt($value)
 * @method static Builder|Section whereValue($value)
 *
 * @property-read Collection<int, EapTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @mixin \Eloquent
 */
class Section extends Model
{
    use TranslationTrait;

    final public const TYPE_HEADLINE = 1;

    final public const TYPE_SUB_HEADLINE = 2;

    final public const TYPE_LIST = 3;

    final public const TYPE_BODY = 4;

    final public const TYPE_CHECKBOX = 5;

    protected $guarded = [];

    protected $connection = 'mysql_eap_online';

    protected $table = 'prizegame_sections';

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function (self $section): void {
            if ($section->documents()->exists()) {
                Storage::delete('eap-online/prizegame/documents/'.$section->documents->filename);
                $section->documents()->delete();
            }

            $section->translations()->delete();
        });
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function documents(): HasOne
    {
        return $this->hasOne(Document::class);
    }
}
