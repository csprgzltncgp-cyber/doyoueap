<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Enums\ChatMessageType;
use App\Events\ChatTherapyEnded;
use App\Events\ChatTherapyMessageCreated;
use App\Events\ExpertStartedTyping;
use App\Http\Controllers\Controller;
use App\Models\EapOnline\ChatMessage;
use App\Models\EapOnline\ChatNotification;
use App\Models\EapOnline\EapLanguageLines;
use App\Models\EapOnline\EapUser;
use App\Notifications\EapOnline\EapChatMessageCreated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index($client_id, $room_id)
    {
        $client = DB::connection('mysql_eap_online')->table('users')->where('id', $client_id)->first();

        // Online
        $booking = DB::connection('mysql_eap_online')->table('online_appointment_bookings')->where('room_id', $room_id)->first();
        if ($booking) {
            $messages = ChatMessage::query()->where('room_id', $booking->room_id)->latest()->get();
        } else {
            $booking = DB::connection('mysql_eap_online')->table('intake_bookings')->where('room_id', $room_id)->first();
            $messages = ChatMessage::query()->where('room_id', $booking->room_id)->latest()->get();
        }

        return view('admin.eap-online.chat.index', ['client' => $client, 'room_id' => $room_id, 'messages' => $messages]);
    }

    public function store(): void
    {
        request()->validate([
            'message' => 'required|string|max:750',
            'room_id' => 'required|string|max:255',
            'users_count' => 'required|integer',
        ]);

        $online_booking = DB::connection('mysql_eap_online')->table('online_appointment_bookings')->where('room_id', request('room_id'))->first();
        $intake_booking = DB::connection('mysql_eap_online')->table('intake_bookings')->where('room_id', request('room_id'))->first();

        $chat_messaage_data = [
            'message' => request('message'),
            'type' => ChatMessageType::EXPERT,
        ];

        $user_id = null;

        if ($online_booking) {
            $chat_messaage_data['room_id'] = $online_booking->room_id;
            $user_id = $online_booking->user_id;
        }

        if ($intake_booking) {
            $chat_messaage_data['room_id'] = $intake_booking->room_id;
            $user_id = $intake_booking->user_id;
        }

        if ($user_id) {
            $eap_user = EapUser::query()->find($user_id);
            $language_line = EapLanguageLines::query()->where('key', 'new_expert_message_notification')->first();
            $message = data_get($language_line->text, ($eap_user->language) ? $eap_user->language->code : 'en');

            if ($message) {
                $eap_user->notify(new EapChatMessageCreated($message));
            }
        }

        $chat_message = ChatMessage::query()->create($chat_messaage_data);

        if (request('users_count') <= 1) {
            ChatNotification::query()->create(['chat_message_id' => $chat_message->id]);
        }

        broadcast(new ChatTherapyMessageCreated(request('message'), request('room_id')))->toOthers();
    }

    public function end_therapy(Request $request): void
    {
        $room_id = $request->input('room_id');

        broadcast(new ChatTherapyEnded(request('room_id')))->toOthers();

        $online_booking = DB::connection('mysql_eap_online')->table('online_appointment_bookings')->where('room_id', $room_id)->first();
        if ($online_booking) {

            DB::connection('mysql_eap_online')->table('online_appointment_bookings')->where('room_id', $room_id)->update([
                'consultation_end' => Carbon::now(),
            ]);

            DB::connection('mysql_eap_online')->table('chat_messages')->where('room_id', $room_id)->delete();
        }

        $intake_booking = DB::connection('mysql_eap_online')->table('intake_bookings')->where('room_id', $room_id)->first();
        if ($intake_booking) {
            DB::connection('mysql_eap_online')->table('intake_bookings')->where('room_id', $room_id)->update([
                'consultation_end' => Carbon::now(),
            ]);
            DB::connection('mysql_eap_online')->table('chat_messages')->where('room_id', $room_id)->delete();
        }
    }

    public function typing(): void
    {
        request()->validate([
            'room_id' => 'required|string|max:255',
        ]);

        broadcast(new ExpertStartedTyping(request('room_id')))->toOthers();
    }
}
