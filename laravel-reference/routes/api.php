<?php

use App\Http\Controllers\Api\CaseController;
use App\Http\Controllers\Api\LiveWebinarController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

/* Routes that require CGP_INTERNAL_AUTHENTICATION_TOKEN */
Route::middleware('cgp.auth')->group(function (): void {
    Route::post('/get_available_experts', (new CaseController)->get_available_experts(...));
    Route::post('/mail_to_lifeworks', (new CaseController)->mail_to_lifeworks(...));
    Route::post('/get_case_data', (new CaseController)->get_case_data(...));
    Route::post('/delete_case_consultations', (new CaseController)->delete_case_consultations(...));
    Route::post('/create_case_consultation', (new CaseController)->create_case_consultation(...));
    Route::post('/get_consultations_number', (new CaseController)->get_consultations_number(...));
    Route::post('/get_used_consultation_number', (new CaseController)->get_used_consultation_number(...));
    Route::post('/set_delete_notification', (new CaseController)->set_delete_notification(...));
    Route::post('/set_deleted_consultation', (new CaseController)->set_deleted_consultation(...));
    Route::post('/set_eap_case_feedback', (new CaseController)->set_eap_case_feedback(...));
    Route::post('/create_wos_answers', (new CaseController)->create_wos_answers(...));
    Route::get('/get_consultation_data', (new CaseController)->get_consultation_data(...));
    Route::get('/case_consultation_type', (new CaseController)->get_consultation_type(...));
    Route::post('/case_assigne_mail', (new CaseController)->send_case_assign_mail(...));
    Route::post('/change_consultation_type', (new CaseController)->change_consultation_type(...));
    Route::get('/get_case_confirmed_at_date', (new CaseController)->get_case_confirmed_at_date(...));
    Route::post('/case_interrupted', (new CaseController)->case_interrupted(...));
    Route::post('/send_compsych_survey', (new CaseController)->send_compsych_survey(...));

    // Test routes for ai operator
    Route::post('/create-case', function (): void {
        $data = request()->all();
        Log::info('Create case: '.json_encode($data));
    });

    Route::prefix('/live-webinar')->name('.live-webinar')->group(function (): void {
        Route::get('/', [LiveWebinarController::class, 'index'])->name('.index');
        Route::get('/current', [LiveWebinarController::class, 'current'])->name('.current');
        Route::get('/{live_webinar}', [LiveWebinarController::class, 'show'])->name('.show');
    });
});
/* Routes that require CGP_INTERNAL_AUTHENTICATION_TOKEN */
