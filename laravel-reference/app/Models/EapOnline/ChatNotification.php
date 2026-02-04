<?php

namespace App\Models\EapOnline;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ChatTherapy\ChatMessage
 *
 * @property int $id
 * @property int $chat_message_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ChatNotification extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'chat_notifications';

    protected $fillable = [
        'chat_message_id',
    ];
}
