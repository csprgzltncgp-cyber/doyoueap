<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessLiveWebinarRecording;
use App\Models\LiveWebinar;
use App\Services\LiveWebinarService;
use App\Services\Zoom\ZoomMeetingService;
use App\Services\Zoom\ZoomSignatureService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class LiveWebinarSessionController extends Controller
{
    public function __construct(
        protected LiveWebinarService $liveWebinarService,
        protected ZoomSignatureService $signatureService,
        protected ZoomMeetingService $zoomMeetingService
    ) {}

    public function signature(Request $request, LiveWebinar $live_webinar): JsonResponse
    {
        $this->authorizeExpert($live_webinar);

        if (! $live_webinar->zoom_meeting_id) {
            $live_webinar = $this->liveWebinarService->sync_zoom_meeting($live_webinar);
        }

        if (! $live_webinar->zoom_meeting_id) {
            throw new Exception('Zoom meeting has not been provisioned.');
        }

        $signature = $this->signatureService->createForHost($live_webinar);

        return response()->json([
            'signature' => $signature['signature'],
            'expires_at' => $signature['expires_at'],
            'sdkKey' => config('services.zoom.sdk_key'),
            'meetingNumber' => $live_webinar->zoom_meeting_id,
            'passcode' => $live_webinar->zoom_passcode,
            'userName' => $request->user()->name,
            'userEmail' => $request->user()->email,
        ]);
    }

    public function end(LiveWebinar $live_webinar): JsonResponse
    {
        $this->authorizeExpert($live_webinar);

        try {
            $this->zoomMeetingService->endMeeting($live_webinar);
        } catch (Throwable $exception) {
            Log::warning('Unable to end Zoom meeting via API', [
                'live_webinar_id' => $live_webinar->id,
                'error' => $exception->getMessage(),
            ]);
        }

        ProcessLiveWebinarRecording::dispatch($live_webinar->id)->delay(now()->addMinutes(30));

        return response()->json([
            'message' => 'Webinar ended successfully.',
        ]);
    }

    protected function authorizeExpert(LiveWebinar $liveWebinar): void
    {
        abort_unless(auth()->id() === $liveWebinar->user_id, 403);
    }
}
