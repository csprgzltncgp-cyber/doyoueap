<?php

use App\Http\Controllers\Employee\CaseController;
use Illuminate\Support\Facades\Route;

Route::get('/employee/case/confirm', [CaseController::class, 'confirm_pending_case'])->middleware('signed')->name('employee.case.confirm');
Route::get('/employee/case', [CaseController::class, 'store_pending_case'])->name('employee.case');
