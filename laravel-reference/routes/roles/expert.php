<?php

use App\Http\Controllers\Expert;
use App\Http\Controllers\Expert\CaseExpertController;
use App\Http\Controllers\Expert\CrisisExpertController;
use App\Http\Controllers\Expert\CurrencyChangeController;
use App\Http\Controllers\Expert\DocumentExpertController;
use App\Http\Controllers\Expert\ExpertController;
use App\Http\Controllers\Expert\InvoiceExpertController;
use App\Http\Controllers\Expert\LiveWebinarController;
use App\Http\Controllers\Expert\LiveWebinarSessionController;
use App\Http\Controllers\Expert\OnsiteConsultationController;
use App\Http\Controllers\Expert\OtherActivityController;
use App\Http\Controllers\Expert\WorkshopExpertController;
use App\Http\Livewire\Expert\ExpertData\Step1;
use App\Http\Livewire\Expert\ExpertData\Step2;
use App\Http\Livewire\Expert\Invoice\Create as InvoiceCreate;
use App\Http\Livewire\Expert\OtherActivity\ShowPage;
use Illuminate\Support\Facades\Route;

Route::get('/expert', fn () => redirect()->route('expert.login'));

Route::get('/register/expert', (new ExpertController)->register(...));
Route::post('/register/expert', (new ExpertController)->register_process(...));

