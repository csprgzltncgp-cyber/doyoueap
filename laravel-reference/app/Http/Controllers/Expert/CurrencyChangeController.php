<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;

class CurrencyChangeController extends Controller
{
    public function __invoke()
    {
        if (auth()->user()->expert_currency_changes()->exists()) {
            return view('expert.currency-change.index', [
                'currency_change' => auth()->user()->expert_currency_changes,
            ]);
        }

        return view('expert.currency-change.create');
    }
}
