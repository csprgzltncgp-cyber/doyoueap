<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class CalendarController extends Controller
{
    // Show calendar
    public function index()
    {
        return view('admin.calendar.index');
    }
}
