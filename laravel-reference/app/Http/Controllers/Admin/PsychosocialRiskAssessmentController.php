<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PsychosocialRiskAssessmentExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Facades\Excel;

class PsychosocialRiskAssessmentController extends Controller implements WithCalculatedFormulas
{
    public function index()
    {
        return view('admin.psychosocial-risk-assessment.list');
    }

    public function download_ivy_summary()
    {
        $answers = DB::table('psychosocial_risk_assessments_ivy')->get(['data', 'Date']);
        if (count($answers) >= 1) {
            return Excel::download(
                new PsychosocialRiskAssessmentExport($answers),
                'riport_ivy.xlsx'
            );
        }

        return view('admin.psychosocial-risk-assessment.list');
    }

    public function download_exxon_summary()
    {
        $answers = DB::table('psychosocial_risk_assessments_exxon')->get(['data', 'Date']);
        if (count($answers) >= 1) {
            return Excel::download(
                new PsychosocialRiskAssessmentExport($answers),
                'riport_exxon.xlsx'
            );
        }

        return view('admin.psychosocial-risk-assessment.list');
    }

    public function download_schott_summary()
    {
        $answers = DB::table('psychosocial_risk_assessments_schott')->get(['data', 'Date']);
        if (count($answers) >= 1) {
            return Excel::download(
                new PsychosocialRiskAssessmentExport($answers),
                'riport_schott.xlsx'
            );
        }

        return view('admin.psychosocial-risk-assessment.list');
    }
}
