<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * OnsiteConsultation
 *
 * @mixin Builder
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class OnsiteConsultationExpert extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    /**
     * @return HasMany<OnsiteConsultationDateAppointment>
     */
    public function onsite_consultation_date_appointments(): HasMany
    {
        return $this->hasMany(OnsiteConsultationDateAppointment::class);
    }
}
