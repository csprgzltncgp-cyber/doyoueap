<?php

use App\Http\Controllers\Client;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\CrisisController;
use App\Http\Controllers\Client\CustomerSatisfactionController;
use App\Http\Controllers\Client\ForceChangePasswordController;
use App\Http\Controllers\Client\HealthMapController;
use App\Http\Controllers\Client\PrizeGameController;
use App\Http\Controllers\Client\ProgramUsageController;
use App\Http\Controllers\Client\RiportController;
use App\Http\Controllers\Client\TelusCaseController;
use App\Http\Controllers\Client\VolumeRequestController;
use App\Http\Controllers\Client\WhatIsNewController;
use App\Http\Controllers\Client\WorkshopController;
use Illuminate\Support\Facades\Route;

Route::prefix('/telus-case')->name('telus-case.')->group(function (): void {
    Route::get('/{code}', [TelusCaseController::class, 'show'])->middleware('signed')->name('show');
    Route::post('/{code}/download', [TelusCaseController::class, 'download'])->name('download');
});

Route::get('/client', fn () => redirect()->route('client.what-is-new.select-language'));

Route::prefix('/client')->middleware('client')->group(function (): void {
    Route::name('client.')->group(function (): void {
        /* LOGIN */
        Route::get('/login', (new ClientController)->login(...))->name('login');
        Route::post('/login', (new ClientController)->login_process(...));

        /* WHAT IS NEW */
        Route::prefix('/what-is-new')->name('what-is-new')->group(function (): void {
            Route::get('/select-language', (new WhatIsNewController)->select_language_view(...))->name('.select-language');
            Route::get('/{language_code}/select-language-process', (new WhatIsNewController)->select_language_process(...))->name('.select-language-process');
            Route::get('/{language_code}/video', (new WhatIsNewController)->video(...))->name('.video');
            Route::get('/{language_code}/contact', (new WhatIsNewController)->contact(...))->name('.contact');
        });
        /* WHAT IS NEW */

        Route::middleware(['is_logged_in', 'user_type:client'])->group(function (): void {
            /* FORCE CHANGE PASSWORD */
            Route::get('/force-change-password', (new ForceChangePasswordController)->force_change_password(...))->name('force-change-password');
            Route::post('/force-change-password', (new ForceChangePasswordController)->force_change_password_process(...))->name('force-change-password-process');
            /* FORCE CHANGE PASSWORD */

            /* NEW PASSWORD */
            Route::get('/new-password', (new ClientController)->new_password(...))->name('new_password');
            Route::post('/new-password', (new ClientController)->force_change_password_process(...))->name('force_change_password_process');

            /* ELÉGEDETTSÉGI INDEX */
            Route::get('/customer-satisfaction', (new CustomerSatisfactionController)->show(...))->name('customer_satisfaction');

            /* WORKSHOPS */
            Route::get('workshops/', (new WorkshopController)->index(...))->name('workshops');

            /* CRISIS INTERVENTIONS */
            Route::get('/crisis-interventions', (new CrisisController)->index(...))->name('crisis-interventions');

            /* SET CUSTOME CLIENT DASHBOARD LANGUAGE */
            Route::get('/custom-language/{code}', (new ClientController)->custom_language(...))->name('custom_language');

            /* PRIZEGAME */
            Route::prefix('/prizegame')->name('prizegame')->group(function (): void {
                Route::get('/{country?}', (new PrizeGameController)->show(...))->name('.show');
                Route::post('/{game?}', (new PrizeGameController)->store(...))->name('.store');
                Route::post('/export/{game}', (new PrizeGameController)->export(...))->name('.export');
            });

            /* RIPORT */
            Route::get('/riport/{quarter?}/{country?}', (new RiportController)->show(...))->name('riport.show');
            Route::get('/login_to_connected_company', (new RiportController)->login_to_connected_company(...))->name('riport.login_to_connected_company');

            /* HEALTH MAP */
            Route::get('/health-map/{country?}', (new HealthMapController)->show(...))->name('health-map');

            /* PROGRAM USAGE */
            Route::get('/program-usage/{country?}/{year?}', (new ProgramUsageController)->show(...))->name('program_usage');

            /* SEND DATA */
            Route::get('/volume-request/{date?}', (new VolumeRequestController)->show(...))->name('volume-request');
        });
    });
});
