<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\VolumeRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\View\View;

class VolumeRequestController extends Controller
{
    public function show(?string $date = null): View
    {
        // IF it is currently the first month of the year (january), set the default date to the last month of thre previous year
        $default_date = (Carbon::now()->month === 1 && Carbon::now()->year > 2024) ? Carbon::now()->subYear()->endOfYear()->startOfMonth() : Carbon::now()->subMonth()->startOfMonth();

        $date = $date ? Carbon::parse($date)->startOfMonth() : $default_date;

        $year = Carbon::parse($date)->year; // Get year from date
        $dates = CarbonPeriod::create(Carbon::now()->setYear($year)->startOfyear()->format('Y-m-d'), '1 month', Carbon::now()->setYear($year)->endOfYear()->startOfMonth()->format('Y-m-d'));

        $company = auth()->user()->companies()->first();

        $volumes = $company
            ->invoice_items()
            ->when(optional($company->country_differentiates)->invoicing, function ($query): void {
                $query->where('country_id', auth()->user()->country_id);
            })
            ->whereHas('volume')
            ->get()
            ->pluck('volume');

        $volume_requests = VolumeRequest::query()
            ->whereIn('volume_id', $volumes->pluck('id'))
            ->whereBetween('date', [Carbon::now()->setYear($year)->startOfYear(), Carbon::now()->setYear($year)->endOfYear()])
            ->orderBy('date')
            ->get();

        $volume_requests_in_year = $volume_requests->pluck('date')->toArray();
        $volume_requests = $volume_requests->where('date', Carbon::parse($date));

        return view('client.volume-request', ['company' => $company, 'dates' => collect($dates), 'volume_requests' => $volume_requests, 'selected_date' => $date, 'volume_requests_in_year' => $volume_requests_in_year]);
    }
}
