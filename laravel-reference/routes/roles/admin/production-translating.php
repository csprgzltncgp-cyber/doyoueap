<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\EapOnline\EapArticlesController;
use App\Http\Controllers\Admin\EapOnline\EapOnlineController;
use App\Http\Controllers\Admin\EapOnline\EapTranslationsController;
use App\Http\Controllers\Admin\Google2FaController;
use App\Http\Controllers\Admin\TaskCommentController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Livewire\Admin\Assets\Create as AssetCreate;
use App\Http\Livewire\Admin\Assets\Index as AssetIndex;
use App\Http\Livewire\Admin\Assets\Storage;
use App\Http\Livewire\Admin\Assets\Waste;
use App\Http\Livewire\Admin\AssetType\Create as AssetTypesCreate;
use App\Http\Livewire\Admin\Todo\TaskEdit;
use App\Http\Livewire\Admin\Todo\TaskShow;
use Illuminate\Support\Facades\Route;

Route::prefix('/production_translating_admin')->group(function (): void {
    Route::name('production_translating_admin.')->group(function (): void {
        /* LOGIN VIEW */
        Route::get('/login', (new AdminController)->login(...))->name('login');

        /* LOGIN PROCESS */
        Route::post('/login', (new AdminController)->login_process(...));

        Route::get('/google2fa/back', (new Google2FaController)->back(...))->name('google2fa.back');

        Route::middleware(['is_logged_in', 'user_type:production_translating_admin', '2fa'])->group(function (): void {
            /* GOOGLE 2FA */
            Route::get('/google2fa/create', (new Google2FaController)->create(...))->name('google2fa.create');
            Route::post('/google2fa/create', (new Google2FaController)->store(...))->name('google2fa.store');
            Route::post('/google2fa/post', (new Google2FaController)->process(...))->name('google2fa.process');
            /* GOOGLE 2FA */

            /* SUBMENUS */
            Route::prefix('/menu')->name('submenu')->group(function (): void {
                Route::get('/digital', fn () => view('admin.submenus.digital'))->name('.digital');
            });
            /* SUBMENUS */

            /* DASHBOARD VIEW */
            Route::get('/dashboard', (new AdminController)->dashboard(...))->name('dashboard');
            /* DASHBOARD VIEW */

            /* TASKS */
            Route::prefix('/todo')->name('todo')->group(function (): void {
                Route::get('/filter', (new TaskController)->filter(...))->name('.filter');
                Route::get('/filter/results', (new TaskController)->filter_result(...))->name('.filter-result');
                Route::get('/statistics', (new TaskController)->statistics(...))->name('.statistics');
                Route::get('/create', (new TaskController)->create(...))->name('.create');
                Route::post('/create', (new TaskController)->store(...))->name('.store');
                Route::get('/issued', (new TaskController)->issued(...))->name('.issued');
                Route::get('/{task}', TaskEdit::class)->name('.edit');
                Route::get('/show/{task}', TaskShow::class)->name('.show');
                Route::get('/download-attachment/{id}', (new TaskController)->download_attachment(...))->name('.download-attachment');
                Route::get('/', (new TaskController)->index(...))->name('.index');
            });
            /* TASKS */

            /* TASK COMMENT */
            Route::prefix('/task-comment')->name('task-comment')->group(function (): void {
                Route::post('/create/{task}', (new TaskCommentController)->store(...))->name('.store');
                Route::post('/{taskComment}', (new TaskCommentController)->update(...))->name('.update');
            });
            /* TASK COMMENT */

            /* CALENDAR */
            Route::prefix('/calendar')->name('calendar')->group(function (): void {
                Route::get('/', (new CalendarController)->index(...))->name('.index');
            });
            /* CALENDAR */

            /* FORCE CHANGE PASSWORD */
            Route::get('/force-change-password', (new AdminController)->force_change_password(...))->name('force-change-password');
            Route::post('/force-change-password', (new AdminController)->force_change_password_process(...))->name('force-change-password-process');
            /* FORCE CHANGE PASSWORD */

            /* EAP ONLINE */
            Route::prefix('/eap-online')->name('eap-online')->group(function (): void {
                Route::get('/actions', (new EapOnlineController)->actions(...))->name('.actions');

                /* TRANSLATIONS */
                Route::prefix('/translation')->name('.translation')->group(function (): void {
                    Route::prefix('/system')->name('.system')->group(function (): void {
                        Route::get('/', (new EapTranslationsController)->system_view(...))->name('.view');
                        Route::post('/', (new EapTranslationsController)->system_store(...))->name('.store');
                    });
                });
                /* TRANSLATIONS */

                /* ARTICLES */
                Route::prefix('/articles')->name('.articles')->group(function (): void {

                    /* TRANSLATE ARTICLE */
                    Route::prefix('/translate')->name('.translate')->group(function (): void {
                        Route::get('/', (new EapArticlesController)->index(...))->name('.list');
                        Route::get('/{id}', (new EapArticlesController)->translate_view(...))->name('.view');
                        Route::post('/{id}', (new EapArticlesController)->translate(...))->name('.save');
                    });
                    /* TRANSLATE ARTICLE */

                });
                /* ARTICLES */
            });
            /* EAP ONLINE */

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
