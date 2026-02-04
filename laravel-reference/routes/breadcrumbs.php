<?php

use App\Models\ActivityPlan;
use App\Models\ActivityPlanCategory;
use App\Models\ActivityPlanCategoryCase;
use App\Models\Asset;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Document;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\OnsiteConsultationExpert;
use App\Models\EapOnline\OnsiteConsultationPlace;
use App\Models\Feedback\Feedback;
use App\Models\LiveWebinar;
use App\Models\Permission;
use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Str;

/* HOME */
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail): void {
    $trail->push(__('common.dashboard'), route(auth()->user()->type.'.dashboard'));
});
/* HOME */

/* CASES */
Breadcrumbs::for('closed-cases', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('common.admin-closed-cases'), route(auth()->user()->type.'.cases.closed'));
});

Breadcrumbs::for('closed-cases.filter', function (BreadcrumbTrail $trail): void {
    $trail->parent('closed-cases');
    $trail->push(__('common.filter'), route(auth()->user()->type.'.cases.filter'));
});

Breadcrumbs::for('closed-cases.filtered', function (BreadcrumbTrail $trail): void {
    $trail->parent('closed-cases.filter');
    $trail->push(__('common.filter-results'), route(auth()->user()->type.'.cases.filtered'));
});

Breadcrumbs::for('in-progress-cases', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('common.cases-in-progress'), route(auth()->user()->type.'.cases.in_progress'));
});

Breadcrumbs::for('cases.view', function (BreadcrumbTrail $trail, $case_id): void {
    if (url()->previous() == route(auth()->user()->type.'.cases.in_progress')) {
        $trail->parent('in-progress-cases');
    } else {
        $trail->parent('closed-cases');
    }

    $trail->push(__('common.case-view'), route(auth()->user()->type.'.cases.view', ['id' => $case_id]));
});

Breadcrumbs::for('cases-summary', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('common.case_summaries'), route(auth()->user()->type.'.cases.summary'));
});
/* CASES */

/* INVOICES */
Breadcrumbs::for('invoices', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.invoices');
    $trail->push(__('common.list-of-invoices'), route(auth()->user()->type.'.invoices.index'));
});

Breadcrumbs::for('invoices.filter', function (BreadcrumbTrail $trail): void {
    $trail->parent('invoices');
    $trail->push(__('common.filter'), route(auth()->user()->type.'.invoices.filter'));
});

Breadcrumbs::for('invoices.filtered', function (BreadcrumbTrail $trail): void {
    $trail->parent('invoices.filter');
    $trail->push(__('common.filter-results'), route(auth()->user()->type.'.invoices.result'));
});

Breadcrumbs::for('invoices.view', function (BreadcrumbTrail $trail, $invoice_id): void {
    $trail->parent('invoices');
    $trail->push(__('common.view-invoice'), route(auth()->user()->type.'.invoices.view', ['id' => $invoice_id]));
});
/* INVOICES */

/* INVOICE HELPER */
Breadcrumbs::for('invoices.direct-invoices', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.invoices');
    $trail->push(__('common.direct-invoices'), route(auth()->user()->type.'.invoice-helper.direct-invoicing.index'));
});
/* INVOICE HELPER */

/* PSYCHOSOCIAL RISK ASSESSMENT */
Breadcrumbs::for('psychosocial-risk-assessment', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.digital');
    $trail->push(__('common.psychosocial_risk_assessment'), route(auth()->user()->type.'.psychosocial-risk-assessment.list'));
});
/* PSYCHOSOCIAL RISK ASSESSMENT */

/* SUBMENUS */
Breadcrumbs::for('submenu.outsources', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('common.submenu.outsources'), route(auth()->user()->type.'.submenu.outsources'));
});

Breadcrumbs::for('submenu.riports', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('common.submenu.riports'), route(auth()->user()->type.'.submenu.riports'));
});

Breadcrumbs::for('submenu.settings', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('common.submenu.settings'), route(auth()->user()->type.'.submenu.settings'));
});

Breadcrumbs::for('submenu.invoices', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('common.submenu.invoices'), route(auth()->user()->type.'.submenu.invoices'));
});

Breadcrumbs::for('submenu.digital', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('common.submenu.digital'), route(auth()->user()->type.'.submenu.digital'));
});
/* SUBMENUS */

/* COMPANIES */
Breadcrumbs::for('companies', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.settings');
    $trail->push(__('common.list_of_companies'), route(auth()->user()->type.'.companies.list'));
});

Breadcrumbs::for('companies.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('companies');
    $trail->push(__('common.create-company'), route(auth()->user()->type.'.companies.new'));
});

Breadcrumbs::for('companies.input-edit', function (BreadcrumbTrail $trail, Company $company): void {
    $trail->parent('companies');
    $trail->push(__('common.edit-of-inputs'), route(auth()->user()->type.'.companies.inputs', ['company' => $company]));
});

Breadcrumbs::for('companies.edit', function (BreadcrumbTrail $trail, Company $company): void {
    $trail->parent('companies');
    $trail->push(__('common.edit'), route(auth()->user()->type.'.companies.edit', ['company' => $company]));
});
/* COMPANIES */

/* COUNTRIES */
Breadcrumbs::for('countries', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.settings');
    $trail->push(__('common.list_of_countries'), route(auth()->user()->type.'.countries.index'));
});

Breadcrumbs::for('countries.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('countries');
    $trail->push(__('common.create-country'), route(auth()->user()->type.'.countries.create'));
});

