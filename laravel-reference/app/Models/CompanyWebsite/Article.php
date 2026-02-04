<?php

namespace App\Models\CompanyWebsite;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\CompanyWebsite\Article
 *
 * @property int $id
 * @property int $input_language
 * @property string $slug
 * @property string $seo_title
 * @property string|null $seo_description
 * @property string|null $seo_keywords
 * @property string|null $thumbnail
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Section> $sections
 * @property-read int|null $sections_count
 * @property string $slugFrom
 *
 * @method static Builder|Article newModelQuery()
 * @method static Builder|Article newQuery()
 * @method static Builder|Article query()
 * @method static Builder|Article whereCreatedAt($value)
 * @method static Builder|Article whereId($value)
 * @method static Builder|Article whereInputLanguage($value)
 * @method static Builder|Article whereSeoDescription($value)
 * @method static Builder|Article whereSeoKeywords($value)
 * @method static Builder|Article whereSeoTitle($value)
 * @method static Builder|Article whereSlug($value)
 * @method static Builder|Article whereThumbnail($value)
 * @method static Builder|Article whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Article extends Model
{
    use HasSlug;

    protected $connection = 'mysql_company_website';

    protected $fillable = [
        'thumbnail',
        'slug',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'input_language',
    ];

    public $slugFrom = 'seo_title';

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function getSectionByType($type)
    {
        $sections = $this->sections;

        foreach ($sections as $section) {
            if ($section->type == $type) {
                return $section->translations()->where('language_id', $this->input_language)->first()->value;
            }
        }

        return null;
    }

    /**
     * @return bool[]
     */
    public function getReadyLanguages(): array
    {
        $sections = $this->sections;
        $languages = Language::all();
        $ready_languages = [];

        foreach ($languages as $language) {
            $ready_languages[$language->code] = true;
            foreach ($sections as $section) {
                $has_translation = $section->translations()
                    ->where('language_id', $language->id)
                    ->where('value', '<>', '')
                    ->whereNotNull('value')
                    ->exists();

                if (! $has_translation) {
                    $ready_languages[$language->code] = false;
                }
            }
        }

        return $ready_languages;
    }
}
