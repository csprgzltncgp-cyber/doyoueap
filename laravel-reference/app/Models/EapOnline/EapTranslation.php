<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapTranslation
 *
 * @property int $id
 * @property int $translatable_id
 * @property string $translatable_type
 * @property int $language_id
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EapLanguage $language
 * @property-read Model|\Eloquent $translatable
 *
 * @method static Builder|EapTranslation newModelQuery()
 * @method static Builder|EapTranslation newQuery()
 * @method static Builder|EapTranslation query()
 * @method static Builder|EapTranslation whereCreatedAt($value)
 * @method static Builder|EapTranslation whereId($value)
 * @method static Builder|EapTranslation whereLanguageId($value)
 * @method static Builder|EapTranslation whereTranslatableId($value)
 * @method static Builder|EapTranslation whereTranslatableType($value)
 * @method static Builder|EapTranslation whereUpdatedAt($value)
 * @method static Builder|EapTranslation whereValue($value)
 *
 * @mixin \Eloquent
 */
class EapTranslation extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'translations';

    protected $guarded = [];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(EapLanguage::class, 'language_id', 'id');
    }
}