Breadcrumbs::for('countries.edit', function (BreadcrumbTrail $trail, Country $country): void {
    $trail->parent('countries');
    $trail->push(__('common.edit'), route(auth()->user()->type.'.countries.edit', ['country' => $country]));
});
/* COUNTRIES */

/* CITIES */
Breadcrumbs::for('cities', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.settings');
    $trail->push(__('common.list_of_cities'), route(auth()->user()->type.'.cities.list'));
});

Breadcrumbs::for('cities.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('cities');
    $trail->push(__('common.create-city'), route(auth()->user()->type.'.cities.new'));
});

Breadcrumbs::for('cities.edit', function (BreadcrumbTrail $trail, City $city): void {
    $trail->parent('cities');
    $trail->push(__('common.edit'), route(auth()->user()->type.'.cities.edit', ['id' => $city]));
});
/* CITIES */

/* PERMISSIONS */
Breadcrumbs::for('permissions', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.settings');
    $trail->push(__('common.list_of_permissions'), route(auth()->user()->type.'.companies.permissions.list'));
});

Breadcrumbs::for('permissions.edit', function (BreadcrumbTrail $trail, Company $company): void {
    $trail->parent('permissions');
    $trail->push(__('common.edit'), route(auth()->user()->type.'.companies.permissions.edit', ['id' => $company]));
});
/* PERMISSIONS */

/* ADMINS */
Breadcrumbs::for('admins', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.settings');
    $trail->push(__('common.list_of_admins'), route(auth()->user()->type.'.admins.list'));
});

Breadcrumbs::for('admins.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('admins');
    $trail->push(__('common.create-admin'), route(auth()->user()->type.'.admins.new'));
});

Breadcrumbs::for('admins.edit', function (BreadcrumbTrail $trail, User $admin): void {
    $trail->parent('admins');
    $trail->push(__('common.edit'), route(auth()->user()->type.'.admins.edit', ['id' => $admin]));
});
/* ADMINS */

/* EXPERTS */
Breadcrumbs::for('experts', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.settings');
    $trail->push(__('common.list_of_experts'), route(auth()->user()->type.'.experts.list'));
});

Breadcrumbs::for('experts.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('experts');
    $trail->push(__('common.create-expert'), route(auth()->user()->type.'.experts.new'));
});

Breadcrumbs::for('experts.edit', function (BreadcrumbTrail $trail, User $expert): void {
    $trail->parent('experts');
    $trail->push(__('common.edit'), route(auth()->user()->type.'.experts.edit', ['user' => $expert]));
});
/* EXPERTS */

/* OPERATORS */
Breadcrumbs::for('operators', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.settings');
    $trail->push(__('common.list_of_operators'), route(auth()->user()->type.'.operators.list'));
});

Breadcrumbs::for('operators.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('operators');
    $trail->push(__('common.create-operator'), route(auth()->user()->type.'.operators.create'));
});

Breadcrumbs::for('operators.edit', function (BreadcrumbTrail $trail, User $operator): void {
    $trail->parent('operators');
    $trail->push(__('common.edit'), route(auth()->user()->type.'.operators.edit', ['user' => $operator]));
});
/* OPERATORS */

/* DOCUMENTS */
Breadcrumbs::for('documents', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.settings');
    $trail->push(__('common.list_of_documents'), route(auth()->user()->type.'.documents.list'));
});

Breadcrumbs::for('documents.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('documents');
    $trail->push(__('common.create-document'), route(auth()->user()->type.'.documents.new'));
});

Breadcrumbs::for('documents.edit', function (BreadcrumbTrail $trail, Document $document): void {
    $trail->parent('documents');
    $trail->push(__('common.edit'), route(auth()->user()->type.'.documents.edit', ['id' => $document]));
});
/* DOCUMENTS */

/* WORKSHOPS */
Breadcrumbs::for('workshops', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.outsources');
    $trail->push(__('workshop.workshop'), route(auth()->user()->type.'.workshops.list'));
});

Breadcrumbs::for('workshops.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('workshops');
    $trail->push(__('workshop.new_workshop'), route(auth()->user()->type.'.workshops.new'));
});

Breadcrumbs::for('workshops.filter', function (BreadcrumbTrail $trail): void {
    $trail->parent('workshops');
    $trail->push(__('common.filter'), route(auth()->user()->type.'.workshops.filter'));
});

Breadcrumbs::for('workshops.filtered', function (BreadcrumbTrail $trail): void {
    $trail->parent('workshops.filter');
    $trail->push(__('common.filter-results'), route(auth()->user()->type.'.workshops.result'));
});

Breadcrumbs::for('workshops.view', function (BreadcrumbTrail $trail, $workshop_id): void {
    $trail->parent('workshops');
    $trail->push(__('workshop.view_workshop'), route(auth()->user()->type.'.workshops.view', ['id' => $workshop_id]));
});
/* WORKSHOPS */

/* WORKSHOPS FEEDBACKS */
Breadcrumbs::for('workshop-feedbacks', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.outsources');
    $trail->push(__('common.workshop_feedback'), route(auth()->user()->type.'.worksop-feedback.index'));
});
/* WORKSHOPS FEEDBACKS */

/* CRISIS INTERVENTIONS */
Breadcrumbs::for('crisis', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.outsources');
    $trail->push(__('crisis.crisis'), route(auth()->user()->type.'.crisis.list'));
});

