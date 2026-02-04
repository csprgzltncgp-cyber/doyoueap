<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\EapOnline\EapMessageAttachment
 *
 * @property int $id
 * @property int $message_id
 * @property string $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read EapMessage|null $eap_message
 *
 * @method static Builder|EapMessageAttachment newModelQuery()
 * @method static Builder|EapMessageAttachment newQuery()
 * @method static Builder|EapMessageAttachment query()
 * @method static Builder|EapMessageAttachment whereCreatedAt($value)
 * @method static Builder|EapMessageAttachment whereDeletedAt($value)
 * @method static Builder|EapMessageAttachment whereId($value)
 * @method static Builder|EapMessageAttachment whereMessageId($value)
 * @method static Builder|EapMessageAttachment whereUpdatedAt($value)
 * @method static Builder|EapMessageAttachment whereUrl($value)
 *
 * @mixin \Eloquent
 */
class EapMessageAttachment extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'message_attachments';

    protected $guarded = [];

    public function eap_message(): BelongsTo
    {
        return $this->belongsTo(EapMessage::class, 'message_id', 'id');
    }
}
