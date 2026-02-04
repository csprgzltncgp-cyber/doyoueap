<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AffiliateSearchController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\Google2FaController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Livewire\Admin\AffiliateSearch\Edit;
use App\Http\Livewire\Admin\AffiliateSearch\Issued;
use App\Http\Livewire\Admin\AffiliateSearch\Show;
use App\Http\Livewire\Admin\Assets\Create as AssetCreate;
use App\Http\Livewire\Admin\Assets\Index as AssetIndex;
use App\Http\Livewire\Admin\Assets\Storage;
use App\Http\Livewire\Admin\Assets\Waste;
use App\Http\Livewire\Admin\AssetType\Create as AssetTypesCreate;
use App\Http\Livewire\Admin\Todo\TaskEdit;
use App\Http\Livewire\Admin\Todo\TaskShow;
use Illuminate\Support\Facades\Route;

Route::prefix('/affiliate_search_admin')->group(function (): void {
    Route::name('affiliate_search_admin.')->group(function (): void {
        /* LOGIN VIEW */
        Route::get('/login', (new AdminController)->login(...))->name('login');

        /* LOGIN PROCESS */
        Route::post('/login', (new AdminController)->login_process(...));

        Route::get('/google2fa/back', (new Google2FaController)->back(...))->name('google2fa.back');

        Route::middleware(['is_logged_in', 'user_type:affiliate_search_admin', '2fa'])->group(function (): void {
            /* GOOGLE 2FA */
            Route::get('/google2fa/create', (new Google2FaController)->create(...))->name('google2fa.create');
            Route::post('/google2fa/create', (new Google2FaController)->store(...))->name('google2fa.store');
            Route::post('/google2fa/post', (new Google2FaController)->process(...))->name('google2fa.process');
            /* GOOGLE 2FA */

            /* DASHBOARD VIEW */
            Route::get('/dashboard', (new AdminController)->dashboard(...))->name('dashboard');
            /* DASHBOARD VIEW */

            /* FORCE CHANGE PASSWORD */
            Route::get('/force-change-password', (new AdminController)->force_change_password(...))->name('force-change-password');
            Route::post('/force-change-password', (new AdminController)->force_change_password_process(...))->name('force-change-password-process');
            /* FORCE CHANGE PASSWORD */

            /* TASKS */
            Route::prefix('/todo')->name('todo')->group(function (): void {
                Route::get('/download-attachment/{id}', (new TaskController)->download_attachment(...))->name('.download-attachment');
                Route::get('/filter', (new TaskController)->filter(...))->name('.filter');
                Route::get('/filter/results', (new TaskController)->filter_result(...))->name('.filter-result');
                Route::get('/statistics', (new TaskController)->statistics(...))->name('.statistics');
                Route::get('/create', (new TaskController)->create(...))->name('.create');
                Route::post('/create', (new TaskController)->store(...))->name('.store');
                Route::get('/issued', (new TaskController)->issued(...))->name('.issued');
                Route::get('/{task}', TaskEdit::class)->name('.edit');
                Route::get('/show/{task}', TaskShow::class)->name('.show');
                Route::get('/', (new TaskController)->index(...))->name('.index');
            });
            /* TASKS */

            /* AFFILIATE SEARCH WORKFLOWS */
            Route::prefix('/affiliate-searches')->name('affiliate_searches')->group(function (): void {
                Route::get('/create', (new AffiliateSearchController)->create(...))->name('.create');
                Route::post('/create', (new AffiliateSearchController)->store(...))->name('.store');
                Route::get('/show/{affiliateSearch}', Show::class)->name('.show');
                Route::get('/edit/{affiliateSearch}', Edit::class)->name('.edit');
                Route::get('/issued', Issued::class)->name('.issued');
                Route::get('/statistics', (new AffiliateSearchController)->statistics(...))->name('.statistics');
                Route::get('/all', (new AffiliateSearchController)->all(...))->name('.all');
                Route::get('/filter', (new AffiliateSearchController)->filter(...))->name('.filter');
                Route::get('/filter/results', (new AffiliateSearchController)->filter_result(...))->name('.filter-result');
                Route::get('/download-attachment/{id}', (new AffiliateSearchController)->download_attachment(...))->name('.download-attachment');
                Route::get('/', (new AffiliateSearchController)->index(...))->name('.index');
            });
            /* AFFILIATE SEARCH WORKFLOWS */

            /* CALENDAR */
            Route::prefix('/calendar')->name('calendar')->group(function (): void {
                Route::get('/', (new CalendarController)->index(...))->name('.index');
            });
            /* CALENDAR */

            /* ASSETS */
            Route::prefix('/assets')->group(function (): void {
                Route::name('assets')->group(function (): void {
                    /* MENU */
                    Route::get('/', (new AssetController)->index(...))->name('.menu');

                    /* INDEX */
                    Route::get('/index', AssetIndex::class)->name('.index');

                    /* NEW */
                    Route::get('/create', AssetCreate::class)->name('.create');

                    /* SHOW WASTE */
                    Route::get('/waste', Waste::class)->name('.waste');

                    /* SHOW STORAGE */
                    Route::get('/storage', Storage::class)->name('.storage');
                });
            });
            /* ASSETS */

            /* ASSET TYPES */
            Route::prefix('/asset-types')->group(function (): void {
                Route::name('asset-types')->group(function (): void {
                    /* NEW */
                    Route::get('/create', AssetTypesCreate::class)->name('.create');
                });
            });
            /* ASSETS TYPES */
        });
    });
});
