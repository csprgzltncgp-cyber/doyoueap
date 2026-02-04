<?php

namespace App\Models\EapOnline\Assessment;

use App\Models\EapOnline\EapTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\Assessment\EapResult
 *
 * @property int $id
 * @property int $type
 * @property int $from
 * @property int|null $to
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @method static Builder|EapResult newModelQuery()
 * @method static Builder|EapResult newQuery()
 * @method static Builder|EapResult query()
 * @method static Builder|EapResult whereCreatedAt($value)
 * @method static Builder|EapResult whereFrom($value)
 * @method static Builder|EapResult whereId($value)
 * @method static Builder|EapResult whereTo($value)
 * @method static Builder|EapResult whereType($value)
 * @method static Builder|EapResult whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapResult extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'assessment_results';

    protected $guarded = [];

    public function translations(): MorphMany
    {
        return $this->morphMany(EapTranslation::class, 'translatable');
    }

    public function hasTranslation($language_id)
    {
        return $this->morphOne(EapTranslation::class, 'translatable')->where('language_id', $language_id)->exists();
    }
}
