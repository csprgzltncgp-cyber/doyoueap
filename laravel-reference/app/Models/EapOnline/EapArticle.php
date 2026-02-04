<?php

namespace App\Models\EapOnline;

use App\Traits\EapOnline\CategoryTrait;
use App\Traits\EapOnline\VisibilityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapArticle
 *
 * @property int $id
 * @property string $slug
 * @property int $input_language
 * @property int|null $prefix_id
 * @property string|null $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapCategory> $eap_categories
 * @property-read int|null $eap_categories_count
 * @property-read EapPrefix|null $eap_prefix
 * @property-read Collection<int, EapSection> $eap_sections
 * @property-read int|null $eap_sections_count
 * @property-read EapThumbnail|null $eap_thumbnail
 * @property-read EapVisibility|null $eap_visibility
 * @property-read EapLesson|null $lesson
 * @property-read EapLesson|null $chapter
 *
 * @method static Builder|EapArticle newModelQuery()
 * @method static Builder|EapArticle newQuery()
 * @method static Builder|EapArticle query()
 * @method static Builder|EapArticle whereCreatedAt($value)
 * @method static Builder|EapArticle whereId($value)
 * @method static Builder|EapArticle whereInputLanguage($value)
 * @method static Builder|EapArticle wherePrefixId($value)
 * @method static Builder|EapArticle whereSlug($value)
 * @method static Builder|EapArticle whereType($value)
 * @method static Builder|EapArticle whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapArticle extends Model
{
    use CategoryTrait;
    use VisibilityTrait;

    protected $connection = 'mysql_eap_online';

    protected $table = 'articles';

    protected $guarded = [];

    public function eap_thumbnail(): HasOne
    {
        return $this->hasOne(EapThumbnail::class, 'resource_id', 'id')->type('article');
    }

    public function eap_categories(): BelongsToMany
    {
        return $this->belongsToMany(EapCategory::class, 'article_category', 'article_id', 'category_id');
    }

    public function eap_sections(): HasMany
    {
        return $this->hasMany(EapSection::class, 'article_id', 'id');
    }

    public function eap_prefix(): BelongsTo
    {
        return $this->belongsTo(EapPrefix::class, 'prefix_id', 'id');
    }

    public function eap_visibility(): HasOne
    {
        return $this->hasOne(EapVisibility::class, 'resource_id', 'id')->type('article');
    }

    public function lesson(): MorphOne
    {
        return $this->morphOne(EapLesson::class, 'lessonable');
    }

    public function chapter(): MorphOne
    {
        return $this->morphOne(EapChapter::class, 'chapterable');
    }

    public function getSectionByType($type)
    {
        $sections = $this->eap_sections;

        foreach ($sections as $section) {
            if ($section->type == $type) {
                return $section->eap_section_translations()->where('language_id', $this->input_language)->first()->value;
            }
        }

        return null;
    }

    public function getMissingTranslationsNumber(): int
    {
        $sections = $this->eap_sections;
        $languages = EapLanguage::all();
        $missing_translations = 0;

        foreach ($sections as $section) {
            foreach ($languages as $language) {
                if (! $section->hasTranslation($language->id)) {
                    $missing_translations++;
                }
            }
        }

        return $missing_translations;
    }

    /**
     * @return bool[]
     */
    public function getReadyLanguages(): array
    {
        $languages = EapLanguage::all();
        $sections = $this->eap_sections;
        $ready_languages = [];

        foreach ($languages as $language) {
            $ready_languages[$language->code] = true;
            foreach ($sections as $section) {
                if ($section->hasTranslation($language->id)) {
                    continue;
                }
                if ($section->has_attachment_translation($language->id)) {
                    continue;
                }
                $ready_languages[$language->code] = false;
            }
        }

        return $ready_languages;
    }

    public function hasMissingTranslation($language_id): bool
    {
        foreach ($this->eap_sections as $section) {
            if ($section->hasTranslation($language_id)) {
                continue;
            }
            if ($section->has_attachment_translation($language_id)) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function hasCategory($category_id)
    {
        return $this->eap_categories()->where('category_id', $category_id)->exists();
    }
}