Breadcrumbs::for('crisis.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('crisis');
    $trail->push(__('crisis.new_crisis'), route(auth()->user()->type.'.crisis.new'));
});

Breadcrumbs::for('crisis.filter', function (BreadcrumbTrail $trail): void {
    $trail->parent('crisis');
    $trail->push(__('common.filter'), route(auth()->user()->type.'.crisis.filter'));
});

Breadcrumbs::for('crisis.filtered', function (BreadcrumbTrail $trail): void {
    $trail->parent('crisis.filter');
    $trail->push(__('common.filter-results'), route(auth()->user()->type.'.crisis.result'));
});

Breadcrumbs::for('crisis.view', function (BreadcrumbTrail $trail, $crisis_id): void {
    $trail->parent('crisis');
    $trail->push(__('crisis.view_crisis'), route(auth()->user()->type.'.crisis.view', $crisis_id));
});
/* CRISIS INTERVENTIONS */

/* OTHER ACTIVITIES */
Breadcrumbs::for('other-activities', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.outsources');
    $trail->push(__('other-activity.other-activities'), route(auth()->user()->type.'.other-activities.index'));
});

Breadcrumbs::for('other-activities.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('other-activities');
    $trail->push(__('other-activity.title'), route(auth()->user()->type.'.other-activities.create'));
});

Breadcrumbs::for('other-activities.filter', function (BreadcrumbTrail $trail): void {
    $trail->parent('other-activities');
    $trail->push(__('common.filter'), route(auth()->user()->type.'.other-activities.filter'));
});

Breadcrumbs::for('other-activities.filtered', function (BreadcrumbTrail $trail): void {
    $trail->parent('other-activities.filter');
    $trail->push(__('common.filter-results'), route(auth()->user()->type.'.other-activities.result'));
});

Breadcrumbs::for('other-activities.show', function (BreadcrumbTrail $trail, $other_activity): void {
    $trail->parent('other-activities');
    $trail->push(__('other-activity.view'), route(auth()->user()->type.'.other-activities.show', ['id' => $other_activity]));
});
/* OTHER ACTIVITIES */

/* RIPORTS */
Breadcrumbs::for('client-riports', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.riports');
    $trail->push(__('common.report_generation'), route(auth()->user()->type.'.riports.index'));
});

Breadcrumbs::for('client-riports.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('client-riports');
    $trail->push(__('common.riport-data'), route(auth()->user()->type.'.riports.create'));
});
/* RIPORTS */

/* CUSTOMER SATISFACTIONS */
Breadcrumbs::for('customer-satisfaction', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.riports');
    $trail->push(__('common.satisfaction_indices'), route(auth()->user()->type.'.customer_satisfaction.index'));
});
/* CUSTOMER STATISFACTIONS */

/* EAP ONLINE RIPORTS */
Breadcrumbs::for('eap-online-riports.create', function (BreadcrumbTrail $trail, $from, $to): void {
    $trail->parent('submenu.riports');
    $trail->push('EAP online riport', route(auth()->user()->type.'.eap-online.riports.create', ['from' => $from, 'to' => $to]));
});
/* EAP ONLINE RIPORTS */

/* NOTIFICATIONS */
Breadcrumbs::for('notifications', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('common.notifications'), route(auth()->user()->type.'.notifications.list'));
});

Breadcrumbs::for('notifications.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('notifications');
    $trail->push(__('common.create-notification'), route(auth()->user()->type.'.notifications.list'));
});

Breadcrumbs::for('notifications.edit', function (BreadcrumbTrail $trail, $id): void {
    $trail->parent('notifications');
    $trail->push(__('common.edit'), route(auth()->user()->type.'.notifications.edit', ['id' => $id]));
});
/* NOTIFICATIONS */

/* TODO */
Breadcrumbs::for('todo.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('task.create'), route(auth()->user()->type.'.todo.create'));
});

Breadcrumbs::for('todo.issued', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('task.issued'), route(auth()->user()->type.'.todo.issued'));
});

Breadcrumbs::for('todo.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('task.all_task'), route(auth()->user()->type.'.todo.index'));
});

Breadcrumbs::for('todo.statistics', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('task.statistics'), route(auth()->user()->type.'.todo.statistics'));
});

Breadcrumbs::for('todo.filter', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('common.filter'), route(auth()->user()->type.'.todo.filter'));
});

Breadcrumbs::for('todo.filtered', function (BreadcrumbTrail $trail): void {
    $trail->parent('todo.filter');
    $trail->push(__('common.filter-results'), route(auth()->user()->type.'.todo.filter-result'));
});

Breadcrumbs::for('todo.show', function (BreadcrumbTrail $trail, $task): void {
    $trail->parent('dashboard');
    $trail->push(__('task.task'), route(auth()->user()->type.'.todo.show', ['task' => $task]));
});

Breadcrumbs::for('todo.edit', function (BreadcrumbTrail $trail, $task): void {
    if (url()->previous() == route(auth()->user()->type.'.todo.index')) {
        $trail->parent('todo.index');
    } else {
        $trail->parent('todo.issued', $task);
    }

    $trail->push(__('task.edit'), route(auth()->user()->type.'.todo.edit', ['task' => $task]));
});
/* TODO */

/* AFFILIATE SEARCH WORKFLOW */
Breadcrumbs::for('affiliate-search-workflow', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('affiliate-search-workflow.menu'), route(auth()->user()->type.'.affiliate_searches.index'));
});

