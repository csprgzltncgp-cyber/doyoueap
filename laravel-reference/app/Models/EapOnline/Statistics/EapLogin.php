<?php

namespace App\Models\EapOnline\Statistics;

use App\Models\Company;
use App\Models\EapOnline\EapUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\Statistics\EapLogin
 *
 * @property int $id
 * @property int $user_id
 * @property string $user_agent
 * @property int $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read EapUser $eap_user
 *
 * @method static Builder|EapLogin newModelQuery()
 * @method static Builder|EapLogin newQuery()
 * @method static Builder|EapLogin query()
 * @method static Builder|EapLogin whereCountryId($value)
 * @method static Builder|EapLogin whereCreatedAt($value)
 * @method static Builder|EapLogin whereId($value)
 * @method static Builder|EapLogin whereUpdatedAt($value)
 * @method static Builder|EapLogin whereUserAgent($value)
 * @method static Builder|EapLogin whereUserId($value)
 *
 * @mixin \Eloquent
 */
class EapLogin extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'login_statistics';

    protected $fillable = [];

    public function company(): HasOneThrough
    {
        return $this->setConnection('mysql')->hasOneThrough(Company::class, EapUser::class, 'company_id', 'user_id');
    }

    public function eap_user(): BelongsTo
    {
        return $this->belongsTo(EapUser::class, 'user_id', 'id');
    }
}
