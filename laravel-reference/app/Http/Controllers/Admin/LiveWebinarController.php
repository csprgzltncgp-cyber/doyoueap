<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveWebinar;
use App\Services\LiveWebinarService;
use Illuminate\View\View;

class LiveWebinarController extends Controller
{
    public function __construct(protected LiveWebinarService $live_webinar_service) {}

    public function index(): View
    {
        return view('admin.live-webinar.index', $this->live_webinar_service->get_index_categories());
    }

    public function create(): View
    {
        return view('admin.live-webinar.create');
    }

    public function show(LiveWebinar $live_webinar): View
    {
        return view('admin.live-webinar.show', ['live_webinar' => $live_webinar]);
    }

    public function delete(LiveWebinar $live_webinar): void
    {
        $this->live_webinar_service->delete_live_webinar($live_webinar);
    }
}
