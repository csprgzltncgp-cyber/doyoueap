<?php

namespace App\Models\EapOnline;

use App\Enums\ChatMessageType;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ChatTherapy\ChatMessage
 *
 * @property int $id
 * @property string $room_id
 * @property string $message
 * @property ChatMessageType $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ChatMessage extends Model
{
    protected $connection = 'mysql_eap_online';

    protected $table = 'chat_messages';

    protected $guarded = [];

    protected $casts = [
        'type' => ChatMessageType::class,
    ];

    public function is_from_expert(): bool
    {
        return $this->type === ChatMessageType::EXPERT;
    }

    public function is_from_user(): bool
    {
        return $this->type === ChatMessageType::USER;
    }
}
