<?php

namespace App\Models\EapOnline;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapMessage
 *
 * @property int $id
 * @property int $mail_id
 * @property int|null $user_id
 * @property string $message
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, EapMessageAttachment> $eap_attachments
 * @property-read int|null $eap_attachments_count
 * @property-read EapMail|null $eap_mail
 *
 * @method static Builder|EapMessage newModelQuery()
 * @method static Builder|EapMessage newQuery()
 * @method static Builder|EapMessage query()
 * @method static Builder|EapMessage whereCreatedAt($value)
 * @method static Builder|EapMessage whereDeletedAt($value)
 * @method static Builder|EapMessage whereId($value)
 * @method static Builder|EapMessage whereMailId($value)
 * @method static Builder|EapMessage whereMessage($value)
 * @method static Builder|EapMessage whereType($value)
 * @method static Builder|EapMessage whereUpdatedAt($value)
 * @method static Builder|EapMessage whereUserId($value)
 *
 * @mixin \Eloquent
 */
class EapMessage extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'messages';

    protected $fillable = [
        'message',
        'type',
        'user_id',
    ];

    public function eap_mail(): BelongsTo
    {
        return $this->belongsTo(EapMail::class, 'mail_id', 'id');
    }

    public function eap_attachments(): HasMany
    {
        return $this->hasMany(EapMessageAttachment::class, 'message_id', 'id');
    }

    public function sender()
    {
        if ($this->type === 'user') {
            return EapUser::query()->find($this->user_id);
        }

        return User::query()->find($this->user_id);
    }
}
