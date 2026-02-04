<?php

namespace App\Models\EapOnline;

use App\Traits\EapOnline\CategoryTrait;
use App\Traits\EapOnline\VisibilityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapPodcast
 *
 * @property int $id
 * @property string $slug
 * @property int $language
 * @property string $link
 * @property string $long_title
 * @property string $short_title
 * @property string $description_first_line
 * @property string $description_second_line
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapCategory> $eap_categories
 * @property-read int|null $eap_categories_count
 * @property-read EapPodcastAttachment|null $eap_podcast_attachment
 * @property-read EapVisibility|null $eap_visibility
 *
 * @method static Builder|EapPodcast newModelQuery()
 * @method static Builder|EapPodcast newQuery()
 * @method static Builder|EapPodcast query()
 * @method static Builder|EapPodcast whereCreatedAt($value)
 * @method static Builder|EapPodcast whereDescriptionFirstLine($value)
 * @method static Builder|EapPodcast whereDescriptionSecondLine($value)
 * @method static Builder|EapPodcast whereId($value)
 * @method static Builder|EapPodcast whereLanguage($value)
 * @method static Builder|EapPodcast whereLink($value)
 * @method static Builder|EapPodcast whereLongTitle($value)
 * @method static Builder|EapPodcast whereShortTitle($value)
 * @method static Builder|EapPodcast whereSlug($value)
 * @method static Builder|EapPodcast whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapPodcast extends Model
{
    use CategoryTrait;
    use VisibilityTrait;

    protected $connection = 'mysql_eap_online';

    protected $table = 'podcasts';

    protected $guarded = [];

    public function eap_visibility(): HasOne
    {
        return $this->hasOne(EapVisibility::class, 'resource_id')->type('podcast');
    }

    public function eap_podcast_attachment(): HasOne
    {
        return $this->hasOne(EapPodcastAttachment::class, 'podcast_id', 'id');
    }

    public function eap_categories(): BelongsToMany
    {
        return $this->belongsToMany(EapCategory::class, 'podcast_category', 'podcast_id', 'category_id');
    }

    public function hasCategory($category_id)
    {
        return $this->eap_categories()->where('category_id', $category_id)->exists();
    }
}
