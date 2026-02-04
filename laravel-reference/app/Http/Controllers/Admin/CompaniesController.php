<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseInput;
use App\Models\CaseInputValue;
use App\Models\Company;
use App\Models\ContractHolder;
use App\Models\Language;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompaniesController extends Controller
{
    public function index()
    {
        return view('admin.companies.list');
    }

    public function permissions()
    {
        return view('admin.companies.permissions');
    }

    public function permission_edit($id)
    {
        $company = Company::query()->findOrFail($id);
        $permissions = Permission::all();

        return view('admin.companies.permissions_edit', ['permissions' => $permissions, 'company' => $company]);
    }

    public function permission_edit_process($id, Request $request)
    {

        Company::query()->findOrFail($id);

        DB::table('permission_x_company')->where('company_id', $id)->delete();

        foreach ($request->input('permission_id') as $key => $value) {
            $temp = [];
            $temp['permission_id'] = $value;
            $temp['company_id'] = $id;
            $temp['number'] = $request->input('number')[$key];
            $temp['duration'] = $request->input('duration')[$key];
            $temp['contact'] = $request->input('contact')[$key];

            DB::table('permission_x_company')->insert($temp);
        }

        return redirect()->route('admin.companies.permissions.edit', ['id' => $id]);
    }

    public function delete($company_id)
    {
        $company = Company::query()->findOrFail($company_id);
        $company->delete();

        return response(['status' => 0]);
    }

    public function input_values($id, $company_id)
    {
        $input = CaseInput::query()->findOrFail($id);
        $languages = Language::query()->get();
        $permissions = Permission::query()->get();
        $contract_holders = ContractHolder::query()->get();

        return view('admin.companies.input_values', ['input' => $input, 'languages' => $languages, 'permissions' => $permissions, 'contract_holders' => $contract_holders]);
    }

    public function input_values_process($input_id, $company_id, Request $request)
    {
        CaseInputValue::edit($input_id, $request);
        session()->flash('companyInputValuesSaved', true);

        return redirect()->route('admin.companies.inputs', ['company' => $company_id]);
    }
}
