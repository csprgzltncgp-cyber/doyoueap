<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Rest\Client;

class VideoChatController extends Controller
{
    public function index($client_id, $room_id)
    {
        $client = DB::connection('mysql_eap_online')->table('users')->where('id', $client_id)->first();

        return view('admin.eap-online.video_chat.index', ['client' => $client, 'room_id' => $room_id]);
    }

    public function token(Request $request)
    {
        $request->validate([
            'room_id' => 'required|string',
        ]);

        $client = new Client(config('twilio.twilio.sid'), config('twilio.twilio.auth_token'));
        $identity = 'expert-'.auth()->id();
        $room_id = $request->input('room_id');

        $room_list = $client->video->v1->rooms->read([
            'uniqueName' => $room_id,
            'type' => 'go',
        ]);

        if (empty($room_list)) {
            $client->video->v1->rooms->create([
                'uniqueName' => $room_id,
                'type' => 'go',
                'unusedRoomTimeout' => 5,
                'maxParticipants' => 2,
            ]);
        }

        $grant = new VideoGrant;
        $grant->setRoom($room_id);

        $token = new AccessToken(
            config('twilio.twilio.sid'),
            config('twilio.twilio.key'),
            config('twilio.twilio.secret'),
            3600,
            $identity,
        );

        $token->addGrant($grant);

        return response()->json([
            'identity' => $identity,
            'token' => $token->toJWT(),
        ]);
    }
}