Breadcrumbs::for('affiliate-search-workflow.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('affiliate-search-workflow');
    $trail->push(__('affiliate-search-workflow.create'), route(auth()->user()->type.'.affiliate_searches.create'));
});

Breadcrumbs::for('affiliate-search-workflow.issued', function (BreadcrumbTrail $trail): void {
    $trail->parent('affiliate-search-workflow');
    $trail->push(__('affiliate-search-workflow.issued'), route(auth()->user()->type.'.affiliate_searches.issued'));
});

Breadcrumbs::for('affiliate-search-workflow.all', function (BreadcrumbTrail $trail): void {
    $trail->parent('affiliate-search-workflow');
    $trail->push(__('task.all_task'), route(auth()->user()->type.'.affiliate_searches.index'));
});

Breadcrumbs::for('affiliate-search-workflow.statistics', function (BreadcrumbTrail $trail): void {
    $trail->parent('affiliate-search-workflow');
    $trail->push(__('task.statistics'), route(auth()->user()->type.'.affiliate_searches.statistics'));
});

Breadcrumbs::for('affiliate-search-workflow.filter', function (BreadcrumbTrail $trail): void {
    $trail->parent('affiliate-search-workflow');
    $trail->push(__('common.filter'), route(auth()->user()->type.'.affiliate_searches.filter'));
});

Breadcrumbs::for('affiliate-search-workflow.filtered', function (BreadcrumbTrail $trail): void {
    $trail->parent('affiliate-search-workflow.filter');
    $trail->push(__('common.filter-results'), route(auth()->user()->type.'.affiliate_searches.filter-result'));
});

Breadcrumbs::for('affiliate-search-workflow.show', function (BreadcrumbTrail $trail, $affiliateSearch): void {
    $trail->parent('affiliate-search-workflow');
    $trail->push(__('common.show'), route(auth()->user()->type.'.affiliate_searches.show', ['affiliateSearch' => $affiliateSearch]));
});

Breadcrumbs::for('affiliate-search-workflow.edit', function (BreadcrumbTrail $trail, $affiliateSearch): void {
    $trail->parent('affiliate-search-workflow');
    $trail->push(__('common.edit'), route(auth()->user()->type.'.affiliate_searches.edit', ['affiliateSearch' => $affiliateSearch]));
});
/* AFFILIATE SEARCH WORKFLOW */

/* COMPANY WEBSITE */
Breadcrumbs::for('compnay-website', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.digital');
    $trail->push(__('company-website.menu'), route(auth()->user()->type.'.company-website.actions'));
});

Breadcrumbs::for('company-website.articles', function (BreadcrumbTrail $trail): void {
    $trail->parent('compnay-website');
    $trail->push(__('company-website.actions.articles.menu'), route(auth()->user()->type.'.company-website.articles.index'));
});

Breadcrumbs::for('company-website.articles.translation', function (BreadcrumbTrail $trail): void {
    $trail->parent('compnay-website');
    $trail->push(__('company-website.actions.articles.translation'), route(auth()->user()->type.'.company-website.articles.translation.index'));
});

Breadcrumbs::for('company-website.articles.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('company-website.articles');
    $trail->push(__('company-website.actions.articles.create'), route(auth()->user()->type.'.company-website.articles.create'));
});

Breadcrumbs::for('company-website.articles.edit', function (BreadcrumbTrail $trail, $article): void {
    $trail->parent('company-website.articles');
    $trail->push(__('company-website.actions.articles.edit'), route(auth()->user()->type.'.company-website.articles.edit', ['article' => $article]));
});

Breadcrumbs::for('company-website.articles.translation.edit', function (BreadcrumbTrail $trail, $article): void {
    $trail->parent('company-website.articles.translation');
    $trail->push(__('company-website.actions.articles.translation_edit'), route(auth()->user()->type.'.company-website.articles.translation.edit', ['article' => $article]));
});
/* COMPANY WEBSITE */

/* FEEDBACK */
Breadcrumbs::for('feedback', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push('Feedback', route(auth()->user()->type.'.feedback.actions'));
});

Breadcrumbs::for('feedback.languages', function (BreadcrumbTrail $trail): void {
    $trail->parent('feedback');
    $trail->push(__('eap-online.actions.language'), route(auth()->user()->type.'.feedback.languages.index'));
});

Breadcrumbs::for('feedback.messages', function (BreadcrumbTrail $trail): void {
    $trail->parent('feedback');
    $trail->push(__('feedback.menu'), route(auth()->user()->type.'.feedback.index'));
});

Breadcrumbs::for('feedback.messages.show', function (BreadcrumbTrail $trail, Feedback $feedback): void {
    $trail->parent('feedback.messages');
    $trail->push(__('feedback.message'), route(auth()->user()->type.'.feedback.show', ['feedback' => $feedback]));
});

Breadcrumbs::for('feedback.messages.filter', function (BreadcrumbTrail $trail): void {
    $trail->parent('feedback.messages');
    $trail->push(__('common.filter'), route(auth()->user()->type.'.feedback.filter.view'));
});

Breadcrumbs::for('feedback.messages.filtered', function (BreadcrumbTrail $trail): void {
    $trail->parent('feedback.messages.filter');
    $trail->push(__('common.filter-results'), route(auth()->user()->type.'.feedback.filter.result'));
});

Breadcrumbs::for('feedback.translations', function (BreadcrumbTrail $trail): void {
    $trail->parent('feedback');
    $trail->push(__('myeap.system.translations'), route(auth()->user()->type.'.feedback.translation.system.index'));
});
/* FEEDBACK */

