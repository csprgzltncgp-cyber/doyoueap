<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;

class BaseExpertController extends Controller
{
    protected $user_notifications;

    public function __construct()
    {
        parent::__construct();
    }
}
