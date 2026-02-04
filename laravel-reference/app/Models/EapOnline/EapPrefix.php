<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapPrefix
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapArticle> $eap_articles
 * @property-read int|null $eap_articles_count
 * @property-read Collection<int, EapTranslation> $eap_prefix_translations
 * @property-read int|null $eap_prefix_translations_count
 * @property-read mixed $first_translation
 *
 * @method static Builder|EapPrefix newModelQuery()
 * @method static Builder|EapPrefix newQuery()
 * @method static Builder|EapPrefix query()
 * @method static Builder|EapPrefix whereCreatedAt($value)
 * @method static Builder|EapPrefix whereId($value)
 * @method static Builder|EapPrefix whereName($value)
 * @method static Builder|EapPrefix whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapPrefix extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'prefixes';

    protected $guarded = [];

    public function getFirstTranslationAttribute()
    {
        return optional(optional($this->eap_prefix_translations()->orderBy('created_at')->first())->language)->name;
    }

    public function eap_prefix_translations(): MorphMany
    {
        return $this->morphMany(EapTranslation::class, 'translatable');
    }

    public function eap_articles(): HasMany
    {
        return $this->hasMany(EapArticle::class, 'prefix_id', 'id');
    }

    public function get_translation($language_id)
    {
        return $this->morphOne(EapTranslation::class, 'translatable')->where('language_id', $language_id)->first();
    }
}
