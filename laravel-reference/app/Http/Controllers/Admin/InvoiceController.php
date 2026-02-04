<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdditionalInvoiceItem;
use App\Models\CustomInvoiceItem;
use App\Models\ExpertConsultationCount;
use App\Models\Invoice;
use App\Models\InvoiceCaseData;
use App\Models\InvoiceCrisisData;
use App\Models\InvoiceEvent;
use App\Models\InvoiceOtherActivityData;
use App\Models\InvoiceWorkshopData;
use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::getList($request->search);

        $all_monts = [];
        $all_years = [];

        foreach ($invoices as $invoice) {
            $month = substr((string) $invoice->date_of_issue, 0, -3);
            $all_monts[] = $month;

            $years = substr((string) $invoice->date_of_issue, 0, -6);
            $all_years[] = $years;
        }

        $filtered_months = array_unique($all_monts);
        rsort($filtered_months);

        $filtered_years = array_unique($all_years);
        rsort($filtered_years);

        return view('admin.invoices.index', ['invoices' => $invoices, 'filtered_months' => $filtered_months, 'filtered_years' => $filtered_years]);
    }

    public function get_invoices(Request $request)
    {
        $take = 10;
        $skip = ($request->page - 1) * $take;
        $invoices = Invoice::query()
            ->whereBetween('date_of_issue', [
                Carbon::parse(Str::replace('_', '-', $request->date))->startOfMonth(),
                Carbon::parse(Str::replace('_', '-', $request->date))->endOfMonth(),
            ])->orderByDesc('date_of_issue')->get();

        if (! filter_var($request->all, FILTER_VALIDATE_BOOLEAN)) {
            $invoices = $invoices->slice($skip, $take);
        } else {
            $invoices = $invoices->slice($skip);
        }

        $views = collect([]);

        foreach ($invoices as $invoice) {
            $views->push(
                view(
                    'components.invoices.admin_invoice_component',
                    [
                        'invoice' => $invoice,
                        'event' => $invoice->last_event(),
                    ]
                )->render()
            );
        }

        return response()->json(['html' => $views]);
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

        $additional_items = AdditionalInvoiceItem::query()->where('invoice_id', $invoice->id)->get();

        $custom_items = CustomInvoiceItem::query()->where('user_id', $invoice->user_id)->get();

        $consultation_count = ExpertConsultationCount::query()
            ->where('month', Carbon::parse($invoice->date_of_issue)->subMonthNoOverflow()->format('Y-m'))
            ->where('user_id', $invoice->user_id)
            ->first();

        return view('admin.invoices.view', [
            'invoice' => $invoice,
            'invoice_case_datas' => $invoice_case_datas,
            'case_data_periods' => $case_data_periods,
            'workshop_data_periods' => $workshop_data_periods,
            'crisis_data_periods' => $crisis_data_periods,
            'other_activity_data_periods' => $other_activity_data_periods,
            'workshop_case_datas' => $workshop_case_datas,
            'crisis_case_datas' => $crisis_case_datas,
            'other_activity_case_datas' => $other_activity_case_datas,
            'additional_items' => $additional_items,
            'custom_items' => $custom_items,
            'consultation_count' => $consultation_count,
        ]);
    }

    public function downloadInvoice($id)
    {
        $invoice = Invoice::query()->findOrFail($id);

        if (! $invoice->downloaded_by) {
            $invoice->downloaded_by = Auth::user()->id;
            $invoice->downloaded_at = Carbon::now();
        }
        $invoice->save();

        return redirect($invoice->url);
    }

    public function setStatus($id, Request $request)
    {
        $invoice = Invoice::query()->findOrFail($id);
        $invoice->status = $request->status;
        $invoice->save();

        return response()->json(['status' => 0, 'invoice' => $invoice]);
    }

    public function filter()
    {
        $experts = User::query()->where('type', 'expert')->orderBy('name', 'asc')->whereHas('invoices')->get();
        $invoices = Invoice::query()->orderBy('name', 'asc')->get();

        return view('admin.invoices.filter', ['experts' => $experts, 'invoices' => $invoices]);
    }

    public function filterResult(Request $request)
    {
        $invoices = Invoice::filter($request->all());

        return view('admin.invoices.result', ['invoices' => $invoices]);
    }

    public function deleteInvoice($id)
    {
        Invoice::query()->where('id', $id)->delete();

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

        return response()->json(['status' => 0]);
    }

    public function revertInvoicePaidStatus($id)
    {
        InvoiceEvent::query()->where('invoice_id', $id)->where('event', 'invoice_paid')->orderBy('id', 'desc')->first()->delete();

        return response()->json(['status' => 0]);
    }

    public function toggleInvoiceSeenStatus($id)
    {
        $invoice = Invoice::query()->findOrFail($id);
        $invoice->seen = ! $invoice->seen;
        $invoice->save();

        return response()->json([
            'status' => 0,
            'seen' => $invoice->seen,
        ]);
    }
}
