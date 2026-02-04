<?php

use App\Http\Controllers\AccountAdmin\ExpertController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\ActivityPlanCategoryCaseController;
use App\Http\Controllers\Admin\ActivityPlanController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AffiliateSearchController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CaseController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompaniesController;
use App\Http\Controllers\Admin\CrisisController;
use App\Http\Controllers\Admin\CustomerSatisfactionController;
use App\Http\Controllers\Admin\DocumentController;
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
use App\Http\Controllers\Admin\EapOnline\OnsiteConsultationExpertController;
use App\Http\Controllers\Admin\EapOnline\OnsiteConsultationPlaceController;
use App\Http\Controllers\Admin\EapOnline\VideoChatController;
use App\Http\Controllers\Admin\Feedback\FeedbackController;
use App\Http\Controllers\Admin\Google2FaController;
use App\Http\Controllers\Admin\InvoiceHelper\CgpDataController;
use App\Http\Controllers\Admin\InvoiceHelper\CompanyProfilesController;
use App\Http\Controllers\Admin\InvoiceHelper\CompletionCertificateController;
use App\Http\Controllers\Admin\InvoiceHelper\DirectInvoicingController;
use App\Http\Controllers\Admin\InvoiceHelper\EnvelopeController;
use App\Http\Controllers\Admin\LiveWebinarController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\OtherActivityController;
use App\Http\Controllers\Admin\PrizeGame\ContentController;
use App\Http\Controllers\Admin\PrizeGame\ContentTranslationController;
use App\Http\Controllers\Admin\PrizeGame\GameController;
use App\Http\Controllers\Admin\PrizeGame\LanguageController;
use App\Http\Controllers\Admin\PrizeGame\LanguageLinesController;
use App\Http\Controllers\Admin\PrizeGame\LotteryController;
use App\Http\Controllers\Admin\PrizeGame\TypeController;
use App\Http\Controllers\Admin\RiportController;
use App\Http\Controllers\Admin\TaskCommentController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\WorkshopController;
use App\Http\Controllers\BusinessBreakfast\EventController;
use App\Http\Controllers\ContractHolderCompanyDataController;
use App\Http\Livewire\Admin\AffiliateSearch\Edit;
use App\Http\Livewire\Admin\AffiliateSearch\Issued;
use App\Http\Livewire\Admin\AffiliateSearch\Show;
use App\Http\Livewire\Admin\Assets\Create as AssetCreate;
use App\Http\Livewire\Admin\Assets\Index as AssetIndex;
use App\Http\Livewire\Admin\Assets\Storage;
use App\Http\Livewire\Admin\Assets\Waste;
use App\Http\Livewire\Admin\AssetType\Create as AssetTypesCreate;
use App\Http\Livewire\Admin\Company\Create as CompanyCreate;
use App\Http\Livewire\Admin\Company\Edit as CompanyEdit;
use App\Http\Livewire\Admin\CompanyInputEditPage;
use App\Http\Livewire\Admin\LiveWebinar\Create as LiveWebinarCreate;
use App\Http\Livewire\Admin\LiveWebinar\Edit as LiveWebinarEdit;
use App\Http\Livewire\Admin\OnsiteConsultation\Create as OnsiteConsultationCreate;
use App\Http\Livewire\Admin\Operator\Create as OperatorCreate;
use App\Http\Livewire\Admin\Operator\Edit as OperatorEdit;
use App\Http\Livewire\Admin\OtherActivity\CreatePage;
use App\Http\Livewire\Admin\OtherActivity\ShowPage;
use App\Http\Livewire\Admin\Todo\TaskEdit;
use App\Http\Livewire\Admin\Todo\TaskShow;
use App\Http\Livewire\Admin\WorkshopFeedback\Index as WorkshopFeedbackIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('/account_admin')->group(function (): void {
    Route::name('account_admin.')->group(function (): void {
        Route::get('/expert-generate-countries', (new Admin\ExpertController)->expertGenerateCountries(...))->name('expert-generate-countries');
        /* LOGIN VIEW */
        Route::get('/login', (new AdminController)->login(...))->name('login');

        /* LOGIN PROCESS */
        Route::post('/login', (new AdminController)->login_process(...));

        Route::get('/google2fa/back', (new Google2FaController)->back(...))->name('google2fa.back');

        Route::group(['middleware' => ['is_logged_in', 'user_type:account_admin', '2fa']], function (): void {
            /* GOOGLE 2FA */
            Route::get('/google2fa/create', (new Google2FaController)->create(...))->name('google2fa.create');
            Route::post('/google2fa/create', (new Google2FaController)->store(...))->name('google2fa.store');
            Route::post('/google2fa/post', (new Google2FaController)->process(...))->name('google2fa.process');
            /* GOOGLE 2FA */

            /* FORCE CHANGE PASSWORD */
            Route::get('/force-change-password', (new AdminController)->force_change_password(...))->name('force-change-password');
            Route::post('/force-change-password', (new AdminController)->force_change_password_process(...))->name('force-change-password-process');
            /* FORCE CHANGE PASSWORD */

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

            /* SUBMENUS */
            Route::prefix('/menu')->name('submenu')->group(function (): void {
                Route::get('/settings', fn () => view('admin.submenus.settings'))->name('.settings');

                Route::get('/outsources', fn () => view('admin.submenus.outsources'))->name('.outsources');

                Route::get('/riports', fn () => view('admin.submenus.riports'))->name('.riports');

                Route::get('/invoices', fn () => view('admin.submenus.invoices'))->name('.invoices');

                Route::get('/digital', fn () => view('admin.submenus.digital'))->name('.digital');
            });
            /* SUBMENUS */

            /* DATA */
            Route::prefix('/data')->name('data')->group(function (): void {
                Route::view('/', 'admin.data.index')->name('.index');
            });
            /* DATA */

            /* LIST OF EXPERTS */
            Route::prefix('experts')->name('experts')->group(function (): void {
                Route::get('/filter', (new ExpertController)->filter(...))->name('.filter');
                Route::get('/filter-result', (new ExpertController)->filter_result(...))->name('.filter-result');
                Route::get('/{user}', (new ExpertController)->show(...))->name('.show');
                Route::get('/', (new ExpertController)->index(...))->name('.index');
            });
            /* LIST OF EXPERTS */

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

            /* CASES */
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

            /* BUSINESS BREAKFAST */
            Route::prefix('/business-breakfast')->name('business-breakfast')->group(function (): void {
                Route::get('/', (new EventController)->index(...))->name('.index');
                Route::get('/export-notification-requests/{event}', (new EventController)->export_notification_requests(...))->name('.export-notification-requests');
                Route::get('/export-bookings/{event}', (new EventController)->export_bookings(...))->name('.export-bookings');
            });
            /* BUSINESS BREAKFAST */

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
                /* WEBINARS */

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
                    Route::get('/expert', (new OnsiteConsultationExpertController)->index(...))->name('.expert.index');
                    Route::get('/expert/create', (new OnsiteConsultationExpertController)->create(...))->name('.expert.create');
                    Route::post('/expert/create', (new OnsiteConsultationExpertController)->store(...))->name('.expert.store');
                    Route::get('/expert/edit/{expert}', (new OnsiteConsultationExpertController)->edit(...))->name('.expert.edit');
                    Route::post('/expert/edit', (new OnsiteConsultationExpertController)->update(...))->name('.expert.update');
                    Route::post('/expert/delete/{expert}', (new OnsiteConsultationExpertController)->delete(...))->name('.expert.delete');
                });
                /* ONSITE CONSULTATION */
            });
            /* EAP ONLINE */

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

            /* COMPANIES & PERMISSIONS */
            Route::prefix('/companies')->group(function (): void {
                Route::name('companies')->group(function (): void {
                    /* NEW */
                    Route::get('/create', CompanyCreate::class)->name('.new');
                    /* LIST */
                    Route::get('/', (new CompaniesController)->index(...))->name('.list');
                    /* EDIT */
                    Route::get('/edit/{company}', CompanyEdit::class)->name('.edit');

                    /* CASE INPUTS */
                    Route::get('/inputs/{company}', CompanyInputEditPage::class)->name('.inputs');
                    Route::post('/inputs/{id}', [CompaniesController::class, 'inputs_process'])->name('.inputs-process');

                    /* CASE INPUTS VALUES */
                    Route::get('/inputs/{input_id}/values/{company_id}', (new CompaniesController)->input_values(...))->name('.inputs.values');
                    Route::post('/inputs/{input_id}/values/{company_id}', (new CompaniesController)->input_values_process(...))->name('.input-values-process');

                    /* JOGOSULTSÁGOK */
                    Route::prefix('/permissions')->group(function (): void {
                        Route::name('.permissions')->group(function (): void {
                            Route::get('/', (new CompaniesController)->permissions(...))->name('.list');
                            Route::get('/edit/{id}', (new CompaniesController)->permission_edit(...))->name('.edit');
                            Route::post('/edit/{id}', (new CompaniesController)->permission_edit_process(...));
                        });
                    });
                });
            });
            /* COMPANIES & PERMISSIONS */

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

            /* WORKSHOPS FEEDBACK */
            Route::prefix('/workshop-feedback')->group(function (): void {
                Route::name('worksop-feedback')->group(function (): void {
                    Route::get('/', WorkshopFeedbackIndex::class)->name('.index');
                });
            });
            /* WORKSHOPS FEEDBACK */

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

            /* ADMINS */
            Route::prefix('/admins')->group(function (): void {
                Route::name('admins')->group(function (): void {
                    /* LIST */
                    Route::get('/', (new AdminController)->index(...))->name('.list');
                    /* NEW */
                    Route::get('/new', (new AdminController)->create(...))->name('.new');
                    Route::post('/new', (new AdminController)->store(...))->name('.new-process');
                    /* EDIT */
                    Route::get('/edit/{id}', (new AdminController)->edit(...))->name('.edit');
                    Route::post('/edit/{id}', (new AdminController)->edit_process(...))->name('.edit-process');
                });
            });
            /* ADMINS */

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

            /* EAP ONLINE */
            Route::prefix('/eap-online')->name('eap-online')->group(function (): void {
                /* EAP ONLINE RIPORT */
                Route::prefix('/riports')->name('.riports')->group(function (): void {
                    Route::get('/create', (new EapRiportController)->create(...))->name('.create');
                });
                /* EAP ONLINE RIPORT */
            });
            /* EAP ONLINE */

            /* CUSTOMER SATISFACTION */
            Route::prefix('/customer-satisfaction')->group(function (): void {
                Route::name('customer_satisfaction')->group(function (): void {
                    Route::get('/', (new CustomerSatisfactionController)->index(...))->name('.index');
                });
            });
            /* CUSTOMER SATISFACTION */

            /* RIPORTS */
            Route::prefix('/riports')->group(function (): void {
                Route::name('riports')->group(function (): void {
                    Route::post('/show', (new RiportController)->show(...))->name('.show');
                    Route::get('/create', (new RiportController)->create(...))->name('.create');
                    Route::get('/', (new RiportController)->index(...))->name('.index');
                });
            });
            /* RIPORTS */

            Route::prefix('/prizegame')->name('prizegame')->group(function (): void {
                Route::get('/actions', fn () => view('admin.prizegame.actions'))->name('.actions');

                /* LANGUAGES */
                Route::prefix('/languages')->name('.languages')->group(function (): void {
                    Route::get('/', (new LanguageController)->index(...))->name('.index');
                    Route::post('/', (new LanguageController)->store(...))->name('.store');
                });
                /* LANGUAGES */

                /* TYPES */
                Route::prefix('/types')->name('.types')->group(function (): void {
                    Route::get('/', (new TypeController)->index(...))->name('.index');
                    Route::post('/', (new TypeController)->store(...))->name('.store');
                });
                /* TYPES */

                /* PAGES */
                Route::prefix('/pages')->name('.pages')->group(function (): void {
                    Route::get('/create', (new ContentController)->create(...))->name('.create');
                    Route::post('/create', (new ContentController)->store(...))->name('.store');
                    Route::get('/edit/{content}', (new ContentController)->edit(...))->name('.edit');
                    Route::post('/edit/{content}', (new ContentController)->update(...))->name('.update');
                    Route::post('/delete/{content?}', (new ContentController)->delete(...))->name('.delete');
                    Route::post('/has-content-like', (new ContentController)->has_content_like(...))->name('.has_content_like');
                    Route::post('/save-as', (new ContentController)->save_as(...))->name('.save-as');
                    Route::get('/', (new ContentController)->index(...))->name('.index');
                    Route::get('/list/{list}', (new ContentController)->list_content(...))->name('.list');
                });
                /* PAGES */

                /* PRIZE GAMES */
                Route::prefix('/games')->name('.games')->group(function (): void {
                    Route::get('/', (new GameController)->index(...))->name('.index');
                    Route::get('/archived', (new GameController)->archived(...))->name('.archived');
                    Route::post('/is-creatable', (new GameController)->is_creatable(...))->name('.is_creatable');
                    Route::post('/create-from-normal', (new GameController)->create_from_normal(...))->name('.create_from_normal');
                    Route::post('/create-from-specific', (new GameController)->create_from_specific(...))->name('.create_from_specific');
                    Route::post('/set_date', (new GameController)->set_date(...))->name('.set-date');
                    Route::post('/delete/{game?}', (new GameController)->delete(...))->name('.delete');
                });
                /* PRIZE GAMES */

                /* LANGUAGE LINES */
                Route::prefix('/translation')->name('.translation')->group(function (): void {
                    Route::prefix('/system')->name('.system')->group(function (): void {
                        Route::get('/', (new LanguageLinesController)->index(...))->name('.index');
                        Route::post('/', (new LanguageLinesController)->store(...))->name('.store');
                    });

                    Route::prefix('/pages')->name('.pages')->group(function (): void {
                        Route::get('/', (new ContentTranslationController)->index(...))->name('.index');
                        Route::post('/', (new ContentTranslationController)->store(...))->name('.store');
                        Route::get('/{content}', (new ContentTranslationController)->show(...))->name('.show');
                    });
                });
                /* LANGUAGE LINES */

                /* LOTTERY */
                Route::prefix('/lottery')->name('.lottery')->group(function (): void {
                    Route::get('/{game}', (new LotteryController)->show(...))->name('.show');
                    Route::post('/{game?}', (new LotteryController)->store(...))->name('.store');
                    Route::get('/archive/{id}', (new LotteryController)->archive(...))->name('.archive');
                    Route::post('/export/{id}', (new LotteryController)->export(...))->name('.export');
                });
                /* LOTTERY */
            });
            /* PRIZEGAME */

            /* ACTIVITY PLAN */
            Route::prefix('/activity-plan')->name('activity-plan')->group(function (): void {
                Route::get('/{activity_plan?}', (new ActivityPlanController)->index(...))->name('.index');
                Route::get('/edit/{activity_plan}', (new ActivityPlanController)->edit(...))->name('.edit');

                Route::post('/toggle-activity-plan-member', (new ActivityPlanController)->toggle_activity_plan_member(...))->name('.toggle-activity-plan-member');

                Route::prefix('/category/{activity_plan_category}/case')->name('.category.case')->group(function (): void {
                    Route::get('/create/{company}/{country}', (new ActivityPlanCategoryCaseController)->create(...))->name('.create');
                    Route::get('/{activity_plan_category_case}', (new ActivityPlanCategoryCaseController)->show(...))->name('.show');
                });
            });
            /* ACTIVITY PLAN */

            /* FEEDBACK */
            Route::prefix('/feedback')->name('feedback')->group(function (): void {
                Route::get('/actions', fn () => view('admin.feedback.actions'))->name('.actions');

                /* LANGUAGES */
                Route::prefix('/languages')->name('.languages')->group(function (): void {
                    Route::get('/', (new Admin\Feedback\LanguageController)->index(...))->name('.index');
                    Route::post('/', (new Admin\Feedback\LanguageController)->store(...))->name('.store');
                });
                /* LANGUAGES */

                /* LANGUAGE LINES */
                Route::prefix('/translation')->name('.translation')->group(function (): void {
                    Route::prefix('/system')->name('.system')->group(function (): void {
                        Route::get('/', (new Admin\Feedback\LanguageLinesController)->index(...))->name('.index');
                        Route::post('/', (new Admin\Feedback\LanguageLinesController)->store(...))->name('.store');
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
        });
    });
});
