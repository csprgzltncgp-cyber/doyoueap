<?php

namespace App\Models\EapOnline;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\EapOnline\OnsiteConsultationPlace
 *
 * @mixin Builder
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection<int, OnsiteConsultation> $consultations
 *
 * @method static Builder|OnsiteConsultationPlace newModelQuery()
 * @method static Builder|OnsiteConsultationPlace newQuery()
 * @method static Builder|OnsiteConsultationPlace query()
 * @method static Builder|OnsiteConsultationPlace whereAllLanguages($value)
 * @method static Builder|OnsiteConsultationPlace whereCreatedAt($value)
 * @method static Builder|OnsiteConsultationPlace whereDescriptionFirstLine($value)
 * @method static Builder|OnsiteConsultationPlace whereDescriptionSecondLine($value)
 * @method static Builder|OnsiteConsultationPlace whereId($value)
 * @method static Builder|OnsiteConsultationPlace whereLanguage($value)
 * @method static Builder|OnsiteConsultationPlace whereLink($value)
 * @method static Builder|OnsiteConsultationPlace whereLongTitle($value)
 * @method static Builder|OnsiteConsultationPlace whereShortTitle($value)
 * @method static Builder|OnsiteConsultationPlace whereSlug($value)
 * @method static Builder|OnsiteConsultationPlace whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OnsiteConsultationPlace extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $fillable = [
        'name',
        'address',
    ];

    public function consultations(): HasMany
    {
        return $this->hasMany(OnsiteConsultation::class, 'id');
    }
}
