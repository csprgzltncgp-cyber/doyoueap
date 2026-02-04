<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ContractHolder;
use App\Models\Country;
use App\Models\EapOnline\EapContactInformation;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapSetting;
use App\Models\EapOnline\EapTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EapOnlineController extends Controller
{
    public function actions()
    {
        return view('admin.eap-online.actions');
    }

    public function languages_view()
    {
        $languages = EapLanguage::all();

        return view('admin.eap-online.languages', [
            'languages' => $languages,
        ]);
    }

    public function languages_add(Request $request)
    {
        EapLanguage::query()->create([
            'name' => $request->get('name'),
            'code' => Str::lower($request->get('code')),
        ]);

        return redirect()->route('admin.eap-online.languages.view');
    }

    public function languages_delete($id)
    {
        EapLanguage::destroy($id);

        return redirect()->route('admin.eap-online.languages.view');
    }

    public function menu_visibilities_view()
    {
        $contract_holders = ContractHolder::query()->get();

        foreach ($contract_holders as $contract_holder) {
            $contract_holder->setAttribute('companies', $contract_holder->companies());
        }

        return view('admin.eap-online.menu-visibilities.view', ['contract_holders' => $contract_holders]);
    }

    public function menu_visibilities_store(Request $request)
    {
        $company_id = (int) $request->get('company_id');
        $menu_item_id = (int) $request->get('menu_item_id');
        $visible = ($request->get('visible') === 'true');

        if (
            ! DB::connection('mysql_eap_online')->table('company_menu_item')->where(['company_id' => $company_id, 'menu_item_id' => $menu_item_id])->exists()
            &&
            $visible
        ) {
            DB::connection('mysql_eap_online')->table('company_menu_item')->insert([
                'company_id' => $company_id,
                'menu_item_id' => $menu_item_id,
            ]);
        }
        if (! DB::connection('mysql_eap_online')->table('company_menu_item')->where(['company_id' => $company_id, 'menu_item_id' => $menu_item_id])->exists()) {
            return response(['status' => 'ok']);
        }
        if ($visible) {
            return response(['status' => 'ok']);
        }
        DB::connection('mysql_eap_online')->table('company_menu_item')->where([
            'company_id' => $company_id,
            'menu_item_id' => $menu_item_id,
        ])->delete();

        return response(['status' => 'ok']);
    }

    public function contact_information_view()
    {
        $country_infos = EapContactInformation::query()->where('company_id', null)->where('country_id', '<>', 0)->get();
        $company_infos = EapContactInformation::query()->where('company_id', '<>', null)->get();
        $default = EapContactInformation::query()->where(['company_id' => null, 'country_id' => null])->first();

        $contract_holders = ContractHolder::query()->get();

        foreach ($contract_holders as $contract_holder) {
            $contract_holder->setAttribute('companies', $contract_holder->companies());
        }

        return view('admin.eap-online.contact_information.list', ['country_infos' => $country_infos, 'company_infos' => $company_infos, 'contract_holders' => $contract_holders, 'default' => $default]);
    }

    // for contact information
    public function get_countries_by_company($company_id)
    {
        try {
            if ($company_id == 'null') {
                return Country::all();
            }

            $company = Company::query()->where('id', $company_id)->with('countries')->first();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return $company->countries;
    }

    public function contact_information_store(Request $request)
    {
        EapContactInformation::query()->updateOrCreate([
            'company_id' => null,
            'country_id' => null,
        ], [
            'email' => $request->input('default')['email'],
            'phone' => $request->input('default')['phone'],
        ]);

        $this->set_contact_information($request->get('only_country'));
        $this->set_contact_information($request->get('country'), true);
        $this->set_contact_information($request->get('new'), false, true);

        return redirect()->back();
    }

    public function delete_contact_information($id): void
    {
        try {
            $contact_information = EapContactInformation::query()->findOrFail($id);
            $contact_information->delete();
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    public function theme_of_the_month_view()
    {
        $languages = EapLanguage::all();
        $theme_of_the_month_language = EapSetting::query()->where('name', 'theme_of_the_month_language')->first();
        if ($theme_of_the_month_language) {
            $theme_of_the_month_text = EapTranslation::query()->where(['translatable_type' => 'App\Models\Setting', 'language_id' => $theme_of_the_month_language->value])->first();
        } else {
            $theme_of_the_month_text = null;
        }

        return view('admin.eap-online.theme_of_the_month.view', ['languages' => $languages, 'theme_of_the_month_language' => $theme_of_the_month_language, 'theme_of_the_month_text' => $theme_of_the_month_text]);
    }

    public function theme_of_the_month_store(Request $request)
    {
        Validator::make($request->all(), [
            'theme_of_the_month_image' => 'max:3000',
        ])->validate();

        // theme of the month image
        if (! empty($request->file('theme_of_the_month_image'))) {
            if ($old_theme_of_the_month_image = EapSetting::query()->where('name', 'theme_of_the_month_image')->first()) {
                Storage::delete('eap-online/thumbnails/theme-of-the-month/'.$old_theme_of_the_month_image->value);
                EapSetting::query()->where('name', 'theme_of_the_month_image')->first()->delete();
            }

            $file = $request->file('theme_of_the_month_image');
            $name = time().'-'.$file->getFilename().'.'.$file->getClientOriginalExtension();
            EapSetting::query()->create([
                'name' => 'theme_of_the_month_image',
                'value' => $name,
            ]);

            $file->storeAs('eap-online/thumbnails/theme-of-the-month', $name);
        }

        // theme of the month language
        if (! empty($request->get('theme_of_the_month_language'))) {
            $language = EapSetting::query()->updateOrCreate([
                'name' => 'theme_of_the_month_language',
            ], [
                'value' => (int) $request->get('theme_of_the_month_language'),
            ]);
        } else {
            $language = null;
        }

        // theme of the month
        if ($request->get('theme_of_the_month_text') && $language) {
            foreach (EapTranslation::query()->where('translatable_type', 'App\Models\Setting')->get() as $translation) {
                $translation->delete();
            }

            EapTranslation::query()->create([
                'language_id' => $request->get('theme_of_the_month_language'),
                'translatable_id' => $language->id,
                'translatable_type' => 'App\Models\Setting',
                'value' => $request->get('theme_of_the_month_text'),
            ]);
        }

        return redirect(route('admin.eap-online.actions'));
    }

    public function connect_countries_to_languages_index()
    {
        $eap_languages = EapLanguage::with('countries')->get();
        $countries = Country::query()->orderBy('name')->get();

        return view('admin.eap-online.connect_countries_to_languages', ['eap_languages' => $eap_languages, 'countries' => $countries]);
    }

    public function connect_countries_to_languages(Request $request)
    {
        if ($request->has('countries')) {
            foreach ($request->input('countries') as $language_id => $countries) {
                $language = EapLanguage::query()->find($language_id);
                $language->countries()->sync($countries);
            }
        }

        return redirect()->back();
    }

    private function set_contact_information($contact_infos = null, bool $country = false, bool $new = false): void
    {
        if (! empty($contact_infos)) {
            foreach ($contact_infos as $contact_info) {
                if ($country) {
                    foreach ($contact_info as $data) {
                        EapContactInformation::query()->updateOrCreate([
                            'company_id' => $data['company_id'],
                            'country_id' => $data['country_id'],
                        ], [
                            'email' => $data['email'],
                            'phone' => $data['phone'],
                            'disabled_phone_card' => $data['phone_card'] === 'disabled',
                            'disabled_email_card' => $data['email_card'] === 'disabled',
                            'disabled_chat_card' => $data['chat_card'] === 'disabled',
                        ]);
                    }
                } elseif ($new) {
                    if ($contact_info['company_id'] == 'null') {
                        EapContactInformation::query()->updateOrCreate([
                            'country_id' => $contact_info['country_id'],
                            'company_id' => null,
                        ], [
                            'email' => $contact_info['email'],
                            'phone' => $contact_info['phone'],
                            'disabled_phone_card' => $contact_info['phone_card'] === 'disabled',
                            'disabled_email_card' => $contact_info['email_card'] === 'disabled',
                            'disabled_chat_card' => $contact_info['chat_card'] === 'disabled',
                        ]);
                    } else {
                        EapContactInformation::query()->updateOrCreate([
                            'company_id' => $contact_info['company_id'],
                            'country_id' => $contact_info['country_id'],
                        ], [
                            'email' => $contact_info['email'],
                            'phone' => $contact_info['phone'],
                            'disabled_phone_card' => $contact_info['phone_card'] === 'disabled',
                            'disabled_email_card' => $contact_info['email_card'] === 'disabled',
                            'disabled_chat_card' => $contact_info['chat_card'] === 'disabled',
                        ]);
                    }
                } else {
                    EapContactInformation::query()->updateOrCreate([
                        'company_id' => null,
                        'country_id' => $contact_info['country_id'],
                    ], [
                        'email' => $contact_info['email'],
                        'phone' => $contact_info['phone'],
                        'disabled_phone_card' => $contact_info['phone_card'] === 'disabled',
                        'disabled_email_card' => $contact_info['email_card'] === 'disabled',
                        'disabled_chat_card' => $contact_info['chat_card'] === 'disabled',
                    ]);
                }
            }
        }
    }
}
