<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\RiportValue
 *
 * @property int $id
 * @property int $riport_id
 * @property int $country_id
 * @property int $type
 * @property string $value
 * @property string|null $connection_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, RiportValue> $connected_values
 * @property-read int|null $connected_values_count
 * @property-read Riport|null $riport
 *
 * @method static Builder|RiportValue newModelQuery()
 * @method static Builder|RiportValue newQuery()
 * @method static Builder|RiportValue query()
 * @method static Builder|RiportValue whereConnectionId($value)
 * @method static Builder|RiportValue whereCountryId($value)
 * @method static Builder|RiportValue whereCreatedAt($value)
 * @method static Builder|RiportValue whereId($value)
 * @method static Builder|RiportValue whereRiportId($value)
 * @method static Builder|RiportValue whereType($value)
 * @method static Builder|RiportValue whereUpdatedAt($value)
 * @method static Builder|RiportValue whereValue($value)
 *
 * @property int|null $is_ongoing
 *
 * @method static Builder|RiportValue whereIsOngoing($value)
 *
 * @mixin \Eloquent
 */
class RiportValue extends Model
{
    // In Database
    final public const TYPE_IS_CRISIS = 3;

    final public const TYPE_PLACE_OF_RECEIPT = 6;

    final public const TYPE_PROBLEM_TYPE = 7;

    final public const TYPE_EMPLOYEE_OR_FAMILY_MEMBER = 9;

    final public const TYPE_GENDER = 10;

    final public const TYPE_AGE = 11;

    final public const TYPE_SOURCE = 12;

    final public const TYPE_PROBLEM_DETAILS = 16;

    final public const TYPE_TYPE_OF_PROBLEM = 24;

    final public const TYPE_LANGUAGE = 32;

    // Company specific in database
    final public const TYPE_VALEO_WORKPLACE_1 = 47;

    final public const TYPE_VALEO_WORKPLACE_2 = 48;

    final public const TYPE_HYDRO_WORKPLACE = 59;

    final public const TYPE_PSE_WORKPLACE = 60;

    final public const TYPE_MICHELIN_WORKPLACE = 61;

    final public const TYPE_SK_BATTERY_WORKPLACE = 74;

    final public const TYPE_GRUPA_WORKPLACE = 71;

    final public const TYPE_ROBERT_BOSCH_WORKPLACE = 72;

    final public const TYPE_GSK_WORKPLACE = 75;

    final public const TYPE_JOHNSON_AND_JOHNSON_WORKPLACE = 77;

    final public const TYPE_SYNGENTA_WORKPLACE = 76;

    final public const TYPE_NESTLE_WORKPLACE = 78; // Nestle / Nestlé / Nestle Hungary

    final public const TYPE_MAHLE_PL_WORKPLACE = 79; // MAHLE Poland

    final public const TYPE_LPP_WORKPLACE = 80; // LPP SA

    final public const TYPE_AMREST_WORKPLCAE = 88; // AmRest Global (group of companies)

    final public const KUKA_WORKPLACE = 92; // KUKA HUNGÁRIA Kft.

    // Specific
    final public const TYPE_CONSULTATION_NUMBER = 500;

    final public const TYPE_STATUS = 501;

    final public const TYPE_CONSULTATION_DATES = 502;

    final public const TYPE_FIRST_CONSULTATION = 503;

    final public const TYPE_CASE_CLOSED_AT = 504;

    final public const TYPE_CASE_CREATED_AT = 511;

    final public const TYPE_WORKSHOP_NUMBER_OF_PARTICIPANTS = 505;

    final public const TYPE_ORIENTATION_NUMBER_OF_PARTICIPANTS = 506;

    final public const TYPE_PRIZEGAME_NUMBER_OF_PARTICIPANTS = 507;

    final public const TYPE_CRISIS_NUMBER_OF_PARTICIPANTS = 508;

    final public const TYPE_HEALTH_DAY_NUMBER_OF_PARTICIPANTS = 509;

    final public const TYPE_EXPERT_OUTPLACEMENT_NUMBER_OF_PARTICIPANTS = 510;

    // Onsite consultation
    final public const TYPE_ONSITE_CONSULTATION_STATUS = 601;

    final public const TYPE_ONSITE_CONSULTATION_SITE = 602;

    protected $guarded = [];

    public function riport(): BelongsTo
    {
        return $this->belongsTo(Riport::class);
    }

    public function connected_values(): HasMany
    {
        return $this->hasMany(RiportValue::class, 'connection_id', 'connection_id');
    }
}
