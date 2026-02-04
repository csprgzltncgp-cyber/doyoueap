<?php

namespace App\Models\EapOnline;

use App\Traits\EapOnline\CategoryTrait;
use App\Traits\EapOnline\VisibilityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapWebinar
 *
 * @property int $id
 * @property string $slug
 * @property int|null $language * @property string $link
 * @property string $long_title
 * @property string $short_title
 * @property string|null $description_first_line
 * @property string|null $description_second_line
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapCategory> $eap_categories
 * @property-read int|null $eap_categories_count
 * @property-read EapVisibility|null $eap_visibility
 * @property-read EapLesson|null $lesson
 * @property-read EapChapter|null $chapter
 *
 * @method static Builder|EapWebinar newModelQuery()
 * @method static Builder|EapWebinar newQuery()
 * @method static Builder|EapWebinar query()
 * @method static Builder|EapWebinar whereCreatedAt($value)
 * @method static Builder|EapWebinar whereDescriptionFirstLine($value)
 * @method static Builder|EapWebinar whereDescriptionSecondLine($value)
 * @method static Builder|EapWebinar whereId($value)
 * @method static Builder|EapWebinar whereLanguage($value)
 * @method static Builder|EapWebinar whereLink($value)
 * @method static Builder|EapWebinar whereLongTitle($value)
 * @method static Builder|EapWebinar whereShortTitle($value)
 * @method static Builder|EapWebinar whereSlug($value)
 * @method static Builder|EapWebinar whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapWebinar extends Model
{
    use CategoryTrait;
    use VisibilityTrait;

    protected $connection = 'mysql_eap_online';

    protected $table = 'webinars';

    protected $guarded = [];

    public function eap_visibility(): HasOne
    {
        return $this->hasOne(EapVisibility::class, 'resource_id')->type('webinar');
    }

    public function eap_categories(): BelongsToMany
    {
        return $this->belongsToMany(EapCategory::class, 'webinar_category', 'webinar_id', 'category_id');
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
}
