<?php

namespace App\Models\EapOnline;

use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\EapOnline\EapLanguage
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property-read Collection<int, Country> $countries
 * @property-read int|null $countries_count
 * @property-read EapContactInformation|null $eap_contact_information
 *
 * @method static Builder|EapLanguage newModelQuery()
 * @method static Builder|EapLanguage newQuery()
 * @method static Builder|EapLanguage query()
 * @method static Builder|EapLanguage whereCode($value)
 * @method static Builder|EapLanguage whereId($value)
 * @method static Builder|EapLanguage whereName($value)
 *
 * @mixin \Eloquent
 */
class EapLanguage extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'languages';

    protected $fillable = ['name', 'code'];

    public $timestamps = false;

    public function eap_contact_information(): HasOne
    {
        return $this->setConnection('mysql_eap_online')->hasOne(EapContactInformation::class, 'language_id');
    }

    public function countries(): BelongsToMany
    {
        $dbname = DB::connection('mysql_eap_online')->getDatabaseName();

        return $this->setConnection('mysql')->belongsToMany(Country::class, $dbname.'.country_language', 'language_id', 'country_id');
    }
}
