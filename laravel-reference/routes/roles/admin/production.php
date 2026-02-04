<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompanyWebsite\ArticlesController;
use App\Http\Controllers\Admin\EapOnline\EapArticlesController;
use App\Http\Controllers\Admin\EapOnline\EapCategoriesController;
use App\Http\Controllers\Admin\EapOnline\EapFilterController;
use App\Http\Controllers\Admin\EapOnline\EapFooterMenuController;
use App\Http\Controllers\Admin\EapOnline\EapMailsController;
use App\Http\Controllers\Admin\EapOnline\EapOnlineController;
use App\Http\Controllers\Admin\EapOnline\EapPodcastsController;
use App\Http\Controllers\Admin\EapOnline\EapPrefixesController;
use App\Http\Controllers\Admin\EapOnline\EapQuizzesController;
use App\Http\Controllers\Admin\EapOnline\EapRiportController;
use App\Http\Controllers\Admin\EapOnline\EapTranslationsController;
use App\Http\Controllers\Admin\EapOnline\EapTranslationStatistics;
use App\Http\Controllers\Admin\EapOnline\EapUsersController;
use App\Http\Controllers\Admin\EapOnline\EapVideosController;
use App\Http\Controllers\Admin\EapOnline\EapVideoTherapyController;
use App\Http\Controllers\Admin\EapOnline\EapWebinarsController;
use App\Http\Controllers\Admin\EapOnline\OnsiteConsultationAppointmentController;
use App\Http\Controllers\Admin\EapOnline\OnsiteConsultationController;
use App\Http\Controllers\Admin\EapOnline\OnsiteConsultationDateController;
use App\Http\Controllers\Admin\EapOnline\OnsiteConsultationPlaceController;
use App\Http\Controllers\Admin\EapOnline\VideoChatController;
use App\Http\Controllers\Admin\ExpertController;
use App\Http\Controllers\Admin\Google2FaController;
use App\Http\Controllers\Admin\LiveWebinarController;
use App\Http\Controllers\Admin\TaskCommentController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Livewire\Admin\Assets\Create as AssetCreate;
use App\Http\Livewire\Admin\Assets\Index as AssetIndex;
use App\Http\Livewire\Admin\Assets\Storage;
use App\Http\Livewire\Admin\Assets\Waste;
use App\Http\Livewire\Admin\AssetType\Create as AssetTypesCreate;
use App\Http\Livewire\Admin\LiveWebinar\Create as LiveWebinarCreate;
use App\Http\Livewire\Admin\LiveWebinar\Edit as LiveWebinarEdit;
use App\Http\Livewire\Admin\OnsiteConsultation\Create as OnsiteConsultationCreate;
use App\Http\Livewire\Admin\Todo\TaskEdit;
use App\Http\Livewire\Admin\Todo\TaskShow;
use Illuminate\Support\Facades\Route;

