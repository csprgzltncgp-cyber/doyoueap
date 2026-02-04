<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\ExpertData
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $phone_prefix
 * @property string|null $phone_number
 * @property string|null $post_code
 * @property int|null $country_id
 * @property int|null $city_id
 * @property string|null $street
 * @property int|null $street_suffix
 * @property string|null $house_number
 * @property bool $required_documents
 * @property bool $completed_first
 * @property int|null $max_inprogress_cases
 * @property int|null $min_inprogress_cases
 * @property int|null $native_language
 * @property bool $can_accept_more_cases
 * @property bool|null $is_cgp_employee
 * @property bool|null $is_eap_online_expert
 * @property int $crisis_psychologist
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read City|null $city
 * @property-read Country|null $country
 * @property-read Collection<int, ExpertFile> $files
 * @property-read int|null $files_count
 * @property-read User $user
 *
 * @method static Builder|ExpertData newModelQuery()
 * @method static Builder|ExpertData newQuery()
 * @method static Builder|ExpertData query()
 * @method static Builder|ExpertData whereCanAcceptMoreCases($value)
 * @method static Builder|ExpertData whereCityId($value)
 * @method static Builder|ExpertData whereCompletedFirst($value)
 * @method static Builder|ExpertData whereCountryId($value)
 * @method static Builder|ExpertData whereCreatedAt($value)
 * @method static Builder|ExpertData whereCrisisPsychologist($value)
 * @method static Builder|ExpertData whereHouseNumber($value)
 * @method static Builder|ExpertData whereId($value)
 * @method static Builder|ExpertData whereIsCgpEmployee($value)
 * @method static Builder|ExpertData whereIsEapOnlineExpert($value)
 * @method static Builder|ExpertData whereMaxInprogressCases($value)
 * @method static Builder|ExpertData whereMinInprogressCases($value)
 * @method static Builder|ExpertData whereNativeLanguage($value)
 * @method static Builder|ExpertData wherePhoneNumber($value)
 * @method static Builder|ExpertData wherePhonePrefix($value)
 * @method static Builder|ExpertData wherePostCode($value)
 * @method static Builder|ExpertData whereRequiredDocuments($value)
 * @method static Builder|ExpertData whereStreet($value)
 * @method static Builder|ExpertData whereStreetSuffix($value)
 * @method static Builder|ExpertData whereUpdatedAt($value)
 * @method static Builder|ExpertData whereUserId($value)
 *
 * @mixin \Eloquent
 */
class ExpertData extends Model
{
    final public const STREET_SUFFIX_STREET = 1;

    final public const STREET_SUFFIX_SQUARE = 2;

    final public const STREET_SUFFIX_ROAD = 3;

    protected $table = 'expert_datas';

    protected $guarded = [];

    protected $casts = [
        'can_accept_more_cases' => 'boolean',
        'is_cgp_employee' => 'boolean',
        'required_documents' => 'boolean',
        'completed_first' => 'boolean',
        'is_eap_online_expert' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ExpertFile::class);
    }
}
