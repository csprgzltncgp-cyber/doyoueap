<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\EapOnline\EapMailNotification
 *
 * @property int $id
 * @property int $mail_id
 * @property string $type
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read EapMail|null $eap_mail
 *
 * @method static Builder|EapMailNotification newModelQuery()
 * @method static Builder|EapMailNotification newQuery()
 * @method static Builder|EapMailNotification query()
 * @method static Builder|EapMailNotification whereCreatedAt($value)
 * @method static Builder|EapMailNotification whereId($value)
 * @method static Builder|EapMailNotification whereMailId($value)
 * @method static Builder|EapMailNotification whereType($value)
 * @method static Builder|EapMailNotification whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EapMailNotification extends Model
{
    public $timestamps = false;

    protected $connection = 'mysql_eap_online';

    protected $table = 'mail_notifications';

    protected $fillable = ['type', 'mail_id'];

    public function eap_mail(): BelongsTo
    {
        return $this->belongsTo(EapMail::class, 'mail_id', 'id');
    }
}
