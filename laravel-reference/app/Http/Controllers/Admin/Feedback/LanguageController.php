<?php

namespace App\Http\Controllers\Admin\Feedback;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapLanguage;
use App\Models\Feedback\Language;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::all();
        $eap_online_languages = EapLanguage::all();

        return view('admin.feedback.languages.index', ['languages' => $languages, 'eap_online_languages' => $eap_online_languages]);
    }

    public function store()
    {
        request()->validate([
            'language' => 'required',
        ]);

        if ($eap_language = EapLanguage::query()->where('id', request()->input('language'))->first()) {
            Language::query()->updateOrCreate([
                'code' => $eap_language->code,
            ], [
                'name' => $eap_language->name,
            ]);
        }

        return redirect()->back();
    }
}
