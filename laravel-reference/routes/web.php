<?php

use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    $host = request()->getHost();
    if (strpos($host, 'staging')) {
        switch ($host) {
            case 'staging.operatordashboard.chestnutce.com':
                return redirect()->route('operator.login');
            case 'staging.expertdashboard.chestnutce.com':
                return redirect()->route('expert.login');
            case 'staging.admindashboard.chestnutce.com':
                return redirect()->route('admin.login');
            case 'staging.clientdashboard.chestnutce.com':
                return redirect()->route('client.what-is-new.select-language');
        }
    } elseif (strpos($host, 'chestnutce') !== false) {
        switch ($host) {
            case 'operator.caredigital.chestnutce.com':
            case 'operatordashboard.chestnutce.com':
                return redirect()->route('operator.login');
            case 'expert.caredigital.chestnutce.com':
            case 'expertdashboard.chestnutce.com':
                return redirect()->route('expert.login');
            case 'admin.caredigital.chestnutce.com':
            case 'admindashboard.chestnutce.com':
                return redirect()->route('admin.login');
            case 'clientdashboard.chestnutce.com':
                return redirect()->route('client.what-is-new.select-language');
        }
    } elseif (strpos($host, 'dashboardsandbox')) {
        switch ($host) {
            case 'operatordashboard.dashboardsandbox.com':
                return redirect()->route('operator.login');
            case 'expertdashboard.dashboardsandbox.com':
                return redirect()->route('expert.login');
            case 'admindashboard.dashboardsandbox.com':
                return redirect()->route('admin.login');
            case 'clientdashboard.dashboardsandbox.com':
                return redirect()->route('client.what-is-new.select-language');
        }
    }

    return redirect()->route('admin.login');
});

Route::get('/logout', LogoutController::class)->name('logout');

require __DIR__.'/roles/admin/admin.php';
require __DIR__.'/roles/admin/account.php';
require __DIR__.'/roles/admin/affiliate-search.php';
require __DIR__.'/roles/admin/eap.php';
require __DIR__.'/roles/admin/financial.php';
require __DIR__.'/roles/admin/production-translating.php';
require __DIR__.'/roles/admin/production.php';
require __DIR__.'/roles/admin/supervisor.php';
require __DIR__.'/roles/admin/todo.php';

require __DIR__.'/roles/client.php';
require __DIR__.'/roles/expert.php';
require __DIR__.'/roles/operator.php';

require __DIR__.'/employee.php';
require __DIR__.'/ajax.php';