/* PRIZEGAME */
Breadcrumbs::for('prizegame', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.digital');
    $trail->push(explode(' ', __('prizegame.actions.title'))[0], route(auth()->user()->type.'.prizegame.actions'));
});

Breadcrumbs::for('prizegame.languages', function (BreadcrumbTrail $trail): void {
    $trail->parent('prizegame');
    $trail->push(__('eap-online.actions.language'), route(auth()->user()->type.'.prizegame.languages.index'));
});

Breadcrumbs::for('prizegame.types', function (BreadcrumbTrail $trail): void {
    $trail->parent('prizegame');
    $trail->push(__('prizegame.types.menu'), route(auth()->user()->type.'.prizegame.types.index'));
});

Breadcrumbs::for('prizegame.pages', function (BreadcrumbTrail $trail): void {
    $trail->parent('prizegame');
    $trail->push(__('prizegame.pages.menu'), route(auth()->user()->type.'.prizegame.pages.index'));
});

Breadcrumbs::for('prizegame.pages.list', function (BreadcrumbTrail $trail): void {
    $trail->parent('prizegame.pages');
    $trail->push(__('prizegame.pages.list'), route(auth()->user()->type.'.prizegame.pages.list', ['list' => 'templates']));
});

Breadcrumbs::for('prizegame.pages.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('prizegame.pages');
    $trail->push(__('prizegame.pages.new'), route(auth()->user()->type.'.prizegame.pages.create'));
});

Breadcrumbs::for('prizegame.pages.edit', function (BreadcrumbTrail $trail, $content): void {
    $trail->parent('prizegame.pages');
    $trail->push(__('prizegame.pages.edit'), route(auth()->user()->type.'.prizegame.pages.edit', ['content' => $content]));
});

Breadcrumbs::for('prizegame.games', function (BreadcrumbTrail $trail): void {
    $trail->parent('prizegame');
    $trail->push(__('prizegame.games.running_menu'), route(auth()->user()->type.'.prizegame.games.index'));
});

Breadcrumbs::for('prizegame.games.archived', function (BreadcrumbTrail $trail): void {
    $trail->parent('prizegame');
    $trail->push(__('prizegame.games.archived_menu'), route(auth()->user()->type.'.prizegame.games.archived'));
});

Breadcrumbs::for('prizegame.translations.system', function (BreadcrumbTrail $trail): void {
    $trail->parent('prizegame');
    $trail->push(__('myeap.system.translations'), route(auth()->user()->type.'.prizegame.translation.system.index'));
});

Breadcrumbs::for('prizegame.translations.pages', function (BreadcrumbTrail $trail): void {
    $trail->parent('prizegame');
    $trail->push(__('prizegame.pages.menu'), route(auth()->user()->type.'.prizegame.translation.pages.index'));
});
/* PRIZEGAME */

/* DATA */
Breadcrumbs::for('data', function (BreadcrumbTrail $trail): void {
    if (auth()->user()->type === 'financial_admin') {
        $trail->parent('dashboard');
    } else {
        $trail->parent('submenu.digital');
    }
    $trail->push(explode('-', __('data.menu'))[0], route(auth()->user()->type.'.data.index'));
});
/* DATA */

/* EAP ONLINE */
Breadcrumbs::for('eap-online', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.digital');
    $trail->push(explode('-', __('eap-online.actions.title'))[0], route(auth()->user()->type.'.eap-online.actions'));
});

Breadcrumbs::for('eap-online.languages', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.actions.language'), route(auth()->user()->type.'.eap-online.languages.view'));
});

Breadcrumbs::for('eap-online.users', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(explode('-', __('eap-online.users.title'))[1], route(auth()->user()->type.'.eap-online.users.list'));
});

Breadcrumbs::for('eap-online.users.filter', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.users');
    $trail->push(__('common.filter'), route(auth()->user()->type.'.eap-online.users.filter.view'));
});

Breadcrumbs::for('eap-online.users.filtered', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.users.filter');
    $trail->push(__('common.filter-results'), route(auth()->user()->type.'.eap-online.users.filter.result'));
});

Breadcrumbs::for('eap-online.categories', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.categories.edit'), route(auth()->user()->type.'.eap-online.categories.list'));
});

Breadcrumbs::for('eap-online.perfixes', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.prefix.menu'), route(auth()->user()->type.'.eap-online.prefixes.list'));
});

Breadcrumbs::for('eap-online.articles', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.articles.menu'), route(auth()->user()->type.'.eap-online.articles.list'));
});

Breadcrumbs::for('eap-online.articles.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.articles');
    $trail->push(__('eap-online.articles.new'), route(auth()->user()->type.'.eap-online.articles.new'));
});

Breadcrumbs::for('eap-online.articles.edit', function (BreadcrumbTrail $trail, $article): void {
    $trail->parent('eap-online.articles');
    $trail->push(__('eap-online.articles.edit'), route(auth()->user()->type.'.eap-online.articles.edit', ['id' => $article]));
});

Breadcrumbs::for('eap-online.videos', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.videos.menu'), route(auth()->user()->type.'.eap-online.videos.list'));
});

Breadcrumbs::for('eap-online.videos.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.videos');
    $trail->push(__('eap-online.videos.add'), route(auth()->user()->type.'.eap-online.videos.new'));
});

