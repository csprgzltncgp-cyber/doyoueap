<?php

namespace App\Models\EapOnline\Statistics;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\Statistics\EapAssessment
 *
 * @property int $id
 * @property int $company_id
 * @property int $type
 * @property string $user_agent
 * @property int $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 *
 * @method static Builder|EapAssessment newModelQuery()
 * @method static Builder|EapAssessment newQuery()
 * @method static Builder|EapAssessment query()
 * @method static Builder|EapAssessment whereCompanyId($value)
 * @method static Builder|EapAssessment whereCountryId($value)
 * @method static Builder|EapAssessment whereCreatedAt($value)
 * @method static Builder|EapAssessment whereId($value)
 * @method static Builder|EapAssessment whereType($value)
 * @method static Builder|EapAssessment whereUpdatedAt($value)
 * @method static Builder|EapAssessment whereUserAgent($value)
 *
 * @mixin \Eloquent
 */
class EapAssessment extends Model
{
    final public const TYPE_ASSESSMENT = 1;

    final public const TYPE_MOOD_METER = 2;

    final public const TYPE_WELL_BEING = 3;

    protected $connection = 'mysql_eap_online';

    protected $table = 'assessment_statistics';

    protected $fillable = [];

    protected $casts = [
        'type' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Company::class);
    }
}