Route::prefix('/production_admin')->group(function (): void {
    Route::name('production_admin.')->group(function (): void {
        /* LOGIN VIEW */
        Route::get('/login', (new AdminController)->login(...))->name('login');

        /* LOGIN PROCESS */
        Route::post('/login', (new AdminController)->login_process(...));

        Route::get('/google2fa/back', (new Google2FaController)->back(...))->name('google2fa.back');

        Route::middleware(['is_logged_in', 'user_type:production_admin', '2fa'])->group(function (): void {
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
                    Route::get('/new', [ExpertController::class, 'new'])->name('.new');
                    Route::post('/new', [ExpertController::class, 'new_process'])->name('.new-process');
                    /* EDIT */
                    Route::get('/edit/{id}', [ExpertController::class, 'edit'])->name('.edit');
                    Route::post('/edit/{id}', [ExpertController::class, 'edit_process'])->name('.edit-process');
                });
            });
            /* EXPERTS */

            /* COMPANY WEBSITE */
            Route::prefix('/company-website')->name('company-website')->group(function (): void {
                Route::get('/actions', fn () => view('admin.compnay-website.actions'))->name('.actions');

                Route::prefix('/articles')->name('.articles')->group(function (): void {
                    Route::get('/', (new ArticlesController)->index(...))->name('.index');
                    Route::get('/create', (new ArticlesController)->create(...))->name('.create');
                    Route::post('/create', (new ArticlesController)->store(...))->name('.store');

                    Route::get('/edit/{article}', (new ArticlesController)->edit(...))->name('.edit');
                    Route::post('/edit/{article}', (new ArticlesController)->update(...))->name('.update');

                    Route::post('/delete/{article}', (new ArticlesController)->delete(...))->name('.delete');

                    Route::prefix('/translation')->name('.translation')->group(function (): void {
                        Route::get('/', (new ArticlesController)->index(...))->name('.index');
                        Route::get('/edit/{article}', (new ArticlesController)->translation_edit(...))->name('.edit');
                        Route::post('/edit/{article}', (new ArticlesController)->translation_update(...))->name('.update');
                    });
                });
            });
            /* COMPANY WEBSITE */

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

                /* TRANSLATION STATISTICS */
                Route::get('/translation-statistics', EapTranslationStatistics::class)->name('.translation-statistics');
                /* TRANSLATION STATISTICS */

                /* THEME OF THE MONTH */
                Route::prefix('/theme-of-the-month')->name('.theme-of-the-month')->group(function (): void {
                    Route::get('/', (new EapOnlineController)->theme_of_the_month_view(...))->name('.view');
                    Route::post('/', (new EapOnlineController)->theme_of_the_month_store(...))->name('.store');
                    Route::prefix('/translate')->name('.translate')->group(function (): void {
                        Route::get('/', (new EapTranslationsController)->theme_of_the_month_view(...))->name('.view');
                        Route::post('/', (new EapTranslationsController)->theme_of_the_month_store(...))->name('.store');
                    });
                });
                /* THEME OF THE MONTH */

                /* CONTACT INFORMATION */
                Route::prefix('/contact-information')->name('.contact_information')->group(function (): void {
                    Route::get('/', (new EapOnlineController)->contact_information_view(...))->name('.list');
                    Route::post('/', (new EapOnlineController)->contact_information_store(...))->name('.store');
                });
                /* CONTACT INFORMATION */

                /* MENU VISIBILITIES */
                Route::prefix('/menu-visibilities')->name('.menu-visibilities')->group(function (): void {
                    Route::get('/', (new EapOnlineController)->menu_visibilities_view(...))->name('.view');
                    Route::post('/', (new EapOnlineController)->menu_visibilities_store(...))->name('.store');
                });
                /* MENU VISIBILITIES */

                /* LANGUAGES */
                Route::prefix('/languages')->name('.languages')->group(function (): void {
                    Route::get('/', (new EapOnlineController)->languages_view(...))->name('.view');
                    Route::post('/', (new EapOnlineController)->languages_add(...));
                    Route::get('/{id}', (new EapOnlineController)->languages_delete(...))->name('.delete');
                });
                /* LANGUAGES */

                /* TRANSLATIONS */
                Route::prefix('/translation')->name('.translation')->group(function (): void {
                    Route::prefix('/system')->name('.system')->group(function (): void {
                        Route::get('/', (new EapTranslationsController)->system_view(...))->name('.view');
                        Route::post('/', (new EapTranslationsController)->system_store(...))->name('.store');
                    });

                    Route::prefix('/assessment')->name('.assessment')->group(function (): void {
                        Route::get('/', (new EapTranslationsController)->assessment_view(...))->name('.view');
                        Route::post('/', (new EapTranslationsController)->assessment_store(...))->name('.store');
                    });

                    Route::prefix('/well-being')->name('.well-being')->group(function (): void {
                        Route::get('/', (new EapTranslationsController)->well_being_view(...))->name('.view');
                        Route::post('/', (new EapTranslationsController)->well_being_store(...))->name('.store');
                    });
                });
                /* TRANSLATIONS */

                /* ROVAT */
                Route::prefix('/prefixes')->name('.prefixes')->group(function (): void {
                    Route::get('/list', (new EapPrefixesController)->index(...))->name('.list');
                    Route::post('/update', (new EapPrefixesController)->update(...))->name('.update');

                    Route::prefix('/translate')->name('.translate')->group(function (): void {
                        Route::get('/', (new EapPrefixesController)->translate_view(...))->name('.view');
                        Route::post('/', (new EapPrefixesController)->translate_store(...))->name('.store');
                    });
                });
                /* ROVAT */

                /* CATEGORIES */
                Route::prefix('/categories')->name('.categories')->group(function (): void {
                    Route::prefix('/list')->name('.list')->group(function (): void {
                        Route::get('/', fn () => view('admin.eap-online.categories.list.master'));
                        Route::get('/all-articles', (new EapCategoriesController)->list_all_articles_type(...))->name('.all-articles');
                        Route::get('/self-help', (new EapCategoriesController)->list_self_help_type(...))->name('.self-help');
                        Route::get('/all-videos', (new EapCategoriesController)->list_all_videos_type(...))->name('.all-videos');
                        Route::get('/all-podcasts', (new EapCategoriesController)->list_all_podcasts_type(...))->name('.all-podcasts');
                    });
                    Route::post('/update', (new EapCategoriesController)->update(...))->name('.update');

                    Route::prefix('/translate')->name('.translate')->group(function (): void {
                        Route::get('/', (new EapCategoriesController)->translate_view(...))->name('.view');
                        Route::post('/', (new EapCategoriesController)->translate_store(...))->name('.store');
                    });
                });
                /* CATEGORIES */

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

                /* ARTICLES */
                Route::prefix('/articles')->name('.articles')->group(function (): void {
                    Route::get('/all', (new EapArticlesController)->index(...))->name('.list');
                    Route::get('/edit/{id}', (new EapArticlesController)->edit_view(...))->name('.edit_view');
                    Route::post('/edit/{id}', (new EapArticlesController)->edit(...))->name('.edit');
                    Route::post('/delete/{id}', (new EapArticlesController)->delete(...))->name('.delete');
                    Route::post('/new', (new EapArticlesController)->store(...))->name('.new');
                    Route::get('/new', (new EapArticlesController)->create(...))->name('.new_view');

                    /* TRANSLATE ARTICLE */
                    Route::prefix('/translate')->name('.translate')->group(function (): void {
                        Route::get('/', (new EapArticlesController)->index(...))->name('.list');
                        Route::get('/{id}', (new EapArticlesController)->translate_view(...))->name('.view');
                        Route::post('/{id}', (new EapArticlesController)->translate(...))->name('.save');
                    });
                    /* TRANSLATE ARTICLE */
                });
                /* ARTICLES */

                /* VIDEOS */
                Route::prefix('/videos')->name('.videos')->group(function (): void {
                    Route::get('/list', (new EapVideosController)->index(...))->name('.list');
                    Route::get('/new', (new EapVideosController)->create(...))->name('.new_view');
                    Route::post('/new', (new EapVideosController)->store(...))->name('.new');
                    Route::get('/edit/{id}', (new EapVideosController)->edit_view(...))->name('.edit_view');
                    Route::post('/edit/{id}', (new EapVideosController)->edit(...))->name('.edit');
                    Route::post('/delete/{id}', (new EapVideosController)->delete(...))->name('.delete');

                    /* TRANSLATE VIDEO */
                    Route::prefix('/translate')->name('.translate')->group(function (): void {
                        Route::get('/', (new EapVideosController)->translate_list(...))->name('.list');
                        Route::get('/{id}', (new EapVideosController)->translate_view(...))->name('.view');
                        Route::post('/{id}', (new EapVideosController)->translate(...))->name('.save');
                    });
                    /* TRANSLATE VIDEO */
                });
                /* VIDEOS */

                /* WEBINARS */
                Route::prefix('/webinars')->name('.webinars')->group(function (): void {
                    Route::get('/list', (new EapWebinarsController)->index(...))->name('.list');
                    Route::get('/new', (new EapWebinarsController)->create(...))->name('.new_view');
                    Route::post('/new', (new EapWebinarsController)->store(...))->name('.new');
                    Route::get('/edit/{id}', (new EapWebinarsController)->edit_view(...))->name('.edit_view');
                    Route::post('/edit/{id}', (new EapWebinarsController)->edit(...))->name('.edit');
                    Route::post('/delete/{id}', (new EapWebinarsController)->delete(...))->name('.delete');
                });
                /* VIDEOS */

                /* PODCASTS */
                Route::prefix('/podcasts')->name('.podcasts')->group(function (): void {
                    Route::get('/list', (new EapPodcastsController)->index(...))->name('.list');
                    Route::get('/new', (new EapPodcastsController)->create(...))->name('.new_view');
                    Route::post('/new', (new EapPodcastsController)->store(...))->name('.new');
                    Route::get('/edit/{id}', (new EapPodcastsController)->edit_view(...))->name('.edit_view');
                    Route::post('/edit/{id}', (new EapPodcastsController)->edit(...))->name('.edit');
                    Route::post('/delete/{id}', (new EapPodcastsController)->delete(...))->name('.delete');
                });
                /* PODCASTS */

                /* QUIZZES */
                Route::prefix('/quizzes')->name('.quizzes')->group(function (): void {
                    Route::get('/list', (new EapQuizzesController)->index(...))->name('.list');
                    Route::get('/new', (new EapQuizzesController)->create(...))->name('.new_view');
                    Route::post('/new', (new EapQuizzesController)->store(...))->name('.new');
                    Route::get('/edit/{id}', (new EapQuizzesController)->edit_view(...))->name('.edit_view');
                    Route::post('/edit/{id}', (new EapQuizzesController)->edit(...))->name('.edit');
                    Route::post('/delete/{id}', (new EapQuizzesController)->delete(...))->name('.delete');

                    /* TRANSLATE QUIZZES */
                    Route::prefix('/translate')->name('.translate')->group(function (): void {
                        Route::get('/', (new EapQuizzesController)->index(...))->name('.list');
                        Route::get('/{id}', (new EapQuizzesController)->translate_view(...))->name('.view');
                        Route::post('/{id}', (new EapQuizzesController)->translate(...))->name('.save');
                    });
                    /* TRANSLATE QUIZZES */
                });
                /* QUIZZES */

                /* FILTER */
                Route::prefix('{model}/filter')->name('.filter')->group(function (): void {
                    Route::get('/', (new EapFilterController)->filter_view(...))->name('.view');
                    Route::post('/', (new EapFilterController)->filter(...))->name('.result');
                });
                /* FILTER */

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

                /* VIDEO CHAT */
                Route::prefix('/video-chat')->group(function (): void {
                    Route::get('/{client_id}/{room_id}', (new VideoChatController)->index(...))->name('.video_chat');
                });
                /* VIDEO CHAT */

                /* CONNECT OPERATOR COUNTRIES TO EAP LANGUAGES */
                Route::prefix('/connect-countries-to-languages')->name('.connect_countries_to_languages')->group(function (): void {
                    Route::get('/', (new EapOnlineController)->connect_countries_to_languages_index(...))->name('.view');
                    Route::post('/', (new EapOnlineController)->connect_countries_to_languages(...))->name('.store');
                });
                /* CONNECT OPERATOR COUNTRIES TO EAP LANGUAGES */

                /* EAP ONLINE RIPORT */
                Route::prefix('/riports')->name('.riports')->group(function (): void {
                    Route::get('/create', (new EapRiportController)->create(...))->name('.create');
                });
                /* EAP ONLINE RIPORT */

                /* FOOTER MENU & DOCUMENTS */
                Route::prefix('/footer')->name('.footer')->group(function (): void {
                    /*  TRANSLATE DOCUMENT */
                    Route::prefix('/document/translate')->name('.document.translate')->group(function (): void {
                        Route::get('/', (new EapFooterMenuController)->documents_translate_list(...))->name('.list');
                        Route::get('/{id}', (new EapFooterMenuController)->documents_translate_view(...))->name('.view');
                        Route::post('/{id}', (new EapFooterMenuController)->documents_translate_store(...))->name('.store');
                    });
                    /*  TRANSLATE DOCUMENT */

                    Route::prefix('/menu')->name('.menu')->group(function (): void {
                        Route::get('/', (new EapFooterMenuController)->menu_points_index(...))->name('.index');
                        Route::post('/', (new EapFooterMenuController)->menu_points_store(...))->name('.store');

                        /*  TRANSLATE MENU POINT */
                        Route::prefix('/translate')->name('.translate')->group(function (): void {
                            Route::get('/', (new EapFooterMenuController)->menu_points_translate_view(...))->name('.view');
                            Route::post('/', (new EapFooterMenuController)->menu_points_translate_store(...))->name('.store');
                        });
                        /*  TRANSLATE MENU POINT */
                    });
                });
                /* FOOTER MENU & DOCUMENTS */

                /* ONSITE CONSULTATION */
                Route::prefix('/onsite-consultation')->name('.onsite-consultation')->group(function (): void {
                    Route::get('/', (new OnsiteConsultationController)->index(...))->name('.index');
                    Route::get('/create', OnsiteConsultationCreate::class)->name('.create');
                    Route::get('/place', (new OnsiteConsultationPlaceController)->index(...))->name('.place.index');
                    Route::get('/place/edit/{place}', (new OnsiteConsultationPlaceController)->edit(...))->name('.place.edit');
                    Route::post('/place/edit', (new OnsiteConsultationPlaceController)->update(...))->name('.place.update');
                    Route::get('/place/create', (new OnsiteConsultationPlaceController)->create(...))->name('.place.create');
                    Route::post('/place/create', (new OnsiteConsultationPlaceController)->store(...))->name('.place.store');
                    Route::post('/place/delete', (new OnsiteConsultationPlaceController)->delete(...))->name('.place.delete');
                    Route::get('/date/index/{onsite_consultation}', (new OnsiteConsultationDateController)->index(...))->name('.date.index');
                    Route::post('/date/create', (new OnsiteConsultationDateController)->store(...))->name('.date.store');
                    Route::post('/appointment/delete/{appointment}', (new OnsiteConsultationAppointmentController)->delete(...))->name('.appointment.delete');
                    Route::post('/appointment/edit', (new OnsiteConsultationAppointmentController)->edit(...))->name('.appointment.edit');
                });
                /* ONSITE CONSULTATION */
            });
        });
    });
});