Breadcrumbs::for('eap-online.videos.edit', function (BreadcrumbTrail $trail, $video): void {
    $trail->parent('eap-online.videos');
    $trail->push(__('eap-online.videos.edit'), route(auth()->user()->type.'.eap-online.videos.edit', ['id' => $video]));
});

Breadcrumbs::for('eap-online.live-webinars', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.live-webinars.menu'), route(auth()->user()->type.'.eap-online.live-webinar.index'));
});

Breadcrumbs::for('eap-online.live-webinars.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.live-webinars');
    $trail->push(__('eap-online.live-webinars.add'), route(auth()->user()->type.'.eap-online.live-webinar.create'));
});

Breadcrumbs::for('eap-online.live-webinars.edit', function (BreadcrumbTrail $trail, LiveWebinar $live_webinar): void {
    $trail->parent('eap-online.live-webinars');
    $trail->push(__('eap-online.live-webinars.edit'), route(auth()->user()->type.'.eap-online.live-webinar.edit', $live_webinar));
});

Breadcrumbs::for('eap-online.webinars', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.webinars.menu'), route(auth()->user()->type.'.eap-online.webinars.list'));
});

Breadcrumbs::for('eap-online.webinars.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.webinars');
    $trail->push(__('eap-online.webinars.add'), route(auth()->user()->type.'.eap-online.webinars.new'));
});

Breadcrumbs::for('eap-online.webinars.edit', function (BreadcrumbTrail $trail, $webinar): void {
    $trail->parent('eap-online.webinars');
    $trail->push(__('eap-online.webinars.edit'), route(auth()->user()->type.'.eap-online.webinars.edit', ['id' => $webinar]));
});

Breadcrumbs::for('eap-online.podcasts', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.podcasts.menu'), route(auth()->user()->type.'.eap-online.podcasts.list'));
});

Breadcrumbs::for('eap-online.podcasts.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.podcasts');
    $trail->push(__('eap-online.podcasts.add'), route(auth()->user()->type.'.eap-online.podcasts.new'));
});

Breadcrumbs::for('eap-online.podcasts.edit', function (BreadcrumbTrail $trail, $podcast): void {
    $trail->parent('eap-online.podcasts');
    $trail->push(__('eap-online.podcasts.edit'), route(auth()->user()->type.'.eap-online.podcasts.edit', ['id' => $podcast]));
});

Breadcrumbs::for('eap-online.quizzes', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.quizzes.menu'), route(auth()->user()->type.'.eap-online.quizzes.list'));
});

Breadcrumbs::for('eap-online.quizzes.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.quizzes');
    $trail->push(__('eap-online.quizzes.new'), route(auth()->user()->type.'.eap-online.quizzes.new'));
});

Breadcrumbs::for('eap-online.quizzes.edit', function (BreadcrumbTrail $trail, $quiz): void {
    $trail->parent('eap-online.quizzes');
    $trail->push(__('eap-online.quizzes.edit'), route(auth()->user()->type.'.eap-online.quizzes.edit', ['id' => $quiz]));
});

Breadcrumbs::for('eap-online.contact-information', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.contact_information.menu'), route(auth()->user()->type.'.eap-online.contact_information.list'));
});

Breadcrumbs::for('eap-online.theme-of-the-month', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.theme_of_the_month.menu'), route(auth()->user()->type.'.eap-online.theme-of-the-month.view'));
});

Breadcrumbs::for('eap-online.translation-statistics', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.translation_statistics.menu'), route(auth()->user()->type.'.eap-online.translation-statistics'));
});

Breadcrumbs::for('eap-online.video-therapy', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.video_therapy.menu'), route(auth()->user()->type.'.eap-online.video_therapy.actions'));
});

Breadcrumbs::for('eap-online.video-therapy.schedule', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.video-therapy');
    $trail->push(__('eap-online.video_therapy.video_chat_appointments'), route(auth()->user()->type.'.eap-online.video_therapy.actions.psychology.timetable'));
});

Breadcrumbs::for('eap-online.video-therapy.schedule.edit', function (BreadcrumbTrail $trail, $language_id, $permission_id): void {
    $language = EapLanguage::query()->find($language_id);
    $permission = Permission::query()->find($permission_id);

    $trail->parent('eap-online.video-therapy.schedule');
    $trail->push($language->name.' ('.$permission->slug.')', route(auth()->user()->type.'.eap-online.video_therapy.actions.psychology.timetable_edit'));
});

Breadcrumbs::for('eap-online.video-therapy.actions.expert_day_off.edit', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.video-therapy');
    $trail->push(__('eap-online.video_therapy.expert_day_off'), route(auth()->user()->type.'.eap-online.video_therapy.actions.expert_day_off.timetable_edit'));
});

Breadcrumbs::for('eap-online.video-therapy.connect_countries_to_languages', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.video-therapy');
    $trail->push(__('eap-online.video_therapy.video_chat_experts'), route(auth()->user()->type.'.eap-online.connect_countries_to_languages.view'));
});

Breadcrumbs::for('eap-online.video-therapy.actions.permissions', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.video-therapy');
    $trail->push(__('eap-online.video_therapy.permissions'), route(auth()->user()->type.'.eap-online.video_therapy.actions.permissions.view'));
});

Breadcrumbs::for('eap-online.footer', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.footer.menu_points.menu'), route(auth()->user()->type.'.eap-online.footer.menu.index'));
});

