<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapCategory
 *
 * @property int $id
 * @property string $name
 * @property string|null $type
 * @property int|null $parent_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapCategory> $childs
 * @property-read int|null $childs_count
 * @property-read Collection<int, EapArticle> $eap_articles
 * @property-read int|null $eap_articles_count
 * @property-read Collection<int, EapTranslation> $eap_category_translations
 * @property-read int|null $eap_category_translations_count
 * @property-read Collection<int, EapVideo> $eap_quizzes
 * @property-read int|null $eap_quizzes_count
 * @property-read Collection<int, EapVideo> $eap_videos
 * @property-read int|null $eap_videos_count
 * @property-read mixed $first_translation
 *
 * @method static Builder|EapCategory newModelQuery()
 * @method static Builder|EapCategory newQuery()
 * @method static Builder|EapCategory query()
 * @method static Builder|EapCategory whereCreatedAt($value)
 * @method static Builder|EapCategory whereId($value)
 * @method static Builder|EapCategory whereName($value)
 * @method static Builder|EapCategory whereParentId($value)
 * @method static Builder|EapCategory whereType($value)
 * @method static Builder|EapCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapCategory extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'categories';

    protected $guarded = [];

    public function getFirstTranslationAttribute()
    {
        return $this->eap_category_translations()->orderBy('created_at')->first()->language->name;
    }

    public function eap_articles(): BelongsToMany
    {
        return $this->belongsToMany(EapArticle::class, 'article_category', 'category_id', 'article_id');
    }

    public function eap_videos(): BelongsToMany
    {
        return $this->belongsToMany(EapVideo::class, 'video_category', 'category_id', 'video_id');
    }

    public function eap_quizzes(): BelongsToMany
    {
        return $this->belongsToMany(EapVideo::class, 'quiz_category', 'category_id', 'quiz_id');
    }

    public function eap_category_translations(): MorphMany
    {
        return $this->morphMany(EapTranslation::class, 'translatable');
    }

    public function childs(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function get_translation($language_id)
    {
        return $this->morphOne(EapTranslation::class, 'translatable')->where('language_id', $language_id)->first();
    }

    public function hasTranslation($language_id)
    {
        return $this->morphOne(EapTranslation::class, 'translatable')->where('language_id', $language_id)->exists();
    }
}
