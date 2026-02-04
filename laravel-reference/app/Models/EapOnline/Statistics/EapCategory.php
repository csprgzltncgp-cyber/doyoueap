<?php

namespace App\Models\EapOnline\Statistics;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\Statistics\EapCategory
 *
 * @property int $id
 * @property int $company_id
 * @property int $category_id
 * @property int $type
 * @property string $user_agent
 * @property int $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read EapCategory|null $eap_category
 *
 * @method static Builder|EapCategory newModelQuery()
 * @method static Builder|EapCategory newQuery()
 * @method static Builder|EapCategory query()
 * @method static Builder|EapCategory whereCategoryId($value)
 * @method static Builder|EapCategory whereCompanyId($value)
 * @method static Builder|EapCategory whereCountryId($value)
 * @method static Builder|EapCategory whereCreatedAt($value)
 * @method static Builder|EapCategory whereId($value)
 * @method static Builder|EapCategory whereType($value)
 * @method static Builder|EapCategory whereUpdatedAt($value)
 * @method static Builder|EapCategory whereUserAgent($value)
 *
 * @mixin \Eloquent
 */
class EapCategory extends Model
{
    final public const TYPE_ARTICLE = 1;

    final public const TYPE_VIDEO = 2;

    final public const TYPE_PODCAST = 3;

    protected $connection = 'mysql_eap_online';

    protected $table = 'category_statistics';

    protected $fillable = [];

    protected $casts = [
        'type' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Company::class);
    }

    public function eap_category(): BelongsTo
    {
        return $this->belongsTo(EapCategory::class, 'category_id', 'id');
    }
}
