<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CaseController;
use App\Http\Controllers\Admin\CrisisController;
use App\Http\Controllers\Admin\Google2FaController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\InvoiceHelper\CgpDataController;
use App\Http\Controllers\Admin\InvoiceHelper\CompanyProfilesController;
use App\Http\Controllers\Admin\InvoiceHelper\CompletionCertificateController;
use App\Http\Controllers\Admin\InvoiceHelper\DirectInvoicingController;
use App\Http\Controllers\Admin\InvoiceHelper\EnvelopeController;
use App\Http\Controllers\Admin\OtherActivityController;
use App\Http\Controllers\Admin\TaskCommentController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\WorkshopController;
use App\Http\Controllers\ContractHolderCompanyDataController;
use App\Http\Livewire\Admin\Assets\Create as AssetCreate;
use App\Http\Livewire\Admin\Assets\Index as AssetIndex;
use App\Http\Livewire\Admin\Assets\Storage;
use App\Http\Livewire\Admin\Assets\Waste;
use App\Http\Livewire\Admin\AssetType\Create as AssetTypesCreate;
use App\Http\Livewire\Admin\OtherActivity\CreatePage;
use App\Http\Livewire\Admin\OtherActivity\ShowPage;
use App\Http\Livewire\Admin\Todo\TaskEdit;
use App\Http\Livewire\Admin\Todo\TaskShow;
use Illuminate\Support\Facades\Route;

