<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AffiliateSearchController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompaniesController;
use App\Http\Controllers\Admin\CompanyWebsite\ArticlesController;
use App\Http\Controllers\Admin\CrisisController;
use App\Http\Controllers\Admin\CustomerSatisfactionController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\EapOnline\ChatController;
use App\Http\Controllers\Admin\EapOnline\EapArticlesController;
use App\Http\Controllers\Admin\EapOnline\EapCategoriesController;
use App\Http\Controllers\Admin\EapOnline\EapFooterMenuController;
use App\Http\Controllers\Admin\EapOnline\EapOnlineController;
use App\Http\Controllers\Admin\EapOnline\EapPodcastsController;
use App\Http\Controllers\Admin\EapOnline\EapPrefixesController;
use App\Http\Controllers\Admin\EapOnline\EapQuizzesController;
use App\Http\Controllers\Admin\EapOnline\EapRiportController;
use App\Http\Controllers\Admin\EapOnline\EapTranslationsController;
use App\Http\Controllers\Admin\EapOnline\EapUsersController;
use App\Http\Controllers\Admin\EapOnline\EapVideosController;
use App\Http\Controllers\Admin\EapOnline\EapVideoTherapyController;
use App\Http\Controllers\Admin\EapOnline\EapWebinarsController;
use App\Http\Controllers\Admin\EapOnline\OnsiteConsultationController;
use App\Http\Controllers\Admin\EapOnline\OnsiteConsultationDateController;
use App\Http\Controllers\Admin\EapOnline\OnsiteConsultationExpertController;
use App\Http\Controllers\Admin\EapOnline\OnsiteConsultationPlaceController;
use App\Http\Controllers\Admin\EapOnline\VideoChatController;
use App\Http\Controllers\Admin\ExpertController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LiveWebinarController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OpenInvoicingController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\PrizeGame\ContentController;
use App\Http\Controllers\Admin\PrizeGame\GameController;
use App\Http\Controllers\Admin\PrizeGame\LanguageLinesController;
use App\Http\Controllers\Admin\RiportController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\WorkshopController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Expert;
use App\Http\Controllers\Expert\CaseExpertController;
use App\Http\Controllers\Expert\InvoiceExpertController;
use App\Http\Controllers\Operator;
use App\Http\Controllers\Operator\CaseController;
use Illuminate\Support\Facades\Route;