Route::prefix('/expert')->group(function (): void {
    Route::name('expert.')->group(function (): void {
        /* LOGIN VIEW */
        Route::get('/login', (new ExpertController)->login(...))->name('login');

        /* LOGIN PROCESS */
        Route::post('/login', (new ExpertController)->login_process(...));
        Route::middleware(['is_logged_in', 'user_type:expert', 'user.notifications'])->group(function (): void {
            /* DASHBOARD VIEW */
            Route::get('/dashboard', (new ExpertController)->dashboard(...))->name('dashboard');

            /* EXPERT DATA */
            Route::prefix('expert-data')->name('expert-data')->group(function (): void {
                Route::get('/step1', Step1::class)->name('.step1');
                Route::get('/step2', Step2::class)->name('.step2');
                Route::get('/step3', fn () => view('expert.expert-data.step3'))->name('.step3');

                Route::get('/step4', fn () => view('expert.expert-data.step4'))->name('.step4');
            });

            /* CURRENCY CHANGE */
            Route::prefix('/currency-change')->name('currency-change')->group(function (): void {
                Route::get('/index', CurrencyChangeController::class)->name('.index');
            });

            /* PROFILE VIEW */
            Route::get('/profile', Step2::class)->name('profile');

            /* FORCE CHANGE PASSWORD */
            Route::get('/force-change-password', (new ExpertController)->force_change_password(...))->name('force-change-password');
            Route::post('/force-change-password', (new ExpertController)->force_change_password_process(...))->name('force-change-password-process');
            /* FORCE CHANGE PASSWORD */

            /* PASSWORD CHANGE */
            Route::get('/password-change', (new ExpertController)->password_change(...))->name('password_change');
            Route::post('/password-change', (new ExpertController)->force_change_password_process(...))->name('force_change_password_process');

            /* CASES */
            Route::prefix('/cases')->group(function (): void {
                Route::name('cases')->group(function (): void {
                    Route::get('/in-progress', (new CaseExpertController)->list_in_progress(...))->name('.in_progress');
                    Route::get('/{id}', (new CaseExpertController)->view(...))->name('.view');
                });
            });
            /* CASES */

            /* LIVE WEBINAR */
            Route::prefix('/live-webinar')->name('live-webinar')->group(function (): void {
                Route::get('/', [LiveWebinarController::class, 'index'])->name('.index');
                Route::get('/{live_webinar}', [LiveWebinarController::class, 'show'])->name('.show');
                Route::get('/{live_webinar}/start', [LiveWebinarController::class, 'start'])->name('.start');
                Route::post('/{live_webinar}/signature', [LiveWebinarSessionController::class, 'signature'])->name('.signature');
                Route::post('/{live_webinar}/end', [LiveWebinarSessionController::class, 'end'])->name('.end');
            });
            /* LIVE WEBINAR */

            /* DOCUMENTS */
            Route::prefix('/documents')->group(function (): void {
                Route::name('documents')->group(function (): void {
                    /* EDIT */
                    Route::get('/view/{id}', (new DocumentExpertController)->view(...))->name('.view');
                });
            });
            /* DOCUMENTS */

            /* INVOICES */
            Route::prefix('/invoices')->group(function (): void {
                Route::name('invoices')->group(function (): void {
                    Route::get('/', (new InvoiceExpertController)->index(...))->name('.index');

                    /* ÚJ */
                    Route::get('/new', InvoiceCreate::class)->name('.new')->middleware('expert.invoice');

                    /* SZÁMLA LETÖLTÉSE */
                    Route::get('/download-invoice/{id}', (new InvoiceExpertController)->downloadInvoice(...))->name('.downloadInvoice');

                    Route::get('/main', (new InvoiceExpertController)->main(...))->name('.main');

                    Route::get('/infos', (new InvoiceExpertController)->infos(...))->name('.infos');

                    Route::get('/{id}', (new InvoiceExpertController)->view(...))->name('.view');
                });
            });
            /* INVOICES */

            /* WORKSHOPS */
            Route::prefix('/workshops')->group(function (): void {
                Route::name('workshops')->group(function (): void {
                    /* LIST */
                    Route::get('/list', (new WorkshopExpertController)->index(...))->name('.list');
                    /* EDIT */
                    Route::get('/edit/{id}', (new WorkshopExpertController)->edit(...))->name('.edit');
                    /* UPDATE */
                    Route::post('/update/{id}', (new WorkshopExpertController)->update(...))->name('.update');
                    /* ACCEPT */
                    Route::get('/accept/{id}', (new WorkshopExpertController)->accept(...))->name('.accept');
                    /* DENIE */
                    Route::get('/denie/{id}', (new WorkshopExpertController)->denie(...))->name('.denie');

                    Route::get('/closed', (new WorkshopExpertController)->list_closed(...))->name('.list_closed');
                });
            });
            /* WORKSHOPS */

            /* CRISIS INTERVENTIONS */
            Route::prefix('/crisis')->group(function (): void {
                Route::name('crisis')->group(function (): void {
                    /* LIST */
                    Route::get('/list', (new CrisisExpertController)->index(...))->name('.list');
                    /* EDIT */
                    Route::get('/edit/{id}', (new CrisisExpertController)->edit(...))->name('.edit');
                    /* UPDATE */
                    Route::post('/update/{id}', (new CrisisExpertController)->update(...))->name('.update');
                    /* ACCEPT */
                    Route::get('/accept/{id}', (new CrisisExpertController)->accept(...))->name('.accept');
                    /* DENIE */
                    Route::get('/denie/{id}', (new CrisisExpertController)->denie(...))->name('.denie');

                    Route::get('/closed', (new CrisisExpertController)->list_closed(...))->name('.list_closed');
                });
            });
            /* CRISIS INTERVENTIONS */

            /* Other activity */
            Route::prefix('/other-activities')->name('other-activities')->group(function (): void {
                Route::get('/', (new OtherActivityController)->index(...))->name('.index');
                Route::get('/closed', (new OtherActivityController)->index_closed(...))->name('.index_closed');
                Route::get('/{id}', ShowPage::class)->name('.show');
            });
            /* Other activity */

            /* Onsite Consultation */
            Route::prefix('/onsite-consultation')->name('onsite-consultation')->group(function (): void {
                Route::get('/', OnsiteConsultationController::class)->name('.index');
            });
            /* Onsite Consultation */
        });

        /* PASSWORD RESET */
        Route::get('/reset-password', (new ExpertController)->reset_password(...))->name('reset-password');
        Route::post('/reset-password', (new ExpertController)->reset_password_process(...))->name('reset-password-process');
    });
});
