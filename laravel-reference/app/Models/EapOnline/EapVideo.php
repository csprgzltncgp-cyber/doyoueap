<?php

namespace App\Models\EapOnline;

use App\Traits\EapOnline\CategoryTrait;
use App\Traits\EapOnline\VisibilityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapVideo
 *
 * @property int $id
 * @property string $slug
 * @property int|null $language
 * @property int $all_languages
 * @property string $link
 * @property string $long_title
 * @property string $short_title
 * @property string|null $description_first_line
 * @property string|null $description_second_line
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapCategory> $eap_categories
 * @property-read int|null $eap_categories_count
 * @property-read Collection<int, EapVideoAttachment> $eap_video_attachment
 * @property-read int|null $eap_video_attachment_count
 * @property-read EapVisibility|null $eap_visibility
 * @property-read EapLesson|null $lesson
 * @property-read EapChapter|null $chapter
 *
 * @method static Builder|EapVideo newModelQuery()
 * @method static Builder|EapVideo newQuery()
 * @method static Builder|EapVideo query()
 * @method static Builder|EapVideo whereAllLanguages($value)
 * @method static Builder|EapVideo whereCreatedAt($value)
 * @method static Builder|EapVideo whereDescriptionFirstLine($value)
 * @method static Builder|EapVideo whereDescriptionSecondLine($value)
 * @method static Builder|EapVideo whereId($value)
 * @method static Builder|EapVideo whereLanguage($value)
 * @method static Builder|EapVideo whereLink($value)
 * @method static Builder|EapVideo whereLongTitle($value)
 * @method static Builder|EapVideo whereShortTitle($value)
 * @method static Builder|EapVideo whereSlug($value)
 * @method static Builder|EapVideo whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapVideo extends Model
{
    use CategoryTrait;
    use VisibilityTrait;

    protected $connection = 'mysql_eap_online';

    protected $table = 'videos';

    protected $guarded = [];

    public function eap_visibility(): HasOne
    {
        return $this->hasOne(EapVisibility::class, 'resource_id')->type('video');
    }

    public function eap_video_attachment(): HasMany
    {
        return $this->hasMany(EapVideoAttachment::class, 'video_id', 'id');
    }

    public function eap_categories(): BelongsToMany
    {
        return $this->belongsToMany(EapCategory::class, 'video_category', 'video_id', 'category_id');
    }

    public function lesson(): MorphOne
    {
        return $this->morphOne(EapLesson::class, 'lessonable');
    }

    public function chapter(): MorphOne
    {
        return $this->morphOne(EapChapter::class, 'chapterable');
    }

    public function hasCategory($category_id)
    {
        return $this->eap_categories()->where('category_id', $category_id)->exists();
    }

    public function get_translation($type, $language_id)
    {
        return EapTranslation::query()->where(['translatable_type' => "App\Models\Video\\{$type}", 'translatable_id' => $this->id, 'language_id' => $language_id])->first();
    }

    public function hasMissingTranslation(): bool
    {
        return $this->getMissingTranslationsNumber() > 0;
    }

    public function hasMissingLanguageTranslation($language_id): bool
    {
        if (empty($this->get_translation('LongTitle', $language_id))) {
            return true;
        }
        if (empty($this->get_translation('ShortTitle', $language_id))) {
            return true;
        }
        if (empty($this->get_translation('DescriptionFirstLine', $language_id))) {
            return true;
        }

        return empty($this->get_translation('DescriptionSecondLine', $language_id));
    }

    /**
     * @return bool[]
     */
    public function getReadyLanguages(): array
    {
        $languages = EapLanguage::all();
        $ready_languages = [];

        foreach ($languages as $language) {
            $ready_languages[$language->code] = true;
            if (empty($this->get_translation('LongTitle', $language->id))) {
                $ready_languages[$language->code] = false;
            }
            if (empty($this->get_translation('ShortTitle', $language->id))) {
                $ready_languages[$language->code] = false;
            }
            if (empty($this->get_translation('DescriptionFirstLine', $language->id))) {
                $ready_languages[$language->code] = false;
            }
            if (empty($this->get_translation('DescriptionSecondLine', $language->id))) {
                $ready_languages[$language->code] = false;
            }
        }

        return $ready_languages;
    }

    public function getMissingTranslationsNumber(): int
    {
        $languages = EapLanguage::all();
        $missing_translations = 0;

        foreach ($languages as $language) {
            if (empty($this->get_translation('LongTitle', $language->id))) {
                $missing_translations++;
            }
            if (empty($this->get_translation('ShortTitle', $language->id))) {
                $missing_translations++;
            }
            if (empty($this->get_translation('DescriptionFirstLine', $language->id))) {
                $missing_translations++;
            }
            if (empty($this->get_translation('DescriptionSecondLine', $language->id))) {
                $missing_translations++;
            }
        }

        return $missing_translations;
    }

    public function has_attachment_translation($language_id)
    {
        return $this->eap_video_attachment()->where('language_id', $language_id)->exists();
    }
}
