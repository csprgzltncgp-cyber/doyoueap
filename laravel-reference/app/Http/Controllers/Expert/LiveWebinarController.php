<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapLanguage;
use App\Models\LiveWebinar;
use App\Services\LiveWebinarService;
use Illuminate\View\View;

class LiveWebinarController extends Controller
{
    public function __construct(protected LiveWebinarService $live_webinar_service) {}

    public function index(): View
    {
        return view('expert.live-webinar.index', ['live_webinars' => $this->live_webinar_service->get_live_webinars(auth()->user()->id)]);
    }

    public function show(LiveWebinar $live_webinar): View
    {
        $eap_language = EapLanguage::query()->where('id', $live_webinar->language_id)->first();

        return view('expert.live-webinar.show', [
            'live_webinar' => $live_webinar,
            'eap_language' => $eap_language,
            'start_url' => $live_webinar->zoom_meeting_id ? route('expert.live-webinar.start', $live_webinar) : null,
        ]);
    }

    public function start(LiveWebinar $live_webinar): View
    {
        abort_unless(auth()->id() === $live_webinar->user_id, 403);

        if (! $live_webinar->zoom_meeting_id) {
            abort(404);
        }

        $config = [
            'sdkKey' => config('services.zoom.sdk_key'),
            'signatureEndpoint' => route('expert.live-webinar.signature', $live_webinar),
            'endEndpoint' => route('expert.live-webinar.end', $live_webinar),
            'meetingNumber' => $live_webinar->zoom_meeting_id,
            'passcode' => $live_webinar->zoom_passcode,
            'userName' => auth()->user()->name,
            'userEmail' => auth()->user()->email,
            'leaveUrl' => route('expert.live-webinar.index'),
        ];

        return view('expert.live-webinar.start', [
            'live_webinar' => $live_webinar,
            'config' => $config,
        ]);
    }
}
