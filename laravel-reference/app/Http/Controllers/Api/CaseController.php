<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cases;
use App\Traits\CaseCloseTrait;
use App\Traits\EapOnline\OnlineTherapyTrait;
use App\Traits\SendMailToLifeworksTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    // Contains the function for EAP API requests
    use CaseCloseTrait;
    use OnlineTherapyTrait;
    use SendMailToLifeworksTrait;

    public function get_available_experts()
    {
        return query_available_experts(
            is_crisis: request()->input('is_crisis'),
            permission_id: request()->input('permission_id'),
            country_id: request()->input('country_id'),
            city_id: request()->input('city_id'),
            specialization_id: request()->input('specialization_id'),
            language_skill_id: request()->input('language_skill_id'),
            consultation_minute: request()->input('consultation_minute'),
            is_personal: request()->input('is_personal'),
            case: request()->input('case'),
            skip_ids: request()->input('skip_ids'),
            company_id: request()->input('company_id'),
            problem_details: request()->input('problem_details_id'),
            ignore_language_skill: request()->input('ignore_language_skill'),
            ignore_case_limit: request()->input('ignore_case_limit')
        );
    }

    public function mail_to_lifeworks(Request $request)
    {
        return $this->send_mail_to_lifeworks($request);
    }

    public function set_eap_case_feedback(Request $request): bool
    {
        $updated_row = Cases::query()->where('id', $request->case_id)->update(['customer_satisfaction' => $request->rating]);

        return $updated_row >= 1;
    }

    public function case_interrupted(): JsonResponse
    {
        request()->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'minka_id' => ['required', 'exists:users,id'],
        ]);

        return $this->interrupt_case(request()->input('case_id'), request()->input('minka_id'));
    }
}
