<?php

namespace App\Models\CompanyWebsite;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\CompanyWebsite\Section
 *
 * @property int $id
 * @property int $article_id
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Article|null $article
 * @property-read Collection<int, Translation> $translations
 * @property-read int|null $translations_count
 *
 * @method static Builder|Section newModelQuery()
 * @method static Builder|Section newQuery()
 * @method static Builder|Section query()
 * @method static Builder|Section whereArticleId($value)
 * @method static Builder|Section whereCreatedAt($value)
 * @method static Builder|Section whereId($value)
 * @method static Builder|Section whereType($value)
 * @method static Builder|Section whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Section extends Model
{
    final public const TYPE_HEADLINE = 'headline';

    final public const TYPE_HIGHLIGHT = 'highlight';

    final public const TYPE_SUBTITLE = 'subtitle';

    final public const TYPE_LEAD = 'lead';

    final public const TYPE_LIST = 'list';

    final public const TYPE_BODY = 'body';

    protected $connection = 'mysql_company_website';

    protected $fillable = [
        'article_id',
        'type',
    ];

    public static function boot(): void
    {
        parent::boot();

        self::deleting(function ($section): void {
            $section->translations()->each(function ($translation): void {
                $translation->delete();
            });
        });
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function get_translation($language_id)
    {
        return $this->morphOne(Translation::class, 'translatable')->where('language_id', $language_id)->first();
    }

    public function has_translation($language_id): bool
    {
        if ($translation = $this->get_translation($language_id)) {
            return ! empty($translation->value);
        }

        return false;
    }
}