Route::prefix('/financial_admin')->group(function (): void {
    Route::name('financial_admin.')->group(function (): void {
        /* LOGIN VIEW */
        Route::get('/login', (new AdminController)->login(...))->name('login');

        /* LOGIN PROCESS */
        Route::post('/login', (new AdminController)->login_process(...));

        Route::get('/google2fa/back', (new Google2FaController)->back(...))->name('google2fa.back');

        Route::middleware(['is_logged_in', 'user_type:financial_admin', '2fa'])->group(function (): void {
            /* GOOGLE 2FA */
            Route::get('/google2fa/create', (new Google2FaController)->create(...))->name('google2fa.create');
            Route::post('/google2fa/create', (new Google2FaController)->store(...))->name('google2fa.store');
            Route::post('/google2fa/post', (new Google2FaController)->process(...))->name('google2fa.process');
            /* GOOGLE 2FA */

            /* DASHBOARD VIEW */
            Route::get('/dashboard', (new AdminController)->dashboard(...))->name('dashboard');
            /* DASHBOARD VIEW */

            /* SUBMENUS */
            Route::prefix('/menu')->name('submenu')->group(function (): void {
                Route::get('/outsources', fn () => view('admin.submenus.outsources'))->name('.outsources');

                Route::get('/invoices', fn () => view('admin.submenus.invoices'))->name('.invoices');
            });
            /* SUBMENUS */

            Route::prefix('/cases')->group(function (): void {
                Route::name('cases')->group(function (): void {
                    Route::get('/filter', (new CaseController)->filter(...))->name('.filter');
                    Route::post('/filter', (new CaseController)->filter_process(...));
                    Route::get('/filtered', (new CaseController)->filtered(...))->name('.filtered');
                    Route::post('/export', (new CaseController)->export(...))->name('.export');
                    Route::post('/delete-all', (new CaseController)->deleteAll(...))->name('.deleteAll');
                    Route::get('/closed', (new CaseController)->list_closed(...))->name('.closed');
                    Route::get('/edit/{id}', [CaseController::class, 'edit'])->name('.edit');
                    Route::get('/{id}', (new CaseController)->view(...))->name('.view');
                    Route::get('/in-progress', (new CaseController)->list_in_progress(...))->name('.in_progress');
                });
            });

            /* DATA */
            Route::prefix('/data')->name('data')->group(function (): void {
                Route::view('/', 'admin.data.index')->name('.index');
            });
            /* DATA */

            /* WORKSHOPS */
            Route::prefix('/workshops')->group(function (): void {
                Route::name('workshops')->group(function (): void {
                    /* LIST */
                    Route::get('/', (new WorkshopController)->index(...))->name('.list');
                    /* VIEW */
                    Route::get('/view/{id}', (new WorkshopController)->view(...))->name('.view');
                    /* NEW */
                    Route::get('/new', (new WorkshopController)->create(...))->name('.new');
                    Route::post('/new', (new WorkshopController)->store(...))->name('.add');

                    Route::get('/delete/{id}', (new WorkshopController)->delete(...))->name('.delete');

                    Route::post('/update/{id}', (new WorkshopController)->update(...))->name('.update');
                    Route::get('/close/{id}', (new WorkshopController)->close(...))->name('.close');
                    Route::get('/close/{id}', (new WorkshopController)->close(...))->name('.close');
                    Route::get('/accept_expert_price/{id}', (new WorkshopController)->accept_expert_price(...))->name('.accept_expert_price');

                    Route::get('/filter', (new WorkshopController)->filter(...))->name('.filter');
                    Route::get('/result', (new WorkshopController)->filterResult(...))->name('.result');

                    Route::post('/setPaid', (new WorkshopController)->setWorkshopToPaid(...))->name('.setWorkshopPaid');
                });
            });
            /* WORKSHOPS */

            /* CRISIS */
            Route::prefix('/crisis')->group(function (): void {
                Route::name('crisis')->group(function (): void {
                    /* LIST */
                    Route::get('/', (new CrisisController)->index(...))->name('.list');
                    /* VIEW */
                    Route::get('/view/{crisis_case}', (new CrisisController)->view(...))->name('.view');
                    /* NEW */
                    Route::get('/new', (new CrisisController)->create(...))->name('.new');
                    Route::post('/new', (new CrisisController)->store(...))->name('.add');

                    Route::get('/delete/{id}', (new CrisisController)->delete(...))->name('.delete');

                    Route::post('/update/{id}', (new CrisisController)->update(...))->name('.update');
                    Route::get('/close/{id}', (new CrisisController)->close(...))->name('.close');
                    Route::get('/close/{id}', (new CrisisController)->close(...))->name('.close');
                    Route::get('/accept_expert_price/{id}', (new CrisisController)->accpet_expert_price(...))->name('.accept_expert_price');

                    Route::get('/filter', (new CrisisController)->filter(...))->name('.filter');
                    Route::get('/result', (new CrisisController)->filterResult(...))->name('.result');

                    Route::post('/setPaid', (new CrisisController)->setCrisisToPaid(...))->name('.setCrisisPaid');
                });
            });
            /* CRISIS */

            /* Other activity */
            Route::prefix('/other-activities')->name('other-activities')->group(function (): void {
                Route::get('/', (new OtherActivityController)->index(...))->name('.index');
                Route::get('/create', CreatePage::class)->name('.create');
                Route::post('/set-paid', (new OtherActivityController)->set_paid(...))->name('.set-paid');

                Route::get('/filter', (new OtherActivityController)->filter(...))->name('.filter');
                Route::get('/result', (new OtherActivityController)->filterResult(...))->name('.result');

                Route::get('/{id}', ShowPage::class)->name('.show');
            });
            /* Other activity */

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

            /* SZÁMLÁZÁS */
            Route::prefix('/invoices')->group(function (): void {
                Route::name('invoices')->group(function (): void {
                    Route::get('/', (new InvoiceController)->index(...))->name('.index');
                    Route::get('download/{id}', (new InvoiceController)->downloadInvoice(...))->name('.downloadInvoice');
                    Route::get('/filter', (new InvoiceController)->filter(...))->name('.filter');
                    Route::get('/result', (new InvoiceController)->filterResult(...))->name('.result');
                    Route::get('/{id}', (new InvoiceController)->view(...))->name('.view');
                });
            });

            /* ASSET */
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
            /* INVENTORY */

            /* ASSET TYPES */
            Route::prefix('/asset-types')->group(function (): void {
                Route::name('asset-types')->group(function (): void {
                    /* NEW */
                    Route::get('/create', AssetTypesCreate::class)->name('.create');
                });
            });
            /* ASSET TYPES */

            /* SZÁMLÁZÓ SEGÉD */
            Route::prefix('/invoice-helper')->group(function (): void {
                Route::name('invoice-helper')->group(function (): void {
                    Route::get('/direct-invoicing', DirectInvoicingController::class)->name('.direct-invoicing.index');

                    Route::prefix('/completion-certificate')->name('.completion-certificate')->group(function (): void {
                        Route::get('/all', (new CompletionCertificateController)->all(...))->name('.all');
                        Route::get('/all/download/{date}', (new CompletionCertificateController)->download_all(...))->name('.all.download');
                        Route::get('/companies', (new CompletionCertificateController)->companies(...))->name('.companies');
                    });

                    Route::prefix('/envelope')->name('.envelope')->group(function (): void {
                        Route::get('/all', (new EnvelopeController)->all(...))->name('.all');
                        Route::get('/all/download/{date}', (new EnvelopeController)->download_all(...))->name('.all.download');
                        Route::get('/companies', (new EnvelopeController)->companies(...))->name('.companies');
                    });

                    Route::get('/company-profiles', CompanyProfilesController::class)->name('.company-profiles.index');

                    Route::get('/cgp-data', CgpDataController::class)->name('.cgp-data');

                    Route::get('/contract-holder-company-data', ContractHolderCompanyDataController::class)->name('.contract-holder-company-data');
                });
            });
            /* SZÁMLÁZÓ SEGÉD */
        });
    });
});
