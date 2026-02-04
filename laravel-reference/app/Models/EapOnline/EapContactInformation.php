<?php

namespace App\Models\EapOnline;

use App\Models\Company;
use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapContactInformation
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $country_id
 * @property string $email
 * @property string $phone
 * @property int|null $disabled_phone_card
 * @property int|null $disabled_email_card
 * @property int|null $disabled_chat_card
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read Country|null $country
 * @property-read EapLanguage $eap_language
 *
 * @method static Builder|EapContactInformation newModelQuery()
 * @method static Builder|EapContactInformation newQuery()
 * @method static Builder|EapContactInformation query()
 * @method static Builder|EapContactInformation whereCompanyId($value)
 * @method static Builder|EapContactInformation whereCountryId($value)
 * @method static Builder|EapContactInformation whereCreatedAt($value)
 * @method static Builder|EapContactInformation whereDisabledChatCard($value)
 * @method static Builder|EapContactInformation whereDisabledEmailCard($value)
 * @method static Builder|EapContactInformation whereDisabledPhoneCard($value)
 * @method static Builder|EapContactInformation whereEmail($value)
 * @method static Builder|EapContactInformation whereId($value)
 * @method static Builder|EapContactInformation wherePhone($value)
 * @method static Builder|EapContactInformation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapContactInformation extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'contact_information';

    protected $guarded = [];

    public function eap_language(): BelongsTo
    {
        return $this->belongsTo(EapLanguage::class, 'language_id', 'id');
    }

    public function company(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Company::class, 'company_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Country::class, 'country_id', 'id');
    }
}
