<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OpenInvoicing;

class OpenInvoicingController extends Controller
{
    public function store()
    {
        request()->validate([
            'until' => 'required|date',
            'user_id' => 'required|exists:users,id',
        ]);

        OpenInvoicing::query()->updateOrCreate([
            'user_id' => request()->input('user_id'),
        ], [
            'until' => request()->input('until'),
        ]);

        return response()->noContent();
    }
}
