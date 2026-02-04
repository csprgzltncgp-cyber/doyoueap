<?php

namespace App\Traits;

use App\Mail\Lpp\CustomerSatisfactionEmail as LppCustomerSatisfactionEmail;
use App\Mail\Prezero\CustomerSatisfactionEmail as PrezeroCustomerSatisfactionEmail;
use App\Mail\Pulso\OuttakeEmail;
use App\Models\CaseInput;
use App\Models\Cases;
use App\Models\InvoiceCaseData;
use App\Traits\EapOnline\OnlineTherapyTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

trait CaseCloseTrait
{
    use OnlineTherapyTrait;

    private function lpp_customer_satisfaction($case): void
    {
        $email_input_id = CaseInput::query()->where('default_type', 'client_email')->select('id')->first();
        $email = $case->values->where('case_input_id', $email_input_id->id)->first()->value;

        $phone = $case->values->where('case_input_id', 17)->first()->value;

        $language_input_id = CaseInput::query()->where('id', 65)->first();
        $language_value_id = optional($case->values->where('case_input_id', $language_input_id->id)->first())->value;

        $language = get_country_code_from_client_language($language_value_id);

        // Mobile number transformation
        $mobile_number = str_replace([' ', '/', '-', '(', ')', '#', '.'], ['', '', '', '', '', '', ''], (string) $phone);

        if (str_starts_with($mobile_number, '+')) {
            $mobile_number = substr($mobile_number, 1);
        }

        if (str_starts_with($mobile_number, '00')) {
            $mobile_number = substr($mobile_number, 2);
        }

        if ((in_array(substr($mobile_number, 0, 2), ['20', '30', '31', '50', '70'])) && strlen($mobile_number) == 9) {
            $mobile_number = '36'.$mobile_number;
        }

        if ((in_array(substr($mobile_number, 0, 4), ['0620', '0630', '0631', '0650', '0670'])) && strlen($mobile_number) == 11) {
            $mobile_number = '36'.substr($mobile_number, 2);
        }

        // Validate client phone number
        if (filter_var($mobile_number, FILTER_SANITIZE_NUMBER_INT)) {
            // $this->send_lpp_customer_satisfaction_sms($mobile_number, $language, $case);
        }
        // Validate client email address
        elseif (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->send_lpp_customer_satisfaction_email($email, $language, $case);
        }
    }

    private function send_lpp_customer_satisfaction_email($email, $language, $case): void
    {
        try {
            Mail::to($email)->send(new LppCustomerSatisfactionEmail($language, $case->case_identifier));
        } catch (Exception $e) {
            Log::error('Error sending customer satisfaction email: '.$e->getMessage());
        }
    }

    private function send_lpp_customer_satisfaction_sms($phone, $language, $case): void
    {
        try {
            $basic = new Basic(config('services.vonage.key'), config('services.vonage.secret'));
            $client = new Client($basic);

            if ($language == 'hu') {
                $message = 'Az elmúlt napok során igénybe vetted tanácsadói programunkat. Bízunk benne, hogy szakértőink támogatásával sikerült megoldanod az életedben felmerülő problémát. Kérjük, kattints az alábbi linkre és osszd meg velünk mennyire vagy elégedett a tanácsadással. Köszönjük közreműködésedet! https://www.satisfaction.24eap.com/'.$case->case_identifier.' Számíthatsz Ránk!';
            } elseif ($language == 'pl') {
                $message = 'W ciągu ostatnich kilku dni korzystałeś/korzystałaś z naszego programu wsparcia. Mamy nadzieję, że dzięki wsparciu naszych ekspertów udało Ci się rozwiązać problem w Twoim życiu. Kliknij na poniższy link i daj nam znać, jak jesteś zadowolony/zadowolona z doradztwa. Dziękujemy za pomóc! https://www.satisfaction.24eap.com/'.$case->case_identifier.' Możesz na nas liczyć!';
            } else {
                $message = 'You used our counseling service in the last few days. We hope that with the support of our experts, you\'ve been able to solve your problem in your life. Please click on the link below and let us know how satisfied you are with the counselling. Thank you for your help! https://www.satisfaction.24eap.com/'.$case->case_identifier.' You can count on us!';
            }

            $response = $client->sms()->send(
                new SMS($phone, config('services.vonage.sms_from'), $message, 'unicode')
            );
        } catch (Exception $e) {
            Log::error('Error sending customer satisfaction sms: '.$e->getMessage());
        }
    }

