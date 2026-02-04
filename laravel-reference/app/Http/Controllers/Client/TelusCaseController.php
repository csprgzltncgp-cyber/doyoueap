<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TelusCaseCode;
use Illuminate\Validation\ValidationException;

class TelusCaseController extends Controller
{
    public function show(TelusCaseCode $code)
    {
        return view('telus-case.show', ['code' => $code]);
    }

    public function download(TelusCaseCode $code)
    {
        request()->validate([
            'code' => 'required|exists:telus_case_codes,code',
        ]);

        if ($code->code !== request()->string('code')->value()) {
            throw ValidationException::withMessages([
                'code' => 'Wrong code!',
            ]);
        }

        $code->update([
            'downloaded_at' => now(),
        ]);

        session()->flash('success', 'Success!');

        return response()->download($code->file);
    }
}
