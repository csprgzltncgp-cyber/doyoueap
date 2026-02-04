<?php

namespace App\Traits;

use App\Exports\LifeworksCaseExport;
use App\Mail\LifeWorksCaseEmail;
use App\Mail\LifeWorksCasePasswordEmail;
use App\Models\Cases;
use App\Models\CloseTelusCase;
use App\Models\Company;
use App\Models\TelusCaseCode;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

trait SendMailToLifeworksTrait
{
    public function send_mail_to_lifeworks(Request $request)
    {
        $case = Cases::query()->where('id', $request->input('case_id'))->first();

        if (! $case) {
            return response()->json(['status' => 1]);
        }

        try {
            $code = Str::random();

            if (! file_exists(storage_path('app/lifeworks-cases'))) {
                mkdir(storage_path('app/lifeworks-cases'), 0777, true);
            }

            $file_path = storage_path('app/lifeworks-cases/case-'.$case->case_identifier.'.xlsx');
            $company = Company::query()->find($case->company_id);

            $case_code = TelusCaseCode::query()->create([
                'case_id' => $case->id,
                'code' => $code,
                'file' => $file_path,
            ]);

            Excel::store(new LifeworksCaseExport($case), '/lifeworks-cases/case-'.$case->case_identifier.'.xlsx', 'private');

            Mail::to('globalservices@intake.telushealth.com')->send(new LifeWorksCaseEmail($case_code, $company->name));
            Mail::to('globalservices@intake.telushealth.com')->send(new LifeWorksCasePasswordEmail($case_code, $company->name));

            $case->employee_contacted_at = Carbon::now('Europe/Budapest');
            $case->customer_satisfaction = 10;

            // IF case is not psychological than set closed status
            if ((int) $case->case_type->value !== 1) {
                $case->status = 'confirmed';
                $case->confirmed_by = Auth::id();
                $case->confirmed_at = Carbon::now('Europe/Budapest');
                $case->employee_contacted_at = Carbon::now('Europe/Budapest');
                $case->customer_satisfaction = 10;
            }

            $case->save();

            // Store when the case can be closed
            CloseTelusCase::query()->create([
                'case_id' => $case->id,
                'closeable_after' => Carbon::now()->addDays(40),
            ]);

        } catch (Exception $e) {

            Log::info('Sending mail to LifeWorks and confirming case #ID: '.$case->id.' FAILED!. ERROR: '.$e->getMessage());

            return response()->json(['status' => 1]);
        }

        return response()->json(['status' => 0]);
    }
}