    private function prezero_iberia_customer_satisfaction($case): void
    {
        $email_input_id = CaseInput::query()->where('default_type', 'client_email')->select('id')->first();
        $email = $case->values->where('case_input_id', $email_input_id->id)->first()->value;

        $language_input_id = CaseInput::query()->where('id', 65)->first();
        $language_value_id = optional($case->values->where('case_input_id', $language_input_id->id)->first())->value;

        $language = get_country_code_from_client_language($language_value_id);

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->send_prezero_iberia_customer_satisfaction_email($email, $language, $case);
        }
    }

    private function send_prezero_iberia_customer_satisfaction_email($email, $language, $case): void
    {
        try {
            Mail::to($email)->send(new PrezeroCustomerSatisfactionEmail($language, $case->case_identifier));
        } catch (Exception $e) {
            Log::error('Error sending customer satisfaction email: '.$e->getMessage());
        }
    }

    private function send_pulso_outtake_email($case): void
    {
        try {
            $email_input_id = CaseInput::query()->where('default_type', 'client_email')->select('id')->first();
            $email = $case->values->where('case_input_id', $email_input_id->id)->first()->value;

            $language_input_id = CaseInput::query()->where('id', 65)->first();
            $language_value_id = optional($case->values->where('case_input_id', $language_input_id->id)->first())->value;

            $language = get_country_code_from_client_language($language_value_id);

            // Ab Inbev - cz, ua
            if ((int) $case->company_id == 199 && in_array((int) $case->country_id, [3, 20])) {
                Mail::to($email)->send(new OuttakeEmail($language, $case->case_identifier));
            }

            // EUROFIT GROUP - hu, sk
            if ((int) $case->company_id == 621 && in_array((int) $case->country_id, [1, 4])) {
                Mail::to($email)->send(new OuttakeEmail($language, $case->case_identifier));
            }

            // UCB - pl, bg, ro, hu, sk, cz
            if ((int) $case->company_id == 705 && in_array((int) $case->country_id, [2, 9, 6, 1, 4, 3])) {
                Mail::to($email)->send(new OuttakeEmail($language, $case->case_identifier));
            }
        } catch (Exception $e) {
            Log::error('Error sending outtake email: '.$e->getMessage());
        }
    }

    private function delete_chat_messages(Cases $case): void
    {
        if (optional($case->values->where('case_input_id', 24)->first())->value == 82) {
            try {
                $request = Http::timeout(15)->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.config('app.cgp_internal_authentication_token'),
                ])->post(config('app.eap_online_url').'/api/chat-messages/delete', [
                    'case_id' => (int) $case->id,
                ]);

                if (! $request->successful()) {
                    Log::info("Request to delete chat mesgaes for: {$case->id} faild. ERROR: {$request->body()}");
                }
            } catch (Exception $e) {
                Log::info("Request to delete chat mesgaes for: {$case->id} faild. ERROR: {$e}");
            }
        }
    }

    public function interrupt_case(int $case_id, ?int $minka_id = null): JsonResponse
    {
        $case = Cases::query()->findOrFail($case_id);

        if (in_array($case->getRawOriginal('status'), ['employee_contacted', 'client_unreachable', 'assigned_to_expert'])) {
            $case->status = 'interrupted_confirmed';
            $case->closed_by_expert = $minka_id ?: Auth::user()->id;
            $case->confirmed_by = $minka_id ?: Auth::user()->id;
            $case->confirmed_at = Carbon::now('Europe/Budapest');
            $case->save();

            $duration = optional($case->values->where('case_input_id', 22)->first())->input_value->value;

            $expert_id = (Auth::user() !== null) ? Auth::user()->id : optional($case->case_accepted_expert())->id;

            $consultation_count = $case->consultations->count();

            // IF online appointment booking, than check if there are deleted consultation within 48 hour.
            $online_appointment_booking = DB::connection('mysql_eap_online')->table('online_appointment_bookings')->where('case_id', $case->id)->exists();

            if ($online_appointment_booking) {
                $consultation_count += $case->get_deleted_within_48_hour_consultations_count();
            }

            InvoiceCaseData::query()->firstOrCreate([
                'case_identifier' => $case->case_identifier,
                'consultations_count' => $consultation_count,
                'expert_id' => $expert_id,
                'duration' => (int) $duration,
                'permission_id' => (int) $case->case_type->value,
            ]);

            $this->exclude_client_from_online_therapy($case->id);
            $this->set_intake_colsed_at_date($case->id);

            if ($case->consultations->count() >= 1) {
                $this->send_pulso_outtake_email($case);
            }

            // IF counsultation type is chat, delete all chat messages in the eap_online databse belongig to the case
            $this->delete_chat_messages($case);

            return response()->json(['status' => 0]);
        }

        return response()->json(['status' => 1]);
    }
}
