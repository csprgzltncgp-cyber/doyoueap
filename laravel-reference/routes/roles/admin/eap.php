<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AffiliateSearchController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CaseController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CountriesController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\EapOnline\EapMailsController;
use App\Http\Controllers\Admin\EapOnline\EapOnlineController;
use App\Http\Controllers\Admin\EapOnline\EapUsersController;
use App\Http\Controllers\Admin\EapOnline\EapVideoTherapyController;
use App\Http\Controllers\Admin\ExpertController;
use App\Http\Controllers\Admin\Feedback\FeedbackController;
use App\Http\Controllers\Admin\Feedback\LanguageController;
use App\Http\Controllers\Admin\Feedback\LanguageLinesController;
use App\Http\Controllers\Admin\Google2FaController;
use App\Http\Controllers\Admin\LiveWebinarController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\TaskCommentController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\TrainingDashboardController;
use App\Http\Livewire\Admin\AffiliateSearch\Edit;
use App\Http\Livewire\Admin\AffiliateSearch\Issued;
use App\Http\Livewire\Admin\AffiliateSearch\Show;
use App\Http\Livewire\Admin\Assets\Create as AssetCreate;
use App\Http\Livewire\Admin\Assets\Index as AssetIndex;
use App\Http\Livewire\Admin\Assets\Storage;
use App\Http\Livewire\Admin\Assets\Waste;
use App\Http\Livewire\Admin\AssetType\Create as AssetTypesCreate;
use App\Http\Livewire\Admin\Expert\Create as ExpertCreate;
use App\Http\Livewire\Admin\Expert\Edit as ExpertEdit;
use App\Http\Livewire\Admin\LiveWebinar\Create as LiveWebinarCreate;
use App\Http\Livewire\Admin\LiveWebinar\Edit as LiveWebinarEdit;
use App\Http\Livewire\Admin\Operator\Create as OperatorCreate;
use App\Http\Livewire\Admin\Operator\Edit as OperatorEdit;
use App\Http\Livewire\Admin\Todo\TaskEdit;
use App\Http\Livewire\Admin\Todo\TaskShow;
use Illuminate\Support\Facades\Route;

