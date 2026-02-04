<?php

namespace App\Models\EapOnline\Statistics;

use App\Models\Company;
use App\Models\EapOnline\EapCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\Statistics\EapSelfHelp
 *
 * @property int $id
 * @property int $company_id
 * @property int $category_id
 * @property string $user_agent
 * @property int $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read EapCategory|null $eap_category
 *
 * @method static Builder|EapSelfHelp newModelQuery()
 * @method static Builder|EapSelfHelp newQuery()
 * @method static Builder|EapSelfHelp query()
 * @method static Builder|EapSelfHelp whereCategoryId($value)
 * @method static Builder|EapSelfHelp whereCompanyId($value)
 * @method static Builder|EapSelfHelp whereCountryId($value)
 * @method static Builder|EapSelfHelp whereCreatedAt($value)
 * @method static Builder|EapSelfHelp whereId($value)
 * @method static Builder|EapSelfHelp whereUpdatedAt($value)
 * @method static Builder|EapSelfHelp whereUserAgent($value)
 *
 * @mixin \Eloquent
 */
class EapSelfHelp extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'self_help_statistics';

    protected $fillable = [];

    public function company(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Company::class);
    }

    public function eap_category(): BelongsTo
    {
        return $this->belongsTo(EapCategory::class, 'category_id', 'id');
    }
}
