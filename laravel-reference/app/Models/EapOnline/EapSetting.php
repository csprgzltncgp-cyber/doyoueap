<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapSetting
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EapTranslation> $eap_translations
 * @property-read int|null $eap_translations_count
 *
 * @method static Builder|EapSetting newModelQuery()
 * @method static Builder|EapSetting newQuery()
 * @method static Builder|EapSetting query()
 * @method static Builder|EapSetting whereCreatedAt($value)
 * @method static Builder|EapSetting whereId($value)
 * @method static Builder|EapSetting whereName($value)
 * @method static Builder|EapSetting whereUpdatedAt($value)
 * @method static Builder|EapSetting whereValue($value)
 *
 * @mixin \Eloquent
 */
class EapSetting extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'settings';

    protected $guarded = [];

    public function eap_translations(): MorphMany
    {
        return $this->morphMany(EapTranslation::class, 'translatable');
    }

    public function get_translation($language_id)
    {
        return $this->morphOne(EapTranslation::class, 'translatable')->where('language_id', $language_id)->first();
    }
}
