<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\EapOnline\EapSection
 *
 * @property int $id
 * @property int $article_id
 * @property string|null $type
 * @property-read EapArticle|null $eap_article
 * @property-read Collection<int, EapSectionAttachment> $eap_section_attachment
 * @property-read int|null $eap_section_attachment_count
 * @property-read Collection<int, EapTranslation> $eap_section_translations
 * @property-read int|null $eap_section_translations_count
 *
 * @method static Builder|EapSection newModelQuery()
 * @method static Builder|EapSection newQuery()
 * @method static Builder|EapSection query()
 * @method static Builder|EapSection whereArticleId($value)
 * @method static Builder|EapSection whereId($value)
 * @method static Builder|EapSection whereType($value)
 *
 * @mixin \Eloquent
 */
class EapSection extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'sections';

    protected $guarded = [];

    public $timestamps = false;

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function ($section): void {
            $section->eap_section_translations()->each(function ($translation): void {
                $translation->delete();
            });

            $section->eap_section_attachment()->each(function ($attachment): void {
                $attachment->delete();
            });
        });
    }

    public function eap_article(): BelongsTo
    {
        return $this->belongsTo(EapArticle::class, 'article_id', 'id');
    }

    public function eap_section_attachment(): HasMany
    {
        return $this->hasMany(EapSectionAttachment::class, 'section_id', 'id');
    }

    public function eap_section_translations(): MorphMany
    {
        return $this->morphMany(EapTranslation::class, 'translatable');
    }

    public function get_translation($language_id)
    {
        return $this->morphOne(EapTranslation::class, 'translatable')->where('language_id', $language_id)->first();
    }

    public function hasTranslation($language_id): bool
    {
        if ($translation = $this->get_translation($language_id)) {
            return ! empty($translation->value);
        }

        return false;
    }

    public function has_attachment_translation($language_id)
    {
        return $this->eap_section_attachment()->where('language_id', $language_id)->exists();
    }
}