Route::prefix('/ajax')->group(function (): void {
    Route::middleware('is_logged_in')->group(function (): void {
        Route::post('/get-company-permissions-and-steps', (new CaseController)->get_company_permissions_and_steps(...));
        Route::post('/get-contract-holder-by-company', (new CaseController)->get_contract_holder_by_company(...));
        Route::post('/get-available-expert-by-permission', (new CaseController)->get_available_expert_by_permission(...));
        Route::post('/get-available-experts', (new CaseController)->get_available_experts(...));
        Route::get('/select-all-cases/{query?}', (new Admin\CaseController)->select_all_cases(...));

        /* EXPERT KIKÖZVETÍTÉSE */
        Route::post('/assing-expert-to-case', (new Admin\CaseController)->assignExpertCase(...));

        /* ACTIVITY CODE HOZZÁRENDELÉSE ESETHEZ */
        Route::post('/set-activity-code', (new Admin\CaseController)->set_activity_code(...));

        /* ÜLÉS HOZZÁADÁSA */
        Route::post('/add-consultation-to-case', (new Admin\CaseController)->addConsultationToCase(...));

        /* STÁTUSZ MÓDOSÍTÁS */
        Route::post('/set-status', (new Admin\CaseController)->setStatus(...));

        /* EXPERT HOZZÁRENDELÉSE ESETHEZ EMAIL KÜLDÉSSEL */
        Route::post('/email-to-expert', (new CaseController)->sendMailToExpert(...));

        /* SINGLE SESSION THERAPY EXPERT HOZZÁRENDELÉSE ESETHEZ */
        Route::post('/assign-single-session-expert', (new CaseController)->assign_single_session_expert(...));

        /* ESET INPUTJÁNAK ÚJ ÉRTÉKET ADUNK */
        Route::post('/operator/assing-new-value-to-case-input', (new CaseController)->assingNewValueToCaseInput(...));
        Route::post('/expert/assing-new-value-to-case-input', (new CaseExpertController)->assingNewValueToCaseInput(...));
        Route::post('/admin/assing-new-value-to-case-input', (new Admin\CaseController)->assingNewValueToCaseInput(...));

        /* EXPERT NEM VÁLLALJA EL AZ ESETET */
        Route::post('/expert/cant-assign', (new CaseExpertController)->cantAssignCase(...));

        /* USER AKTIVÁLÁS, DEAKTIVÁLÁS */
        Route::post('/toggle-user-active', (new AdminController)->toggleActive(...));

        /* USER ZÁROLÁS, FELOLDÁS */
        Route::post('/toggle-user-locked', (new AdminController)->toggleLocked(...));

        /* CUSTOMER SATISFACTION */
        Route::post('/customer-satisfaction', (new CaseExpertController)->customer_satisfaction(...));

        /* COMPANY DELETE */
        Route::delete('/delete-company/{id}', (new CompaniesController)->delete(...));

        /* OPERATOR DELETE */
        Route::delete('/delete-operator/{id}', (new OperatorController)->delete(...));

        /* AFFILIATE SEARCH DELETE */
        Route::delete('/delete-affiliate-search/{id}', (new AffiliateSearchController)->delete(...));

        /* TASK DELETE */
        Route::delete('/delete-task/{id}', (new TaskController)->delete(...));

        /* CITY DELETE */
        Route::delete('/delete-city/{id}', (new CityController)->delete(...));

        /* CASE DELETE */
        Route::delete('/delete-case/{id}', (new Admin\CaseController)->delete(...));

        /* DOCUMENT DELETE */
        Route::delete('/delete-document/{id}', (new DocumentController)->delete(...));

        /* DOCUMENT ADMIN */
        Route::delete('/delete-admin/{id}', (new AdminController)->delete(...));

        /* EXPERT RESEND REG MAIL */
        Route::post('/expert-registration-email-resend', (new ExpertController)->resendRegistrationEmail(...));

        /* EXPERT DELETE */
        Route::delete('/delete-expert/{id}', (new ExpertController)->delete(...));

        /* CANCEL EXPERT CONTRACT */
        Route::delete('/cancel-expert-contract/{expert}', (new ExpertController)->cancel_contract(...));

        /* CUSTOMER SATISFACTION FELTÖLTÉSE NEM LEHETSÉGES */
        Route::post('/customer-satisfaction-not-possible', (new CaseExpertController)->customerSatisfactionNotPossible(...));

        /* AMDIN ÜLÉS TÖRLÉS */
        Route::delete('/delete-consultation/{id}', (new Admin\CaseController)->deleteConsultation(...));

        /* ÜGYFÉL NEM ELÉRHETŐ */
        Route::post('/client-unreachable', (new CaseExpertController)->clientUnreachable(...));

        /* ÜLÉS IDŐPONTJÁNAK MÓDOSÍTÁSA */
        Route::post('/edit-consultation', (new CaseExpertController)->editConsultationDate(...));

        /* WOS KÉRDŐÍV KITÖLTÉSÉNEK MENTÉSE */
        Route::post('/wos-survey-clicked', (new CaseExpertController)->wosSurveyClicked(...));

        /* PHQ9 KERDŐÍV PONTSZÁMÁNAK BEÁLLÍTÁSA */
        Route::post('set-phq9', (new CaseExpertController)->set_phq9_score(...));

        /* NESTLE KÉRDŐÍV ELKÜLDÉSE */
        Route::post('/send-nestle-questionnaire', (new CaseExpertController)->send_nestle_questionnaire(...));

        /* ÜLÉS TÖRLÉS */
        Route::delete('/expert-delete-consultation', (new CaseExpertController)->deleteConsultation(...));

        /* ONLINE FOGLALÁSOS ÜLÉS TÖRLÉS */
        Route::delete('/expert-delete-online-consultation', (new CaseExpertController)->delete_online_consultation(...));

        /* AZ ESET LEZARASA */
        Route::get('/close-case/{case}', (new CaseExpertController)->close_case(...));

        /* BEJELENTKEZÉS USERKÉNT */
        Route::post('/login-as', (new AdminController)->loginAs(...))->middleware('user_type:admin');

        /* BEJELENTKEZÉS USERKÉNT */
        Route::get('/login-back-as-admin', (new AdminController)->loginBackAsAdmin(...));

        /* REG MAIL KIKÜLDÉSE EGY ORSZÁGON BELÜLI ÖSSZES FÜGGŐBEN LEVŐ SZAKÉRTŐNEK */
        Route::get('/send-welcome-mail-to-country/{id}', (new ExpertController)->sendBatchRegMail(...));

        /* KÉRDÉS KÜLDÉSE AZ OPERÁTORNAK */
        Route::post('/send-question-to-operator', (new CaseExpertController)->sendQuestionToOperator(...));

        /* BEJELENTKEZÉS OPERÁTORKÉNT KAPCSOLT FIÓKBA */
        Route::post('/login-as-operator', (new Operator\OperatorController)->loginAsOtherAccount(...));

        /* BEJELENTKEZÉS KLIENSKÉNT */
        Route::post('/login-as-client', (new ClientController)->loginAsOtherAccount(...));

        /* ÁTJELENTKEZÉS DELOITTE KLIENSKÉNT */
        Route::post('/login-as-deloitte-client', (new ClientController)->login_as_deloitte_client(...));

        /* EAP RIPORT AKTIVÁLÁSA */
        Route::post('/eap-online/activate-riport', (new EapRiportController)->activate_riport(...));

        /* EAP RIPORT DEAKTIVÁLÁSA */
        Route::post('/eap-online/deactivate-riport', (new EapRiportController)->deactivate_riport(...));

        /* EAP RIPORTOK AKTIVÁLÁSA */
        Route::post('/eap-online/activate-riports', (new EapRiportController)->activate_riports(...));

        /* EAP RIPORTOK DEAKTIVÁLÁSA */
        Route::post('/eap-online/deactivate-riports', (new EapRiportController)->deactivate_riports(...));

        /* RIPORTOK AKTIVÁLÁSA */
        Route::post('/activate-riports', (new RiportController)->activate(...));

        /* RIPORTOK DEAKTIVÁLÁSA */
        Route::post('/deactivate-riports', (new RiportController)->deactivate(...));

        /* ELÉGEDETTSÉGI INDEXEK AKTIVÁLÁSA */
        Route::post('/activate-satisfactions', [CustomerSatisfactionController::class, 'activate']);

        /* ELÉGEDETTSÉGI INDEXEK DEAKTIVÁLÁSA */
        Route::post('/deactivate-satisfactions', [CustomerSatisfactionController::class, 'deactivate']);

        /* CUSTOMER SATISFACTION TÖRLÉSE */
        Route::delete('/delete-customer-satisfaction/{id}', (new Admin\CaseController)->deleteCustomerSatisfaction(...));

        /* SZAKÉRTŐ NEM VÁLLALJA AZ ESETET VISSZAVONÁSA */
        Route::delete('/revert-expert-cannot-assign/{caseId}/{userId}', (new Admin\CaseController)->revertExpertCannotAssign(...));

        /* A TANÁCSADÁS MEGSZAKADT */
        Route::put('/case-interrupted/{id}', (new CaseExpertController)->caseInterrupted(...));

        /**/
        Route::get('/get-cases/{country_id}', (new Admin\CaseController)->getCases(...));

        Route::get('/need_exclamation/{country_id}', (new Admin\CaseController)->need_exclamation(...));

        Route::get('/get-invoices', (new InvoiceController)->get_invoices(...));

        Route::post('/save-hourly-rate', (new InvoiceExpertController)->save_hourly_rate(...));

        Route::get('/get-workshops', (new WorkshopController)->get_workshops(...));

        Route::get('/get-crisis-interventions', (new CrisisController)->get_crisis_interventions(...));

        /* RIPORT SUMMARY */
        Route::get('/riport-summary', [ClientController::class, 'riportSummaryAjax']);

        /* NOTIFICATION DELETE */
        Route::delete('/delete-notification/{id}', (new NotificationController)->delete(...));

        /* NOTIFICATION SEEN */
        Route::post('/notification-seen/{id}', (new NotificationController)->notification_seen(...));

        /* SZÁMLA TÖRLÉSE */
        Route::delete('/delete-invoice-by-expert/{id}', (new InvoiceExpertController)->deleteInvoice(...));

        /* SZÁMLA ESEMÉNY LÉTREHOZÁS/AKTIVÁLÁS */
        Route::post('/invoice-event/{id}', (new InvoiceExpertController)->createEvent(...));

        /* SZÁMLA ESEMÉNY TÖRLÉS/DEAKTIVÁLÁS */
        Route::delete('/invoice-event/{id}', (new InvoiceExpertController)->deleteEvent(...));

        /* ESET HOZZÁADÁSA SZÁMLÁHOZ */
        Route::post('/add-case-to-invoice', (new InvoiceExpertController)->addCaseToInvoice(...));

        /* SZÁMLA STÁTUSZÁNAK MÓDOSÍTÁSA */
        Route::put('/set-invoice-status/{invoiceId}', (new InvoiceController)->setStatus(...));

        /* SZÁMLA TÖRLÉSE */
        Route::delete('/delete-invoice-by-admin/{id}', (new InvoiceController)->deleteInvoice(...));

        /* SZÁMLA KIEGYENLÍTVE JELZÉS VISSZAVONÁSA */
        Route::put('/revert-invoice-paid-status/{id}', (new InvoiceController)->revertInvoicePaidStatus(...));

        Route::put('/toggle-invoice-seen-status/{id}', (new InvoiceController)->toggleInvoiceSeenStatus(...));

        Route::post('/change-expert-country', (new ExpertController)->changeExpertCountry(...));

        /* EAP USER TÖRLÉSE */
        Route::post('/eap-user-delete/{id}', (new EapUsersController)->delete(...));

        /* HASZNÁLATABAN VAN-E A KATEGÓRIA */
        Route::get('/has-attached-article-category/{id}', (new EapCategoriesController)->has_article_attached(...));

        /* HASZNÁLATABAN VAN-E A PREFIX */
        Route::get('/has-attached-article-prefix/{id}', (new EapPrefixesController)->has_article_attached(...));

        /* EAP KATEGÓRIA TÖRLÉSE */
        Route::get('/delete-category/{id}', (new EapCategoriesController)->delete(...));

        /* EAP PREFIX TÖRLÉSE */
        Route::get('/delete-prefix/{id}', (new EapPrefixesController)->delete(...));

        /* EAP FOOTER MENUPONT TÖRLÉSE */
        Route::get('/delete-menu-point/{id}', (new EapFooterMenuController)->delete_menu_point(...));

        /* EAP FOOTER MENUPONT DOKUMENTUM TÖRLÉSE */
        Route::get('/delete-menu-point-document/{id}', (new EapFooterMenuController)->delete_document(...));

        /* EAP CONTACT INFO TÖRLÉSE */
        Route::delete('/delete-contact-information/{id}', (new EapOnlineController)->delete_contact_information(...));

        /* CÉGHEZ TARTOZÓ ORSZÁGOK LEKÉRÉSE */
        Route::get('/get-countries/{company_id}', (new EapOnlineController)->get_countries_by_company(...));

        /* WOS KÉRDŐIV VÁLASZOK HOZZÁADÁSA AZ ESETHEZ */
        Route::post('/add-wos-to-case', (new Admin\CaseController)->addWosToCase(...));

        /* SESSION TÖRLÉSE */
        Route::get('/clear-session/{key}', function ($key): void {
            if (session()->has($key)) {
                session()->forget($key);
            }
        });

        /* STORE OPEN INVOICING */
        Route::post('/open-invoicing/store', (new OpenInvoicingController)->store(...));

        /* EAP ONLINE TRANSLATION LINES LEKÉRÉSE */
        Route::get('/get-translations-lines', (new EapTranslationsController)->get_translation_lines(...));

        /* PRIZEGAME TRANSLATION LINES LEKÉRÉSE */
        Route::get('/get-prizegame-translations-lines', (new LanguageLinesController)->get_translation_lines(...));

        /* FEEDBACK TRANSLATION LINES LEKÉRÉSE */
        Route::get('/get-feedback-translations-lines', (new Admin\Feedback\LanguageLinesController)->get_translation_lines(...));

        /* EAP ARTICLE SECTION TÖRLÉSE */
        Route::post('/delete-existing-article-section', (new EapArticlesController)->delete_existing_article_section(...));

        /* COMPANY WEBSITE ARTICLE SECTION TÖRLÉSE */
        Route::post('/company-website/delete-existing-article-section', (new ArticlesController)->delete_existing_article_section(...));

        /* EAP ARTICLE SECTION ATTACHMENT TÖRLÉSE */
        Route::post('/delete_section_attachment_translation', (new EapArticlesController)->delete_section_attachment_translation(...));

        /* PRIZEGAME SECTION TÖRLÉSE */
        Route::post('/delete-existing-prizegame-section', (new ContentController)->delete_existing_section(...));

        /* PRIZEGAME DOCUMENT TÖRLÉSE */
        Route::post('/delete-existing-prizegame-document', (new ContentController)->delete_existing_document(...));

        /* PRIZEGAME IMAGE TÖRLÉSE */
        Route::post('/delete-existing-prizegame-image', (new ContentController)->delete_existing_image(...));

        /* PRIZEGAME QUESTION TÖRLÉSE */
        Route::post('/delete-existing-prizegame-question', (new ContentController)->delete_existing_question(...));

        /* PRIZEGAME ANSWER TÖRLÉSE */
        Route::post('/delete-existing-prizegame-answer', (new ContentController)->delete_existing_answer(...));

        /* PRIZEGAME TYPE */
        Route::post('/set-prizegame-type', (new ContentController)->set_prizegame_type(...));

        /* PRIZEGAME VIEWABLE */
        Route::post('/set-prizegame-viewable', (new GameController)->set_viewable(...));

        /* CÉGES WEBOLDAL CIKKEK LEKÉRÉSE */
        Route::get('/company-website/get-articles', (new ArticlesController)->get_articles(...));

        /* EAP CIKKEK LEKÉRÉSE */
        Route::get('/get-articles', (new EapArticlesController)->get_articles(...));

        /* EAP VIDEÓK LEKÉRÉSE */
        Route::get('/get-videos', (new EapVideosController)->get_videos(...));

        /* EAP WEBINÁROK LEKÉRÉSE */
        Route::get('/get-webinars', (new EapWebinarsController)->get_webinars(...));

        /* EAP PODCASTOK LEKÉRÉSE */
        Route::get('/get-podcasts', (new EapPodcastsController)->get_podcasts(...));

        /* EAP QUIZEK LEKÉRÉSE */
        Route::get('/get-quizzes', (new EapQuizzesController)->get_quizzes(...));

        /* EAP VIDEO CHAT LEZÁRÁSA */
        Route::post('/end-therapy', (new EapVideoTherapyController)->end_therapy(...));

        /* EAP CHAT LEZÁRÁSA */
        Route::post('/end-chat-therapy', (new ChatController)->end_therapy(...));

        /* EAP VIDEO CHAT TOKEN GENERALAS */
        Route::post('/video-therapy/token', (new VideoChatController)->token(...));

        Route::post('/email-to-lifeworks', (new CaseController)->sendMailToLifeWorks(...));

        /* WORKSHOP CHECK EXPERT AVAILABLITY */
        Route::post('/check-workshop-expert-availability', (new WorkshopController)->check_expert_availability(...));

        /* GET AVAILABLE CONSULTATION TYPES */
        Route::post('/get-available-consultation-types', (new CaseController)->get_available_consultation_types(...));

        /* ONSITE CONSULTATION PLACE DELETE */
        Route::post('/delete-onsite-consultation-place/{place}', (new OnsiteConsultationPlaceController)->delete(...));

        /* ONSITE CONSULTATION EXPERT DELETE */
        Route::post('/delete-onsite-consultation-expert/{expert}', (new OnsiteConsultationExpertController)->delete(...));

        /* ONSITE CONSULTATION DATE DELETE */
        Route::post('/delete-onsite-consultation-date/{consultation_date}', (new OnsiteConsultationDateController)->delete(...));

        /* ONSITE CONSULTATION DELETE */
        Route::post('/delete-onsite-consultation/{consultation}', (new OnsiteConsultationController)->delete(...));

        /* CHECK TELEKOM EMAIl ADDRESS */
        Route::post('/check-telekom-email-address', (new CaseController)->check_telekom_email_address(...));

        /* CHECK APPLICATION CODE REQUIREMENT */
        Route::post('/check-application-code-requirement', (new CaseController)->check_application_code_requirement(...));

        /* CHECK APPLICATION CODE */
        Route::post('/check-application-code', (new CaseController)->check_application_code(...));

        /* DELETE PRIZEGAME */
        Route::post('/delete-prizegame', (new GameController)->delete(...));

        /* GET AVAILABEL EXPERTS FOR WS/CI/O outsorucing */
        Route::get('/get-outsource-experts', (new WorkshopController)->get_experts_by_outsource_country(...));

        /* DELETE LIVE WEBINAR */
        Route::post('/delete-live-webinar/{live_webinar}', [LiveWebinarController::class, 'delete']);
    });
});
