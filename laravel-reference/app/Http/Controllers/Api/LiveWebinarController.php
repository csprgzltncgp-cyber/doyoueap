<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LiveWebinarCurrentRequest;
use App\Http\Requests\Api\LiveWebinarIndexRequest;
use App\Http\Resources\LiveWebinarResource;
use App\Models\LiveWebinar;
use App\Services\LiveWebinarService;

class LiveWebinarController extends Controller
{
    public function __construct(protected LiveWebinarService $live_webinar_service) {}

    public function index(LiveWebinarIndexRequest $request)
    {
        return LiveWebinarResource::collection($this->live_webinar_service->get_live_webinars_for_eap_user($request));
    }

    public function show(LiveWebinar $live_webinar)
    {
        return LiveWebinarResource::make($live_webinar);
    }

    public function current(LiveWebinarCurrentRequest $request)
    {
        return LiveWebinarResource::collection($this->live_webinar_service->get_current_live_webinar_for_eap_user($request));
    }
}
