<?php

namespace App\Models\EapOnline;

use App\Models\Company;
use App\Models\Country;
use App\Models\EapOnline\Statistics\EapAssessment;
use App\Models\EapOnline\Statistics\EapLogin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

/**
 * App\Models\EapOnline\EapUser
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property int $company_id
 * @property int $country_id
 * @property string|null $last_online_therapy
 * @property int|null $case_id
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Country $country
 * @property-read Collection<int, EapAssessment> $eap_assessment_statistics
 * @property-read int|null $eap_assessment_statistics_count
 * @property-read Collection<int, EapLogin> $eap_login_statistics
 * @property-read int|null $eap_login_statistics_count
 * @property-read Collection<int, EapMail> $eap_mails
 * @property-read int|null $eap_mails_count
 *
 * @method static Builder|EapUser newModelQuery()
 * @method static Builder|EapUser newQuery()
 * @method static Builder|EapUser query()
 * @method static Builder|EapUser whereCaseId($value)
 * @method static Builder|EapUser whereCompanyId($value)
 * @method static Builder|EapUser whereCountryId($value)
 * @method static Builder|EapUser whereCreatedAt($value)
 * @method static Builder|EapUser whereEmail($value)
 * @method static Builder|EapUser whereEmailVerifiedAt($value)
 * @method static Builder|EapUser whereId($value)
 * @method static Builder|EapUser whereLastVideoTherapy($value)
 * @method static Builder|EapUser wherePassword($value)
 * @method static Builder|EapUser whereUpdatedAt($value)
 * @method static Builder|EapUser whereUsername($value)
 *
 * @mixin \Eloquent
 */
class EapUser extends Model
{
    use Notifiable;

    protected $connection = 'mysql_eap_online';

    protected $table = 'users';

    protected $fillable = [];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::deleting(function (self $user): void {
            if ($user->eap_login_statistics()->count() > 0) {
                foreach ($user->eap_login_statistics()->get() as $stat) {
                    $stat->delete();
                }
            }
        });
    }

    public function getEmailAttribute(string $email): string
    {
        return Crypt::decrypt($email);
    }

    public function routeNotificationForPusherPushNotifications($notification): string
    {
        return 'eapchat.user.'.$this->id;
    }

    public function company(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Country::class);
    }

    public function eap_mails(): HasMany
    {
        return $this->hasMany(EapMail::class);
    }

    public function eap_login_statistics(): HasMany
    {
        return $this->setConnection('mysql_eap_online')->hasMany(EapLogin::class, 'user_id');
    }

    public function eap_assessment_statistics(): HasMany
    {
        return $this->setConnection('mysql_eap_online')->hasMany(EapAssessment::class, 'user_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(EapLanguage::class);
    }
}
