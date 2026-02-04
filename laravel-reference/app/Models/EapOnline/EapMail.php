<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\EapOnline\EapMail
 *
 * @property int $id
 * @property string $date
 * @property string $subject
 * @property string $category
 * @property int $user_id
 * @property int $country_id
 * @property string|null $deleted_at
 * @property-read Collection<int, EapMessage> $eap_messages
 * @property-read int|null $eap_messages_count
 * @property-read Collection<int, EapMailNotification> $eap_notifications
 * @property-read int|null $eap_notifications_count
 * @property-read EapUser $eap_user
 * @property-read bool $is_opened
 *
 * @method static Builder|EapMail newModelQuery()
 * @method static Builder|EapMail newQuery()
 * @method static Builder|EapMail query()
 * @method static Builder|EapMail whereCategory($value)
 * @method static Builder|EapMail whereCountryId($value)
 * @method static Builder|EapMail whereDate($value)
 * @method static Builder|EapMail whereDeletedAt($value)
 * @method static Builder|EapMail whereId($value)
 * @method static Builder|EapMail whereSubject($value)
 * @method static Builder|EapMail whereUserId($value)
 *
 * @mixin \Eloquent
 */
class EapMail extends Model
{
    public $timestamps = false;

    protected $connection = 'mysql_eap_online';

    protected $table = 'mails';

    protected $fillable = [];

    protected $appends = ['is_opened'];

    public function getIsOpenedAttribute(): bool
    {
        return $this->is_opened($this->eap_notifications()->get());
    }

    public function eap_user(): BelongsTo
    {
        return $this->belongsTo(EapUser::class, 'user_id', 'id');
    }

    public function eap_messages(): HasMany
    {
        return $this->hasMany(EapMessage::class, 'mail_id', 'id');
    }

    public function eap_notifications(): HasMany
    {
        return $this->hasMany(EapMailNotification::class, 'mail_id', 'id');
    }

    public function is_opened($notifications = null): bool
    {
        if (empty($notifications)) {
            return true;
        }

        foreach ($notifications as $notification) {
            if ($notification->type === 'new_mail_user') {
                return false;
            }
        }

        return true;
    }

    public function first_message(): HasOne
    {
        return $this->hasOne(EapMessage::class, 'mail_id', 'id')->orderBy('id');
    }
}