Breadcrumbs::for('eap-online.mails', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(explode('-', __('eap-online.mails.menu'))[1], route(auth()->user()->type.'.eap-online.mails.list'));
});

Breadcrumbs::for('eap-online.mails.filter', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.mails');
    $trail->push(__('common.filter'), route(auth()->user()->type.'.eap-online.mails.filter.view'));
});

Breadcrumbs::for('eap-online.mails.filtered', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.mails.filter');
    $trail->push(__('common.filter-results'), route(auth()->user()->type.'.eap-online.mails.filter.result'));
});

Breadcrumbs::for('eap-online.mails.show', function (BreadcrumbTrail $trail, $mail): void {
    $trail->parent('eap-online.mails');
    $trail->push(__('eap-online.mails.message'), route(auth()->user()->type.'.eap-online.mails.view', ['id' => $mail]));
});

Breadcrumbs::for('eap-online.menu-visibilities', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.menu-visibilities.menu'), route(auth()->user()->type.'.eap-online.menu-visibilities.view'));
});

Breadcrumbs::for('eap-online.translate-articles', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.articles.translate'), route(auth()->user()->type.'.eap-online.articles.translate.list'));
});

Breadcrumbs::for('eap-online.translate-articles.view', function (BreadcrumbTrail $trail, $article): void {
    $trail->parent('eap-online.translate-articles');
    $trail->push(Str::limit($article->getSectionByType('headline'), 75), route(auth()->user()->type.'.eap-online.articles.translate.view', ['id' => $article]));
});

Breadcrumbs::for('eap-online.translate-videos', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.videos.translate'), route(auth()->user()->type.'.eap-online.videos.translate.list'));
});

Breadcrumbs::for('eap-online.translate-videos.view', function (BreadcrumbTrail $trail, $video): void {
    $trail->parent('eap-online.translate-videos');
    $trail->push(Str::limit($video->short_title, 75), route(auth()->user()->type.'.eap-online.videos.translate.view', ['id' => $video]));
});

Breadcrumbs::for('eap-online.translate-quizzes', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.quizzes.translate'), route(auth()->user()->type.'.eap-online.quizzes.translate.list'));
});

Breadcrumbs::for('eap-online.translate-quizzes.view', function (BreadcrumbTrail $trail, $quiz): void {
    $trail->parent('eap-online.translate-quizzes');
    $trail->push(Str::limit($quiz->title_translations()->where('language_id', $quiz->input_language)->first()->value, 75), route(auth()->user()->type.'.eap-online.quizzes.translate.view', ['id' => $quiz]));
});

Breadcrumbs::for('eap-online.translate-categories', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(explode('-', __('eap-online.categories.title'))[1], route(auth()->user()->type.'.eap-online.categories.translate.view'));
});

Breadcrumbs::for('eap-online.translate-prefixes', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.prefix.translate'), route(auth()->user()->type.'.eap-online.prefixes.translate.view'));
});

Breadcrumbs::for('eap-online.translate-theme-of-the-month', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.theme_of_the_month.translate'), route(auth()->user()->type.'.eap-online.theme-of-the-month.translate.view'));
});

Breadcrumbs::for('eap-online.translate-footer-menu', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.footer.menu_points.translate'), route(auth()->user()->type.'.eap-online.footer.menu.translate.view'));
});

Breadcrumbs::for('eap-online.translate-footer-documents', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.footer.documents.translate'), route(auth()->user()->type.'.eap-online.footer.document.translate.list'));
});

Breadcrumbs::for('eap-online.translate-footer-document', function (BreadcrumbTrail $trail, $menu_point): void {
    $trail->parent('eap-online.translate-footer-documents');
    $trail->push(Str::limit($menu_point->firstTranslation->value, 75), route(auth()->user()->type.'.eap-online.footer.document.translate.view', ['id' => $menu_point]));
});

Breadcrumbs::for('eap-online.translate-system', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(explode('-', __('eap-online.system.title'))[1], route(auth()->user()->type.'.eap-online.translation.system.view'));
});

Breadcrumbs::for('eap-online.translate-assessment', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push('Assessment', route(auth()->user()->type.'.eap-online.translation.assessment.view'));
});

Breadcrumbs::for('eap-online.translate-well-being', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push('Well-Being', route(auth()->user()->type.'.eap-online.translation.well-being.view'));
});

Breadcrumbs::for('eap-online.onsite-consultation', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online');
    $trail->push(__('eap-online.onsite_consultation.menu'), route(auth()->user()->type.'.eap-online.onsite-consultation.index'));
});

Breadcrumbs::for('eap-online.onsite-consultation.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.onsite-consultation');
    $trail->push(__('eap-online.onsite_consultation.new_consultatiom'), route(auth()->user()->type.'.eap-online.onsite-consultation.create'));
});