Route::prefix('/eap_admin')->group(function (): void {
    Route::name('eap_admin.')->group(function (): void {
        Route::get('/expert-generate-countries', (new ExpertController)->expertGenerateCountries(...))->name('expert-generate-countries');
        /* LOGIN VIEW */
        Route::get('/login', (new AdminController)->login(...))->name('login');

        /* LOGIN PROCESS */
        Route::post('/login', (new AdminController)->login_process(...));

        Route::get('/google2fa/back', (new Google2FaController)->back(...))->name('google2fa.back');

        Route::middleware(['is_logged_in', 'user_type:eap_admin', '2fa'])->group(function (): void {
            /* GOOGLE 2FA */
            Route::get('/google2fa/create', (new Google2FaController)->create(...))->name('google2fa.create');
            Route::post('/google2fa/create', (new Google2FaController)->store(...))->name('google2fa.store');
            Route::post('/google2fa/post', (new Google2FaController)->process(...))->name('google2fa.process');
            /* GOOGLE 2FA */

            /* FORCE CHANGE PASSWORD */
            Route::get('/force-change-password', (new AdminController)->force_change_password(...))->name('force-change-password');
            Route::post('/force-change-password', (new AdminController)->force_change_password_process(...))->name('force-change-password-process');
            /* FORCE CHANGE PASSWORD */

            /* TRAINING DASHBOARD */
            Route::prefix('/training-dashboard')->name('training-dashboard')->group(function (): void {
                Route::get('/index', (new TrainingDashboardController)->index(...))->name('.index');
                Route::post('/generate-new-password', (new TrainingDashboardController)->generate_new_password(...))->name('.generate_new_password');
            });
            /* TRAINING DASHBOARD */

            /* SUBMENUS */
            Route::prefix('/menu')->name('submenu')->group(function (): void {
                Route::get('/settings', fn () => view('admin.submenus.settings'))->name('.settings');

                Route::get('/outsources', fn () => view('admin.submenus.outsources'))->name('.outsources');

                Route::get('/riports', fn () => view('admin.submenus.riports'))->name('.riports');

                Route::get('/digital', fn () => view('admin.submenus.digital'))->name('.digital');
            });
            /* SUBMENUS */

            /* COUNTRIES */
            Route::prefix('/countries')->name('countries')->group(function (): void {
                Route::get('/', (new CountriesController)->index(...))->name('.index');
                Route::get('/create', (new CountriesController)->create(...))->name('.create');
                Route::post('/create', (new CountriesController)->store(...))->name('.store');
                Route::get('/edit/{country}', (new CountriesController)->edit(...))->name('.edit');
                Route::patch('/edit/{country}', (new CountriesController)->update(...))->name('.update');
                Route::delete('/delete/{country}', (new CountriesController)->delete(...))->name('.delete');
            });
            /* COUNTRIES */

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

            /* CALENDAR */
            Route::prefix('/calendar')->name('calendar')->group(function (): void {
                Route::get('/', (new CalendarController)->index(...))->name('.index');
            });
            /* CALENDAR */

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

            /* TASK COMMENT */
            Route::prefix('/task-comment')->name('task-comment')->group(function (): void {
                Route::post('/create/{task}', (new TaskCommentController)->store(...))->name('.store');
                Route::post('/{taskComment}', (new TaskCommentController)->update(...))->name('.update');
            });
            /* TASK COMMENT */

            /* EAP ONLINE */
            Route::prefix('/eap-online')->name('eap-online')->group(function (): void {
                Route::get('/actions', (new EapOnlineController)->actions(...))->name('.actions');

                /* LIVE WEBINAR */
                Route::prefix('/live-webinar')->name('.live-webinar')->group(function (): void {
                    Route::get('/create', LiveWebinarCreate::class)->name('.create');
                    Route::get('/', [LiveWebinarController::class, 'index'])->name('.index');
                    Route::get('/{live_webinar}', LiveWebinarEdit::class)->name('.edit');
                    Route::post('/', [LiveWebinarController::class, 'store'])->name('.store');
                    Route::post('/{live_webinar}/update', [LiveWebinarController::class, 'update'])->name('.update');
                    Route::post('/{live_webinar}/delete', [LiveWebinarController::class, 'delete'])->name('.delete');
                });
                /* LIVE WEBINAR */

                /* MAILS */
                Route::prefix('/mails')->name('.mails')->group(function (): void {
                    Route::post('/restore-notification', (new EapMailsController)->restore_notification(...))->name('.restore_notification');
                    Route::get('/all', (new EapMailsController)->index(...))->name('.list');
                    Route::get('/{id}', (new EapMailsController)->view(...))->name('.view');
                    Route::post('/{id}', (new EapMailsController)->reply(...))->name('.reply');

                    /* FILTER */
                    Route::prefix('/filter')->name('.filter')->group(function (): void {
                        Route::get('/view', (new EapMailsController)->filter_view(...))->name('.view');
                        Route::post('/result', (new EapMailsController)->filter(...))->name('.result');
                    });
                    /* FILTER */
                });
                /* MAILS */

                /* USERS */
                Route::prefix('/users')->name('.users')->group(function (): void {
                    Route::get('/', (new EapUsersController)->index(...))->name('.list');

                    /* FILTER */
                    Route::prefix('/filter')->name('.filter')->group(function (): void {
                        Route::get('/', (new EapUsersController)->filter_view(...))->name('.view');
                        Route::post('/', (new EapUsersController)->filter(...))->name('.result');
                    });
                    /* FILTER */
                });
                /* USERS */
            });
            /* EAP ONLINE */

            /* FEEDBACK */
            Route::prefix('/feedback')->name('feedback')->group(function (): void {
                Route::get('/actions', fn () => view('admin.feedback.actions'))->name('.actions');

                /* LANGUAGES */
                Route::prefix('/languages')->name('.languages')->group(function (): void {
                    Route::get('/', (new LanguageController)->index(...))->name('.index');
                    Route::post('/', (new LanguageController)->store(...))->name('.store');
                });
                /* LANGUAGES */

                /* LANGUAGE LINES */
                Route::prefix('/translation')->name('.translation')->group(function (): void {
                    Route::prefix('/system')->name('.system')->group(function (): void {
                        Route::get('/', (new LanguageLinesController)->index(...))->name('.index');
                        Route::post('/', (new LanguageLinesController)->store(...))->name('.store');
                    });
                });
                /* LANGUAGE LINES */

                /* FILTER */
                Route::prefix('/filter')->name('.filter')->group(function (): void {
                    Route::get('/view', (new FeedbackController)->filter_view(...))->name('.view');
                    Route::post('/result', (new FeedbackController)->filter(...))->name('.result');
                });
                /* FILTER */

                Route::get('/delete/{feedback}', (new FeedbackController)->delete(...))->name('.delete');
                Route::get('/{feedback}', (new FeedbackController)->show(...))->name('.show');
                Route::get('/{feedback}/set-unread', (new FeedbackController)->set_unread(...))->name('.set-unread');
                Route::post('/{feedback}/reply', (new FeedbackController)->reply(...))->name('.reply');
                Route::get('/', (new FeedbackController)->index(...))->name('.index');
            });
            /* FEEDBACK */

            /* ESETEK */
            Route::prefix('/cases')->group(function (): void {
                Route::name('cases')->group(function (): void {
                    Route::post('/generate-new-cases', (new CaseController)->generate_new_cases(...))->name('.generate-new-cases');
                    Route::get('/filter', (new CaseController)->filter(...))->name('.filter');
                    Route::post('/filter', (new CaseController)->filter_process(...));
                    Route::get('/filtered', (new CaseController)->filtered(...))->name('.filtered');
                    Route::post('/export', (new CaseController)->export(...))->name('.export');
                    Route::post('/delete-all', (new CaseController)->deleteAll(...))->name('.deleteAll');
                    Route::get('/closed', (new CaseController)->list_closed(...))->name('.closed');
                    Route::get('/in-progress', (new CaseController)->list_in_progress(...))->name('.in_progress');
                    Route::get('/edit/{id}', [CaseController::class, 'edit'])->name('.edit');
                    Route::get('/{id}', (new CaseController)->view(...))->name('.view');
                });
            });

            /* DOCUMENTS */
            Route::prefix('/documents')->group(function (): void {
                Route::name('documents')->group(function (): void {
                    Route::get('/', (new DocumentController)->index(...))->name('.list');
                    /* EDIT */
                    Route::get('/edit/{id}', (new DocumentController)->edit(...))->name('.edit');
                    Route::post('/edit/{id}', (new DocumentController)->edit_process(...))->name('.edit-process');

                    Route::get('/new', (new DocumentController)->create(...))->name('.new');
                    Route::post('/new', (new DocumentController)->store(...))->name('.new-process');
                });
            });
            /* DOCUMENTS */

            /* OPERATORS */
            Route::prefix('/operators')->group(function (): void {
                Route::name('operators')->group(function (): void {
                    /* LIST */
                    Route::get('/', (new OperatorController)->index(...))->name('.list');
                    /* NEW */
                    Route::get('/create', OperatorCreate::class)->name('.create');
                    /* EDIT */
                    Route::get('/edit/{user}', OperatorEdit::class)->name('.edit');
                });
            });
            /* OPERATORS */

            /* DASHBOARD VIEW */
            Route::get('/dashboard', (new AdminController)->dashboard(...))->name('dashboard');
            /* DASHBOARD VIEW */

            /* CITIES */
            Route::prefix('/cities')->group(function (): void {
                Route::name('cities')->group(function (): void {
                    Route::get('/', (new CityController)->index(...))->name('.list');
                    /* EDIT */
                    Route::get('/cities/{id}', (new CityController)->edit(...))->name('.edit');
                    Route::post('/cities/{id}', (new CityController)->edit_process(...))->name('.edit-process');

                    Route::get('/cities', (new CityController)->create(...))->name('.new');
                    Route::post('/cities', (new CityController)->store(...))->name('.new-process');
                });
            });
            /* CITIES */

            /* EXPERTS */
            Route::prefix('/experts')->group(function (): void {
                Route::name('experts')->group(function (): void {
                    /* LIST */
                    Route::get('/', (new ExpertController)->index(...))->name('.list');
                    /* NEW */
                    Route::get('/new', ExpertCreate::class)->name('.new');
                    /* EDIT */
                    Route::get('/edit/{user}', ExpertEdit::class)->name('.edit');
                });
            });
            /* EXPERTS */

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

            /* NOTIFICATIONS */
            Route::prefix('/notifications')->group(function (): void {
                Route::name('notifications')->group(function (): void {
                    /* LIST */
                    Route::get('/', (new NotificationController)->index(...))->name('.list');

                    /* NEW */
                    Route::get('/new', (new NotificationController)->create(...))->name('.new');
                    Route::post('/new', (new NotificationController)->store(...))->name('.new-process');

                    /* EDIT */
                    Route::get('/edit/{id}', (new NotificationController)->edit(...))->name('.edit');
                    Route::post('/edit/{id}', (new NotificationController)->edit_process(...))->name('.edit-process');
                });
            });
            /* NOTIFICATIONS */
        });

        Route::prefix('/eap-online')->name('eap-online')->group(function (): void {
            Route::get('/actions', (new EapOnlineController)->actions(...))->name('.actions');

            /* VIDEO CHAT THERAPY */
            Route::prefix('/video-therapy')->name('.video_therapy')->group(function (): void {
                Route::prefix('/actions')->name('.actions')->group(function (): void {
                    Route::get('/', (new EapVideoTherapyController)->actions(...));

                    /* PSYCHOLOGY */
                    Route::prefix('/psychology')->name('.psychology')->group(function (): void {
                        Route::get('/', (new EapVideoTherapyController)->timetable_part_1(...))->name('.timetable');
                        Route::get('/edit', (new EapVideoTherapyController)->timetable_part_2(...))->name('.timetable_edit');
                        Route::post('/save', (new EapVideoTherapyController)->save_appointment(...))->name('.save_appointment');
                        Route::post('/{appointment_id}/edit', (new EapVideoTherapyController)->edit_appointment(...))->name('.edit_appointment');
                        Route::post('/delete', (new EapVideoTherapyController)->delete_appointment(...))->name('.delete_appointment');
                    });
                    /* PSYCHOLOGY */

                    /* PERMISSIONS */
                    Route::prefix('/permissions')->name('.permissions')->group(function (): void {
                        Route::get('/', (new EapVideoTherapyController)->permissions_view(...))->name('.view');
                        Route::post('/', (new EapVideoTherapyController)->permissions_store(...))->name('.store');
                    });
                    /* PERMISSIONS */

                    /* EXPERT DAY OFF */
                    Route::prefix('/expert-day-off')->name('.expert_day_off')->group(function (): void {
                        Route::get('/', (new EapVideoTherapyController)->expert_day_off_1(...))->name('.timetable');
                        Route::get('/edit', (new EapVideoTherapyController)->expert_day_off_2(...))->name('.timetable_edit');
                        Route::post('/save', (new EapVideoTherapyController)->save_day_off(...))->name('.save_day_off');
                        Route::post('/delete', (new EapVideoTherapyController)->delete_day_off(...))->name('.delete_day_off');
                        Route::post('/{expert_day_off_id}/edit', (new EapVideoTherapyController)->edit_day_off(...))->name('.edit_day_off');
                    });
                    /* EXPERT DAY OFF */
                });
            });
            /* VIDEO CHAT THERAPY */

            /* CONNECT OPERATOR COUNTRIES TO EAP LANGUAGES */
            Route::prefix('/connect-countries-to-languages')->name('.connect_countries_to_languages')->group(function (): void {
                Route::get('/', (new EapOnlineController)->connect_countries_to_languages_index(...))->name('.view');
                Route::post('/', (new EapOnlineController)->connect_countries_to_languages(...))->name('.store');
            });
            /* CONNECT OPERATOR COUNTRIES TO EAP LANGUAGES */
        });
    });
});
