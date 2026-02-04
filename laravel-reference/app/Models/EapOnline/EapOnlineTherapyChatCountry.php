<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EapOnline\EapOnlineTherapyChatCountry
 *
 * @property int $id
 * @property int $country_id
 *
 * @method static Builder|EapOnlineTherapyChatCountry newModelQuery()
 * @method static Builder|EapOnlineTherapyChatCountry newQuery()
 * @method static Builder|EapOnlineTherapyChatCountry query()
 * @method static Builder|EapOnlineTherapyChatCountry whereCountryId($value)
 * @method static Builder|EapOnlineTherapyChatCountry whereId($value)
 *
 * @mixin \Eloquent
 */
class EapOnlineTherapyChatCountry extends Model
{
    use HasFactory;

    protected $connection = 'mysql_eap_online';

    protected $table = 'online_therapy_countries';

    protected $fillable = ['country_id'];

    public $timestamps = false;
}
