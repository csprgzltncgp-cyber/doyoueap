<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\CreateSummarizedRiportExport;
use App\Models\Company;
use App\Models\ContractHolder;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RiportExportController extends Controller
{
    public function contract_holder()
    {
        request()->validate([
            'contract_holder_id' => 'required|exists:contract_holders,id',
            'year' => 'required|numeric',
            'month' => 'required|numeric',
        ]);

        $contract_holder = ContractHolder::query()->where('id', request()->input('contract_holder_id'))->first();
        $filename = request()->input('year').'-'.request()->input('month').'.xlsx';
        try {
            return Storage::disk('private')->download(
                'contract-holder-exports/'.$contract_holder->id.'/'.$filename,
                Str::of($contract_holder->name)->lower().'-'.$filename
            );
        } catch (Exception) {
            return redirect()->back()->with('file-not-found', Str::of($contract_holder->name)->lower().'-'.$filename);
        }
    }

    public function download_custom_company_riport()
    {
        request()->validate([
            'company_id' => 'required|exists:companies,id',
            'year' => 'required|numeric',
            'month' => 'required|numeric',
        ]);

        $company = Company::query()->where('id', request()->input('company_id'))->first();
        $filename = request()->input('year').'-'.request()->input('month').'.xlsx';
        try {
            return Storage::disk('private')->download(
                'custom-company-exports/'.$company->id.'/'.$filename,
                Str::of($company->name)->lower().'-'.$filename
            );
        } catch (Exception) {
            return redirect()->back()->with('file-not-found', Str::of($company->name)->lower().'-'.$filename);
        }
    }

    public function download_summarized_riport(string $filename)
    {
        try {
            $file = Storage::disk('private')->download(
                'summarized-riport-exports/'.$filename,
                $filename
            );
        } catch (Exception) {
            abort(404);
        }

        return $file;
    }

    public function company_summarize()
    {
        CreateSummarizedRiportExport::dispatch(request('quarter'), request('company_prefix'), auth()->user()->name, auth()->user()->email);
        session()->flash('riport-export-started', __('common.riport_export_started'));

        return redirect()->back();
    }
}
