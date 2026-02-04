<?php

namespace App\Http\Controllers\Admin\PrizeGame;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapLanguage;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = EapLanguage::all();
        $eap_online_languages = EapLanguage::all();

        return view('admin.prizegame.languages.index', ['languages' => $languages, 'eap_online_languages' => $eap_online_languages]);
    }

    public function store()
    {
        request()->validate([
            'language' => 'required',
        ]);

        if ($eap_language = EapLanguage::query()->where('id', request()->input('language'))->first()) {
            EapLanguage::query()->updateOrCreate([
                'code' => $eap_language->code,
            ], [
                'name' => $eap_language->name,
            ]);
        }

        return redirect()->back();
    }
}