Breadcrumbs::for('eap-online.onsite-consultation.date.index', function (BreadcrumbTrail $trail, $onsite_consultation): void {
    $trail->parent('eap-online.onsite-consultation');
    $trail->push(__('eap-online.onsite_consultation.edit_appointments'), route(auth()->user()->type.'.eap-online.onsite-consultation.date.index', $onsite_consultation));
});
Breadcrumbs::for('eap-online.onsite-consultation.place.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.onsite-consultation');
    $trail->push(__('eap-online.onsite_consultation.places'), route(auth()->user()->type.'.eap-online.onsite-consultation.place.index'));
});
Breadcrumbs::for('eap-online.onsite-consultation.place.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.onsite-consultation.place.index');
    $trail->push(__('eap-online.onsite_consultation.new_place'), route(auth()->user()->type.'.eap-online.onsite-consultation.place.create'));
});
Breadcrumbs::for('eap-online.onsite-consultation.place.edit', function (BreadcrumbTrail $trail, OnsiteConsultationPlace $onsite_consultation_place): void {
    $trail->parent('eap-online.onsite-consultation.place.index');
    $trail->push(__('eap-online.onsite_consultation.edit_place'), route(auth()->user()->type.'.eap-online.onsite-consultation.place.edit', $onsite_consultation_place));
});
Breadcrumbs::for('eap-online.onsite-consultation.expert.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.onsite-consultation');
    $trail->push(__('eap-online.onsite_consultation.experts'), route(auth()->user()->type.'.eap-online.onsite-consultation.expert.index'));
});
Breadcrumbs::for('eap-online.onsite-consultation.expert.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('eap-online.onsite-consultation.expert.index');
    $trail->push(__('eap-online.onsite_consultation.new_expert'), route(auth()->user()->type.'.eap-online.onsite-consultation.expert.create'));
});
Breadcrumbs::for('eap-online.onsite-consultation.expert.edit', function (BreadcrumbTrail $trail, OnsiteConsultationExpert $onsite_consultation_expert): void {
    $trail->parent('eap-online.onsite-consultation.expert.index');
    $trail->push(__('eap-online.onsite_consultation.edit_expert'), route(auth()->user()->type.'.eap-online.onsite-consultation.expert.edit', $onsite_consultation_expert));
});
/* EAP ONLINE */

/* ASSET */
Breadcrumbs::for('assets', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('common.assets'), route(auth()->user()->type.'.assets.menu'));
});

Breadcrumbs::for('assets.list', function (BreadcrumbTrail $trail): void {
    $trail->parent('assets');
    $trail->push(__('asset.equipments'), route(auth()->user()->type.'.assets.index'));
});

Breadcrumbs::for('assets.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('assets.list');
    $trail->push(__('common.add-new-equipment'), route(auth()->user()->type.'.assets.create'));
});

Breadcrumbs::for('assets.create-type', function (BreadcrumbTrail $trail): void {
    $trail->parent('assets.list');
    $trail->push(__('common.create-type'), route(auth()->user()->type.'.asset-types.create'));
});

Breadcrumbs::for('waste', function (BreadcrumbTrail $trail): void {
    $trail->parent('assets');
    $trail->push(__('common.waste'), route(auth()->user()->type.'.assets.waste'));
});

Breadcrumbs::for('storage', function (BreadcrumbTrail $trail): void {
    $trail->parent('assets');
    $trail->push(__('common.storage'), route(auth()->user()->type.'.assets.storage'));
});

Breadcrumbs::for('assets.input-edit', function (BreadcrumbTrail $trail, Asset $assets): void {
    $trail->parent('assets');
    $trail->push(__('common.edit-of-inputs'), route(auth()->user()->type.'.assets.inputs', ['assets' => $assets]));
});

Breadcrumbs::for('assets.edit', function (BreadcrumbTrail $trail, Asset $assets): void {
    $trail->parent('assets');
    $trail->push(__('common.edit'), route(auth()->user()->type.'.assets.edit', ['assets' => $assets]));
});

/* INVENTORY */

/* BUSINESS BREAKFAST */
Breadcrumbs::for('business-breakfast', function (BreadcrumbTrail $trail): void {
    $trail->parent('submenu.digital');
    $trail->push('Business Breakfast', route(auth()->user()->type.'.business-breakfast.index'));
});
/* BUSINESS BREAKFAST */

/* TODO CALENDAR */
Breadcrumbs::for('todo-calendar', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('common.calendar'), route(auth()->user()->type.'.calendar.index'));
});
/* TODO CALENDAR */

/* ACTIVITY PLAN */
Breadcrumbs::for('activity-plan.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('dashboard');
    $trail->push(__('activity-plan.menu'), route(auth()->user()->type.'.activity-plan.index'));
});

Breadcrumbs::for('activity-plan.edit', function (BreadcrumbTrail $trail, ActivityPlan $activity_plan): void {
    $trail->parent('activity-plan.index');
    $trail->push(__('activity-plan.create-edit-category'), route(auth()->user()->type.'.activity-plan.edit', ['activity_plan' => $activity_plan]));
});

Breadcrumbs::for('activity-plan.category.case.create', function (BreadcrumbTrail $trail, ActivityPlanCategory $activity_plan_category, Company $company, Country $country): void {
    $trail->parent('activity-plan.index');
    $trail->push(
        __('activity-plan.create-new-category-case-breadcrumb', ['company' => $company->name, 'country' => $country->name]),
        route(auth()->user()->type.'.activity-plan.category.case.create', ['activity_plan_category' => $activity_plan_category, 'company' => $company, 'country' => $country])
    );
});

Breadcrumbs::for('activity-plan.category.case.show', function (BreadcrumbTrail $trail, ActivityPlanCategory $activity_plan_category, ActivityPlanCategoryCase $activity_plan_category_case): void {
    $trail->parent('activity-plan.index');
    $trail->push(__('activity-plan.show-category-case'), route(auth()->user()->type.'.activity-plan.category.case.show', ['activity_plan_category' => $activity_plan_category, 'activity_plan_category_case' => $activity_plan_category_case]));
});
/* ACTIVITY PLAN */
