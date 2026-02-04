<?php

use App\Http\Controllers\Operator\CaseController;
use App\Http\Controllers\Operator\DocumentController;
use App\Http\Controllers\Operator\EapMailsController;
use App\Http\Controllers\Operator\ExpertController;
use App\Http\Controllers\Operator\OperatorController;
use Illuminate\Support\Facades\Route;

Route::get('/operator', fn () => redirect()->route('operator.login'));

Route::get('/register/operator', (new OperatorController)->register(...));
Route::post('/register/operator', (new OperatorController)->register_process(...));

Route::prefix('/operator')->group(function (): void {
    Route::name('operator.')->group(function (): void {
        /* LOGIN VIEW */
        Route::get('/login', (new OperatorController)->login(...))->name('login');

        /* LOGIN PROCESS */
        Route::post('/login', (new OperatorController)->login_process(...));

        Route::middleware(['is_logged_in', 'user_type:operator'])->group(function (): void {
            /* DASHBOARD VIEW */
            Route::get('/dashboard', (new OperatorController)->dashboard(...))->name('dashboard');
            /* DASHBOARD VIEW */

            /* FORCE CHANGE PASSWORD */
            Route::get('/force-change-password', (new OperatorController)->force_change_password(...))->name('force-change-password');
            Route::post('/force-change-password', (new OperatorController)->force_change_password_process(...))->name('force-change-password-process');
            /* FORCE CHANGE PASSWORD */

            /* EAP ONLINE */
            Route::prefix('/eap-online')->group(function (): void {
                Route::name('eap-online')->group(function (): void {
                    /* MAILS */
                    Route::prefix('/mails')->name('.mails')->group(function (): void {
                        Route::get('/all', (new EapMailsController)->index(...))->name('.list');

                        Route::post('/{id}', (new EapMailsController)->reply(...))->name('.reply');

                        /* FILTER */
                        Route::prefix('/filter')->name('.filter')->group(function (): void {
                            Route::get('/view', (new EapMailsController)->filter_view(...))->name('.view');
                            Route::post('/result', (new EapMailsController)->filter(...))->name('.result');
                        });
                        /* FILTER */

                        Route::get('/{id}/{page}', (new EapMailsController)->view(...))->name('.view');
                    });
                    /* MAILS */
                });
            });
            /* EAP ONLINE */

            /* CASES */
            Route::prefix('/cases')->group(function (): void {
                Route::name('cases')->group(function (): void {
                    Route::post('/generate-new-cases', (new CaseController)->generate_new_cases(...))->name('.generate-new-cases');
                    Route::get('/filter', (new CaseController)->filter(...))->name('.filter');
                    Route::post('/export', (new CaseController)->export(...))->name('.export');
                    Route::post('/filter', (new CaseController)->filter_process(...))->name('.filter-process');
                    Route::get('/filtered', (new CaseController)->filtered(...))->name('.filtered');
                    /* NEW */
                    Route::get('/new', (new CaseController)->create(...))->name('.new');
                    Route::post('/new', (new CaseController)->store(...))->name('.new-process');
                    /* NEW */
                    Route::get('/edit/{id}', (new CaseController)->edit(...))->name('.edit');
                    Route::post('/edit/{id}', (new CaseController)->edit_process(...))->name('.edit-process');

                    Route::get('/in-progress', (new CaseController)->list_in_progress(...))->name('.in_progress');
                    Route::get('/{id}', (new CaseController)->view(...))->name('.view');

                    Route::get('/created/{id}', (new CaseController)->created(...))->name('.created');
                });
            });
            /* CASES */

            /* DOCUMENTS */
            Route::prefix('/documents')->group(function (): void {
                Route::name('documents')->group(function (): void {
                    /* EDIT */
                    Route::get('/view/{id}', (new DocumentController)->view(...))->name('.view');
                });
            });
            /* DOCUMENTS */

            /* LIST OF EXPERTS */
            Route::prefix('experts')->name('experts')->group(function (): void {
                Route::get('/filter', (new ExpertController)->filter(...))->name('.filter');
                Route::get('/filter-result', (new ExpertController)->filter_result(...))->name('.filter-result');
                Route::get('/{user}', (new ExpertController)->show(...))->name('.show');
                Route::get('/', (new ExpertController)->index(...))->name('.index');
            });
            /* LIST OF EXPERTS */
        });
    });
});
