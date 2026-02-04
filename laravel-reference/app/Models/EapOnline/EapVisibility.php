<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapVisibility
 *
 * @property int $id
 * @property int $resource_id
 * @property string|null $type
 * @property int $self_care
 * @property int $after_assessment
 * @property int $well_being
 * @property int $theme_of_the_month
 * @property int $home_page
 * @property int $burnout_page
 * @property int $domestic_violence_page
 * @property string|null $from_date
 * @property string|null $to_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|EapVisibility newModelQuery()
 * @method static Builder|EapVisibility newQuery()
 * @method static Builder|EapVisibility query()
 * @method static Builder|EapVisibility type($value)
 * @method static Builder|EapVisibility whereAfterAssessment($value)
 * @method static Builder|EapVisibility whereBurnoutPage($value)
 * @method static Builder|EapVisibility whereCreatedAt($value)
 * @method static Builder|EapVisibility whereFromDate($value)
 * @method static Builder|EapVisibility whereHomePage($value)
 * @method static Builder|EapVisibility whereId($value)
 * @method static Builder|EapVisibility whereResourceId($value)
 * @method static Builder|EapVisibility whereSelfCare($value)
 * @method static Builder|EapVisibility whereThemeOfTheMonth($value)
 * @method static Builder|EapVisibility whereToDate($value)
 * @method static Builder|EapVisibility whereType($value)
 * @method static Builder|EapVisibility whereUpdatedAt($value)
 * @method static Builder|EapVisibility whereWellBeing($value)
 *
 * @mixin \Eloquent
 */
class EapVisibility extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'visibilities';

    protected $guarded = [];

    public function scopeType($query, $value)
    {
        return $query->where('type', $value);
    }
}
