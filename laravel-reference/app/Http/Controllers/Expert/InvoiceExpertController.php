<?php

namespace App\Http\Controllers\Expert;

use App\Models\AdditionalInvoiceItem;
use App\Models\CustomInvoiceItem;
use App\Models\ExpertConsultationCount;
use App\Models\Invoice;
use App\Models\InvoiceCaseData;
use App\Models\InvoiceCrisisData;
use App\Models\InvoiceEvent;
use App\Models\InvoiceLiveWebinarData;
use App\Models\InvoiceOtherActivityData;
use App\Models\InvoiceWorkshopData;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvoiceExpertController extends BaseExpertController
{
    public function index()
    {
        $invoices = Auth::user()->invoices->load('events')->sortByDesc('id');

        return view('expert.invoices.index', ['invoices' => $invoices]);
    }

    public function main()
    {
        return view('expert.invoices.main');
    }

    public function infos()
    {
        return view('expert.invoices.infos');
    }

    public function view($id)
    {
        $invoice = Invoice::query()->findOrFail($id)->load('data_changes');

        $invoice_case_datas = InvoiceCaseData::query()->where('invoice_id', $id)
            ->get()
            ->groupBy('permission_id')
            ->mapWithKeys(fn ($values, $permission_id) => $permission_id === 0 || ($permission_id === '' || $permission_id === '0') ? [__('invoice.uncategorized') => $values] : [$permission_id => $values]);

        $case_data_periods = $invoice_case_datas
            ->map(fn ($cases, $permission_id): array => [
                'permission_id' => $permission_id,
                'permission_name' => Permission::query()->find($permission_id)?->translation->value ?? __('invoice.uncategorized'),
                'period' => $cases->map(fn ($case) => Str::title(Carbon::parse($case->created_at)->translatedFormat('F')))->unique(),
            ]);

        $workshop_case_datas = InvoiceWorkshopData::query()->where('invoice_id', $id)->get();
        $workshop_data_periods = $workshop_case_datas->map(fn ($case) => Str::title(Carbon::parse($case->created_at)->translatedFormat('F')))->unique();

        $crisis_case_datas = InvoiceCrisisData::query()->where('invoice_id', $id)->get();
        $crisis_data_periods = $crisis_case_datas->map(fn ($case) => Str::title(Carbon::parse($case->created_at)->translatedFormat('F')))->unique();

        $other_activity_case_datas = InvoiceOtherActivityData::query()->where('invoice_id', $id)->get();
        $other_activity_data_periods = $other_activity_case_datas->map(fn ($case) => Str::title(Carbon::parse($case->created_at)->translatedFormat('F')))->unique();

        $live_webinar_case_datas = InvoiceLiveWebinarData::query()->where('invoice_id', $id)->get();
        $live_webinar_data_periods = $live_webinar_case_datas->map(fn ($case) => Str::title(Carbon::parse($case->created_at->subMonthWithNoOverflow())->translatedFormat('F')))->unique();

        $additional_items = AdditionalInvoiceItem::query()->where('invoice_id', $invoice->id)->get();

        $custom_items = CustomInvoiceItem::query()->where('user_id', auth()->user()->id)->get();

        $consultation_count = ExpertConsultationCount::query()
            ->where('month', Carbon::parse($invoice->date_of_issue)->subMonthNoOverflow()->format('Y-m'))
            ->where('user_id', $invoice->user_id)
            ->first();

        if ($invoice->user_id != Auth::user()->id) {
            abort(403);
        }

        return view('expert.invoices.view', [
            'invoice' => $invoice,
            'invoice_case_datas' => $invoice_case_datas,
            'case_data_periods' => $case_data_periods,
            'workshop_case_datas' => $workshop_case_datas,
            'workshop_data_periods' => $workshop_data_periods,
            'crisis_case_datas' => $crisis_case_datas,
            'crisis_data_periods' => $crisis_data_periods,
            'other_activity_case_datas' => $other_activity_case_datas,
            'other_activity_data_periods' => $other_activity_data_periods,
            'live_webinar_case_datas' => $live_webinar_case_datas,
            'live_webinar_data_periods' => $live_webinar_data_periods,
            'additional_items' => $additional_items,
            'custom_items' => $custom_items,
            'consultation_count' => $consultation_count,
        ]);
    }

    public function downloadInvoice($id)
    {
        $invoice = Invoice::query()->find($id);
        if (! $invoice || $invoice->user_id != Auth::user()->id) {
            abort(403);
        }

        return redirect($invoice->url);
    }

    public function deleteInvoice($id)
    {
        $invoice = Invoice::query()->findOrFail($id);
        if ($invoice->user_id == Auth::user()->id) {
            $invoice->deleted_by_expert_at = Carbon::now();
            $invoice->save();

            InvoiceCaseData::query()->where('invoice_id', $id)->update([
                'invoice_id' => null,
            ]);

            InvoiceWorkshopData::query()->where('invoice_id', $id)->update([
                'invoice_id' => null,
            ]);

            InvoiceCrisisData::query()->where('invoice_id', $id)->update([
                'invoice_id' => null,
            ]);

            InvoiceOtherActivityData::query()->where('invoice_id', $id)->update([
                'invoice_id' => null,
            ]);

            InvoiceLiveWebinarData::query()->where('invoice_id', $id)->update([
                'invoice_id' => null,
            ]);

            return response()->json(['status' => 0]);
        }

        return response()->json(['status' => 1]);
    }

    public function createEvent($id, Request $request)
    {
        InvoiceEvent::query()->create([
            'invoice_id' => $id,
            'event' => $request->event,
        ]);

        if ($request->event == 'invoice_payment_sent') {
            $invoice = Invoice::query()->findOrFail($id);
            $invoice->status = 'listed_in_a_bank';
            $invoice->save();
        }

        return response()->json(['status' => 0]);
    }

    public function deleteEvent($id, Request $request)
    {
        InvoiceEvent::query()->where('invoice_id', $id)->where('event', $request->event)->latest()->first()->delete();

        return response()->json(['status' => 0]);
    }

    public function addCaseToInvoice(Request $request)
    {
        $isAddable = Invoice::addCaseToInvoice($request->case, $request->invoice);

        return response()->json([
            'status' => $isAddable,
            'msg' => $isAddable ? __('common.case-id-successfully-added') : __('common.wrong-case-id'),
        ]);
    }

    public function save_hourly_rate()
    {
        request()->validate([
            'currency' => 'required|max:3',
            'hourly_rate_50' => 'required',
            'hourly_rate_30' => 'sometimes|required',
        ]);

        auth()->user()->invoice_datas()->updateOrCreate([
            'user_id' => auth()->user()->id,
        ], [
            'currency' => request('currency'),
            'hourly_rate_50' => (int) request('hourly_rate_50'),
        ]);

        if (request()->has('hourly_rate_30')) {
            auth()->user()->invoice_datas()->update([
                'hourly_rate_30' => ((int) request('hourly_rate_30') == 0) ? (int) request('hourly_rate_50') : (int) request('hourly_rate_30'),
            ]);
        }

        return response()->json(['status' => 0]);
    }
}
