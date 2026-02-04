@extends('layout.master')

@section('title')
Admin Dashboard
@endsection

@section('extra_css')
<link rel="stylesheet" href="{{asset('assets/css/cases/view.css')}}?t={{time()}}">
<link rel="stylesheet" href="{{asset('assets/css/eap-online/translations.css')}}?t={{time()}}">

@if(Auth::user()->type == 'production_translating_admin')
<style>
    html {
        user-select: none;
        /* supported by Chrome and Opera */
        -webkit-user-select: none;
        /* Safari */
        -khtml-user-select: none;
        /* Konqueror HTML */
        -moz-user-select: none;
        /* Firefox */
        -ms-user-select: none;
        /* Internet Explorer/Edge */
    }
</style>
@endif
@endsection

@section('extra_js')
<script src="/assets/js/eap-online/translations.js?v={{time()}}" charset="utf-8"></script>
<script>
    let iterator = 0;

        $(async function () {
            await getContent();

            $('#search').on('input', async function (e) {
                await getContent(e.target.value)
            });
        });

        function resetSearch() {
            $("#search").val("");
            getContent();
        }

        async function getContent(needle = '') {
            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: '/ajax/get-translations-lines',
                data: {
                    needle
                },
                success: function (data) {
                    $('#holder').html(createHTML(data));
                },
            });
        }

        function createRowHTML(line, index, data) {
            let html = '';
            html += `
                <form method="post" class="col-12 input">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                             onclick="toggleTranslationSection('system-${line.id}-translations', this)"
                        >
                            <p class="m-0 mr-3">${(line.text['{{app()->getLocale()}}]'] ? line.text['{{app()->getLocale()}}]'] : line.text.en)}</p>
                            <div class="d-flex align-items-center"><div class="d-flex flex-wrap">`;

            data.languages.forEach((language) => {
                if (language.code in line.text) {
                    html += `
                                         <div style="background-color:rgb(145,183,82);" class="px-2 text-white mr-3 mb-2">
                                            ${language.code}
                                        </div>`;
                } else {
                    html += `
                                         <div style="background-color:rgb(219, 11, 32);" class="px-2 text-white mr-3 mb-2">
                                            ${language.code}
                                        </div>`;
                }
            });
            html += `</div><svg xmlns="http://www.w3.org/2000/svg" style="min-width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
</svg>
                            </div>
                            <input type="hidden" name="old[${index}][key]" required value="${line.key}"
                                   readonly>
                        </div>
                    </div>
                    <div class="d-none" id="system-${line.id}-translations">`;

            data.languages.forEach((language) => {
                html += `
                              <div class="row translation">
                                <div class="col-1 text-center" style="padding-top:15px;">
                                    ${language.code}
                                    </div>
                                    <div class="col-8 pl-0">
                                                    <textarea name="old[${index}][text][${language.code}]"
                                                  @if(Auth::user()->type == 'production_translating_admin')
                ${['ua', 'nl', 'pt', 'gr', 'se', 'fi', 'dk', 'al', 'sk'].includes(language.code) ? '' : 'readonly'}
                                                    @endif
                placeholder="{{__('eap-online.system.translation')}}">${(language.code in line.text) ? line.text[language.code] : ''}</textarea>
                                </div>
                              </div>`
            });
            html += `<div class="col-12 mt- mb-4">
                        <button type="submit" class="button btn-radius d-flex align-items-center">
                            <img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            {{__('common.save')}}</button>
                        </div>
                    </div>
                </form>`;

            return html;
        }

        function createCategoryRowHTML(type, name) {
            return `
            <div class="row w-100">
            <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line d-flex" onClick="toggleTranslationSection('${type}', this)">
                    <div class=" d-flex justify-content-between align-items-center col-12">
                        <p class="m-0">${name}</p>
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
</svg>
                    </div>
                </div>

            <div class="col-12 input d-none category-row-lead" id="${type}">
            `;
        }

        function createHTML(data) {
            let html = '';
            let categories = {};

            data.translation_lines.forEach((line, index) => {
                switch (true) {
                    case (line.key.split('_').shift().includes('lead')):
                        if (!('lead' in categories)) {
                            categories['lead'] = createCategoryRowHTML('lead', '{{__('eap-online.system_translation_categories.lead')}}');
                            categories['lead'] += createRowHTML(line, index, data);
                        } else {
                            categories['lead'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.split('_').shift().includes('common')):
                        if (!('common' in categories)) {
                            categories['common'] = createCategoryRowHTML('common', '{{__('eap-online.system_translation_categories.common')}}');
                            categories['common'] += createRowHTML(line, index, data);
                        } else {
                            categories['common'] += createRowHTML(line, index, data);
                        }
                        break;
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.split('_').shift().includes('menu')):
                        if (!('menu' in categories)) {
                            categories['menu'] = createCategoryRowHTML('menu_translations', '{{__('eap-online.system_translation_categories.menu')}}');
                            categories['menu'] += createRowHTML(line, index, data);
                        } else {
                            categories['menu'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                    case (line.key.split('_').shift().includes('validation')):
                        if (!('validation' in categories)) {
                            categories['validation'] = createCategoryRowHTML('validation', '{{__('eap-online.system_translation_categories.validation')}}');
                            categories['validation'] += createRowHTML(line, index, data);
                        } else {
                            categories['validation'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.split('_').shift().includes('footer')):
                        if (!('footer' in categories)) {
                            categories['footer'] = createCategoryRowHTML('footer', '{{__('eap-online.system_translation_categories.footer')}}');
                            categories['footer'] += createRowHTML(line, index, data);
                        } else {
                            categories['footer'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.split('_').shift().includes('register')):
                        if (!('register' in categories)) {
                            categories['register'] = createCategoryRowHTML('register', '{{__('eap-online.system_translation_categories.register')}}');
                            categories['register'] += createRowHTML(line, index, data);
                        } else {
                            categories['register'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.split('_').shift().includes('prizegame')):
                        if (!('prizegame' in categories)) {
                            categories['prizegame'] = createCategoryRowHTML('prizegame', '{{__('eap-online.system_translation_categories.prizegame')}}');
                            categories['prizegame'] += createRowHTML(line, index, data);
                        } else {
                            categories['prizegame'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.split('_').shift().includes('burnout')):
                        if (!('burnout' in categories)) {
                            categories['burnout'] = createCategoryRowHTML('burnout', '{{__('eap-online.system_translation_categories.burnout')}}');
                            categories['burnout'] += createRowHTML(line, index, data);
                        } else {
                            categories['burnout'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('domestic_violence') && !line.key.includes('menu')):
                        if (!('domestic_violence' in categories)) {
                            categories['domestic_violence'] = createCategoryRowHTML('domestic_violence', '{{__('eap-online.system_translation_categories.domestic_violence')}}');
                            categories['domestic_violence'] += createRowHTML(line, index, data);
                        } else {
                            categories['domestic_violence'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.split('_').shift().includes('language')):
                        if (!('language' in categories)) {
                            categories['language'] = createCategoryRowHTML('language', '{{__('eap-online.system_translation_categories.language')}}');
                            categories['language'] += createRowHTML(line, index, data);
                        } else {
                            categories['language'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.split('_').shift().includes('case')):
                        if (!('case' in categories)) {
                            categories['case'] = createCategoryRowHTML('case', '{{__('eap-online.system_translation_categories.case')}}');
                            categories['case'] += createRowHTML(line, index, data);
                        } else {
                            categories['case'] += createRowHTML(line, index, data);
                        }
                        break;
                    case ((line.key.split('_').shift().includes('intake')
                        && !line.key.includes('intake_video_consultation_appointment_confirmation_mail')
                        && !line.key.includes('intake_chat_consultation_appointment_confirmation_mail')
                        && !line.key.includes('intake_appointment_confirmation_mail')
                        && !line.key.includes('intake_expert_new') )
                        || (line.key.includes('video_therapy') && !line.key.includes('company_case_input')) // Remove company input name translations
                        || line.key.includes('online_therapy')
                        || (line.key.includes('consultation_') && !line.key.includes('intake_video_consultation_appointment_confirmation_mail') && !line.key.includes('onsite')
                        && !line.key.includes('intake_chat_consultation_appointment_confirmation_mail'))
                        || line.key.includes('chat_therapy')
                        || line.key.includes('wos_')):
                        if (!('intake' in categories)) {
                            categories['intake'] = createCategoryRowHTML('intake', '{{__('eap-online.system_translation_categories.intake')}}');
                            categories['intake'] += createRowHTML(line, index, data);
                        } else {
                            categories['intake'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('new_client_online')) :
                        if (!('new_client_online' in categories)) {
                            categories['new_client_online'] = createCategoryRowHTML('new_client_online', 'Email - Kliens email - Beosztással - Időpont létrehozása szakértő által');
                            categories['new_client_online'] += createRowHTML(line, index, data);
                        } else {
                            categories['new_client_online'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('delete_client_online') && !line.key.includes('delete_client_online_case')) :
                        if (!('delete_client_online' in categories)) {
                            categories['delete_client_online'] = createCategoryRowHTML('delete_client_online', 'Email - Kliens email - Beosztással - Időpont törlése szakértő által');
                            categories['delete_client_online'] += createRowHTML(line, index, data);
                        } else {
                            categories['delete_client_online'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('delete_client_online_case')) :
                        if (!('delete_client_online_case' in categories)) {
                            categories['delete_client_online_case'] = createCategoryRowHTML('delete_client_online_case', 'Email - Kliens email - Beosztással - Tanácsadás törlése szakértő által');
                            categories['delete_client_online_case'] += createRowHTML(line, index, data);
                        } else {
                            categories['delete_client_online_case'] += createRowHTML(line, index, data);
                        }
                        break;delete_client_online_case
                    case (line.key.includes('new_client_intake')) :
                        if (!('new_client_intake' in categories)) {
                            categories['new_client_intake'] = createCategoryRowHTML('new_client_intake', 'Email - Kliens email - Nincs beosztással - Időpont létrehozása szakértő által');
                            categories['new_client_intake'] += createRowHTML(line, index, data);
                        } else {
                            categories['new_client_intake'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('edit_client_intake')) :
                        if (!('edit_client_intake' in categories)) {
                            categories['edit_client_intake'] = createCategoryRowHTML('edit_client_intake', 'Email - Kliens email - Nincs beosztással - Időpont módsítása szakértő által');
                            categories['edit_client_intake'] += createRowHTML(line, index, data);
                        } else {
                            categories['edit_client_intake'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('delete_client_intake')) :
                        if (!('delete_client_intake' in categories)) {
                            categories['delete_client_intake'] = createCategoryRowHTML('delete_client_intake', 'Email - Kliens email - Nincs beosztással - Időpont törlés szakértő által');
                            categories['delete_client_intake'] += createRowHTML(line, index, data);
                        } else {
                            categories['delete_client_intake'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('new_expert_online_appointment_mail_video')) :
                        if (!('new_expert_online_appointment_mail_video' in categories)) {
                            categories['new_expert_online_appointment_mail_video'] = createCategoryRowHTML('new_expert_online_appointment_mail_video', 'Email - Szakértő email - Beosztással - Jelentkezés videó tanácsadásra kliens által');
                            categories['new_expert_online_appointment_mail_video'] += createRowHTML(line, index, data);
                        } else {
                            categories['new_expert_online_appointment_mail_video'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('new_expert_online_appointment_mail_chat')) :
                        if (!('new_expert_online_appointment_mail_chat' in categories)) {
                            categories['new_expert_online_appointment_mail_chat'] = createCategoryRowHTML('new_expert_online_appointment_mail_chat', 'Email - Szakértő email - Beosztással - Jelentkezés chat tanácsadásra kliens által');
                            categories['new_expert_online_appointment_mail_chat'] += createRowHTML(line, index, data);
                        } else {
                            categories['new_expert_online_appointment_mail_chat'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('edit_expert_online_appointment_mail_video')) :
                        if (!('edit_expert_online_appointment_mail_video' in categories)) {
                            categories['edit_expert_online_appointment_mail_video'] = createCategoryRowHTML('edit_expert_online_appointment_mail_video', 'Email - Szakértő email - Beosztással - Video tanácsadási időpont módosítása kliens által');
                            categories['edit_expert_online_appointment_mail_video'] += createRowHTML(line, index, data);
                        } else {
                            categories['edit_expert_online_appointment_mail_video'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('edit_expert_online_appointment_mail_chat')) :
                        if (!('edit_expert_online_appointment_mail_chat' in categories)) {
                            categories['edit_expert_online_appointment_mail_chat'] = createCategoryRowHTML('edit_expert_online_appointment_mail_chat', 'Email - Szakértő email - Beosztással - Chat tanácsadási időpont módosítása kliens által');
                            categories['edit_expert_online_appointment_mail_chat'] += createRowHTML(line, index, data);
                        } else {
                            categories['edit_expert_online_appointment_mail_chat'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('delete_expert_online_appointment_mail_video') && !line.key.includes('delete_expert_online_appointment_case')) :
                        if (!('delete_expert_online_appointment_mail_video' in categories)) {
                            categories['delete_expert_online_appointment_mail_video'] = createCategoryRowHTML('delete_expert_online_appointment_mail_video', 'Email - Szakértő email - Beosztással - Video tanácsadási időpont törlése kliens által');
                            categories['delete_expert_online_appointment_mail_video'] += createRowHTML(line, index, data);
                        } else {
                            categories['delete_expert_online_appointment_mail_video'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('delete_expert_online_appointment_mail_chat') && !line.key.includes('delete_expert_online_appointment_case')) :
                        if (!('delete_expert_online_appointment_mail_chat' in categories)) {
                            categories['delete_expert_online_appointment_mail_chat'] = createCategoryRowHTML('delete_expert_online_appointment_mail_chat', 'Email - Szakértő email - Beosztással - Chat tanácsadási időpont törlése kliens által');
                            categories['delete_expert_online_appointment_mail_chat'] += createRowHTML(line, index, data);
                        } else {
                            categories['delete_expert_online_appointment_mail_chat'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('delete_expert_online_appointment_case_mail_video')) :
                        if (!('delete_expert_online_appointment_case_mail_video' in categories)) {
                            categories['delete_expert_online_appointment_case_mail_video'] = createCategoryRowHTML('delete_expert_online_appointment_case_mail_video', 'Email - Szakértő email - Beosztással - Video tanácsadási időpont lemodása kliens által');
                            categories['delete_expert_online_appointment_case_mail_video'] += createRowHTML(line, index, data);
                        } else {
                            categories['delete_expert_online_appointment_case_mail_video'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('delete_expert_online_appointment_case_mail_chat')) :
                        if (!('delete_expert_online_appointment_case_mail_chat' in categories)) {
                            categories['delete_expert_online_appointment_case_mail_chat'] = createCategoryRowHTML('delete_expert_online_appointment_case_mail_chat', 'Email - Szakértő email - Beosztással - Chat tanácsadási időpont lemodása kliens által');
                            categories['delete_expert_online_appointment_case_mail_chat'] += createRowHTML(line, index, data);
                        } else {
                            categories['delete_expert_online_appointment_case_mail_chat'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('online_appointment_confirmation_mail_video')) :
                        if (!('online_appointment_confirmation_mail_video' in categories)) {
                            categories['online_appointment_confirmation_mail_video'] = createCategoryRowHTML('online_appointment_confirmation_mail_video', 'Email - Kliens email - Beosztással - Időpont foglalása video tanácsadásra kliens által');
                            categories['online_appointment_confirmation_mail_video'] += createRowHTML(line, index, data);
                        } else {
                            categories['online_appointment_confirmation_mail_video'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('online_appointment_confirmation_mail_chat')) :
                        if (!('online_appointment_confirmation_mail_chat' in categories)) {
                            categories['online_appointment_confirmation_mail_chat'] = createCategoryRowHTML('online_appointment_confirmation_mail_chat', 'Email - Kliens email - Beosztással - Időpont foglalása chat tanácsadásra kliens által');
                            categories['online_appointment_confirmation_mail_chat'] += createRowHTML(line, index, data);
                        } else {
                            categories['online_appointment_confirmation_mail_chat'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('intake_appointment_confirmation_mail')) :
                        if (!('intake_appointment_confirmation_mail' in categories)) {
                            categories['intake_appointment_confirmation_mail'] = createCategoryRowHTML('intake_appointment_confirmation_mail', 'Email - Kliens email - Nincs beosztással - Időpont foglalása személyes vagy telefonos tanácsadásra kliens által');
                            categories['intake_appointment_confirmation_mail'] += createRowHTML(line, index, data);
                        } else {
                            categories['intake_appointment_confirmation_mail'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('intake_video_consultation_appointment_confirmation_mail')) :
                        if (!('intake_video_consultation_appointment_confirmation_mail' in categories)) {
                            categories['intake_video_consultation_appointment_confirmation_mail'] = createCategoryRowHTML('intake_video_consultation_appointment_confirmation_mail', 'Email - Kliens email - Nincs beosztással - Időpont foglalása video tanácsadásra kliens által');
                            categories['intake_video_consultation_appointment_confirmation_mail'] += createRowHTML(line, index, data);
                        } else {
                            categories['intake_video_consultation_appointment_confirmation_mail'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('intake_chat_consultation_appointment_confirmation_mail')) :
                        if (!('intake_chat_consultation_appointment_confirmation_mail' in categories)) {
                            categories['intake_chat_consultation_appointment_confirmation_mail'] = createCategoryRowHTML('intake_chat_consultation_appointment_confirmation_mail', 'Email - Kliens email - Nincs beosztással - Időpont foglalása chat tanácsadásra kliens által');
                            categories['intake_chat_consultation_appointment_confirmation_mail'] += createRowHTML(line, index, data);
                        } else {
                            categories['intake_chat_consultation_appointment_confirmation_mail'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('password_request_mail')) :
                        if (!('password_request_mail' in categories)) {
                            categories['password_request_mail'] = createCategoryRowHTML('password_request_mail', 'Email - Új jelszó/felhasználónév beállítása');
                            categories['password_request_mail'] += createRowHTML(line, index, data);
                        } else {
                            categories['password_request_mail'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('user_register_mail')) :
                        if (!('user_register_mail' in categories)) {
                            categories['user_register_mail'] = createCategoryRowHTML('user_register_mail', 'Email - Új regisztráció befejezése!');
                            categories['user_register_mail'] += createRowHTML(line, index, data);
                        } else {
                            categories['user_register_mail'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('intake_expert_new')) :
                        if (!('intake_expert_new' in categories)) {
                            categories['intake_expert_new'] = createCategoryRowHTML('intake_expert_new', 'Email - Szakértő email - Nincs beosztással - Tanácsadásra jelentkezés kliens által');
                            categories['intake_expert_new'] += createRowHTML(line, index, data);
                        } else {
                            categories['intake_expert_new'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('onsite_consultation')) :
                        if (!('onsite_consultation' in categories)) {
                            categories['onsite_consultation'] = createCategoryRowHTML('onsite_consultation', '{{__('eap-online.system_translation_categories.onsite_consultation')}}');
                            categories['onsite_consultation'] += createRowHTML(line, index, data);
                        } else {
                            categories['onsite_consultation'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('create_onsite_appointment')) :
                        if (!('create_onsite_appointment' in categories)) {
                            categories['create_onsite_appointment'] = createCategoryRowHTML('create_onsite_appointment', '{{__('eap-online.system_translation_categories.email_onsite_consultation_creation')}}');
                            categories['create_onsite_appointment'] += createRowHTML(line, index, data);
                        } else {
                            categories['create_onsite_appointment'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('edit_onsite_appointment')) :
                        if (!('edit_onsite_appointment' in categories)) {
                            categories['edit_onsite_appointment'] = createCategoryRowHTML('edit_onsite_appointment', '{{__('eap-online.system_translation_categories.email_onsite_consultation_edit')}}');
                            categories['edit_onsite_appointment'] += createRowHTML(line, index, data);
                        } else {
                            categories['edit_onsite_appointment'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('delete_onsite_appointment')) :
                        if (!('delete_onsite_appointment' in categories)) {
                            categories['delete_onsite_appointment'] = createCategoryRowHTML('delete_onsite_appointment', '{{__('eap-online.system_translation_categories.email_onsite_consultation_delete')}}');
                            categories['delete_onsite_appointment'] += createRowHTML(line, index, data);
                        } else {
                            categories['delete_onsite_appointment'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('reminder_onsite_appointment')) :
                        if (!('reminder_onsite_appointment' in categories)) {
                            categories['reminder_onsite_appointment'] = createCategoryRowHTML('reminder_onsite_appointment', '{{__('eap-online.system_translation_categories.email_onsite_consultation_reminder')}}');
                            categories['reminder_onsite_appointment'] += createRowHTML(line, index, data);
                        } else {
                            categories['reminder_onsite_appointment'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.split('_').shift().includes('specialization')):
                        if (!('specialization' in categories)) {
                            categories['specialization'] = createCategoryRowHTML('specialization', '{{__('eap-online.system_translation_categories.specialization')}}');
                            categories['specialization'] += createRowHTML(line, index, data);
                        } else {
                            categories['specialization'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.split('_').shift().includes('permission')):
                        if (!('permission' in categories)) {
                            categories['permission'] = createCategoryRowHTML('permission', '{{__('eap-online.system_translation_categories.permission')}}');
                            categories['permission'] += createRowHTML(line, index, data);
                        } else {
                            categories['permission'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.includes('password_reset')):
                        if (!('password_reset' in categories)) {
                            categories['password_reset'] = createCategoryRowHTML('password_reset', '{{__('eap-online.system_translation_categories.password_reset')}}');
                            categories['password_reset'] += createRowHTML(line, index, data);
                        } else {
                            categories['password_reset'] += createRowHTML(line, index, data);
                        }
                        break;
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.includes('moodm_meter')):
                        if (!('moodm_meter' in categories)) {
                            categories['moodm_meter'] = createCategoryRowHTML('mood_meter', '{{__('eap-online.system_translation_categories.mood_meter')}}');
                            categories['moodm_meter'] += createRowHTML(line, index, data);
                        } else {
                            categories['moodm_meter'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                    case (line.key.split('_').shift().includes('mail')):
                        if (!('mail' in categories)) {
                            categories['mail'] = createCategoryRowHTML('mail', '{{__('eap-online.system_translation_categories.mail')}}');
                            categories['mail'] += createRowHTML(line, index, data);
                        } else {
                            categories['mail'] += createRowHTML(line, index, data);
                        }
                        break;
                    case (line.key.split('_').shift().includes('profile')):
                        if (!('profile' in categories)) {
                            categories['profile'] = createCategoryRowHTML('profile', '{{__('eap-online.system_translation_categories.profile')}}');
                            categories['profile'] += createRowHTML(line, index, data);
                        } else {
                            categories['profile'] += createRowHTML(line, index, data);
                        }
                        break;
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.split('_').shift().includes('videos')):
                        if (!('videos' in categories)) {
                            categories['videos'] = createCategoryRowHTML('videos', '{{__('eap-online.system_translation_categories.videos')}}');
                            categories['videos'] += createRowHTML(line, index, data);
                        } else {
                            categories['videos'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.split('_').shift().includes('podcasts')):
                        if (!('podcasts' in categories)) {
                            categories['podcasts'] = createCategoryRowHTML('podcasts', '{{__('eap-online.system_translation_categories.podcasts')}}');
                            categories['podcasts'] += createRowHTML(line, index, data);
                        } else {
                            categories['podcasts'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.includes('live_webinar')):
                        if (!('live_webinar' in categories)) {
                            categories['live_webinar'] = createCategoryRowHTML('live_webinar', '{{__('eap-online.system_translation_categories.live_webinar')}}');
                            categories['live_webinar'] += createRowHTML(line, index, data);
                        } else {
                            categories['live_webinar'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.split('_').shift().includes('articles')):
                        if (!('articles' in categories)) {
                            categories['articles'] = createCategoryRowHTML('articles', '{{__('eap-online.system_translation_categories.articles')}}');
                            categories['articles'] += createRowHTML(line, index, data);
                        } else {
                            categories['articles'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.split('_').shift().includes('quizzes')):
                        if (!('quizzes' in categories)) {
                            categories['quizzes'] = createCategoryRowHTML('quizzes', '{{__('eap-online.system_translation_categories.quizzes')}}');
                            categories['quizzes'] += createRowHTML(line, index, data);
                        } else {
                            categories['quizzes'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.includes('self_help')):
                        if (!('self_help' in categories)) {
                            categories['self_help'] = createCategoryRowHTML('self_help', '{{__('eap-online.system_translation_categories.self_help')}}');
                            categories['self_help'] += createRowHTML(line, index, data);
                        } else {
                            categories['self_help'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.includes('assessmentm_results')):
                        if (!('assessmentm_results' in categories)) {
                            categories['assessmentm_results'] = createCategoryRowHTML('assessmentm_results', '{{__('eap-online.system_translation_categories.assessmentm_results')}}');
                            categories['assessmentm_results'] += createRowHTML(line, index, data);
                        } else {
                            categories['assessmentm_results'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.includes('well_beingm_results')):
                        if (!('well_beingm_results' in categories)) {
                            categories['well_beingm_results'] = createCategoryRowHTML('well_beingm_results', '{{__('eap-online.system_translation_categories.well_beingm_results')}}');
                            categories['well_beingm_results'] += createRowHTML(line, index, data);
                        } else {
                            categories['well_beingm_results'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.split('_').shift().includes('assessment')):
                        if (!('assessment' in categories)) {
                            categories['assessment'] = createCategoryRowHTML('assessment', '{{__('eap-online.system_translation_categories.assessment')}}');
                            categories['assessment'] += createRowHTML(line, index, data);
                        } else {
                            categories['assessment'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                    case (line.key.split('_').shift().includes('contact')):
                        if (!('contact' in categories)) {
                            categories['contact'] = createCategoryRowHTML('contact', '{{__('eap-online.system_translation_categories.contact')}}');
                            categories['contact'] += createRowHTML(line, index, data);
                        } else {
                            categories['contact'] += createRowHTML(line, index, data);
                        }
                        break;
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.split('_').shift().includes('home')):
                        if (!('home' in categories)) {
                            categories['home'] = createCategoryRowHTML('home', '{{__('eap-online.system_translation_categories.home')}}');
                            categories['home'] += createRowHTML(line, index, data);
                        } else {
                            categories['home'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                    @if(Auth::user()->type != 'production_translating_admin')
                    case (line.key.split('_').shift().includes('minka')):
                        if (!('minka' in categories)) {
                            categories['minka'] = createCategoryRowHTML('minka', 'Minka');
                            categories['minka'] += createRowHTML(line, index, data);
                        } else {
                            categories['minka'] += createRowHTML(line, index, data);
                        }
                        break;
                    @endif
                }
            });

            categories = Object.fromEntries(Object.entries(categories).sort());

            Object.values(categories).forEach((category) => {
                category += '</div></div>';
                html += category;
            })


            return html;
        }

        function addInput() {
            let html = '<form method="post" class="row w-100">{{csrf_field()}}\
            <div class="col-12 input mb-3">\
                      <div class="row">\
                        <div class="col-4 pl-0">\
                          <input name="new[' + iterator + '][key]"  required value="" placeholder="{{__('eap-online.system.input_name')}}">\
                        </div>\
                      </div>';

            @foreach($languages as $language)
                html += '<div class="row translation">\
                <div class="col-1 text-center" style="padding-top:15px;">\
                    {{$language->code}}\
                </div>\
                <div class="col-8 pl-0">\
                  <textarea name="new[' + iterator + '][text][{{$language->code}}]" placeholder="{{__('eap-online.system.translation')}}"></textarea>\
                </div>\
              </div>';
            @endforeach
                html += '<div class="col-12 mt- mb-4">\
                <button type="submit" class="button btn-radius d-flex align-items-center"><img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">{{__('common.save')}}</button>\
        </div></div></form>';
            iterator++;

            $('#holder').prepend(html);
        }
</script>
@endsection

@section('content')
<div class="row ml-0">
    <div class="col-12 p-0">
        {{Breadcrumbs::render('eap-online.translate-system')}}
        <h1>{{__('eap-online.system.title')}}</h1>
    </div>
    <div class="col-6 mb-5 p-0">
        <div class="mb-3">
            <a onClick="addInput()" href="javascript:;">{{__('eap-online.system.new')}}</a>
        </div>
        <div class="w-100 d-flex flex-row align-items-center col-12 pl-0 ml-0">
            <input class="mb-0 btn-input-field-height" style="height: 64px" type="text" id="search"
                placeholder="{{__('eap-online.system.search')}}">
            <button class="ml-3 flex-grow-1 btn-radius"
                style="--btn-height: auto; --btn-margin-left: var(--btn-margin-x); --btn-margin-bottom:0px;"
                onClick="resetSearch()">
                <div class="d-flex flex-row justify-content-center justify-content-center align-items-center">
                    <img src="{{asset('assets/img/reset.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                    {{__('eap-online.system.reset')}}
                </div>
            </button>
        </div>
    </div>

    <div class="col-12" id="holder">

    </div>
    <div class="row col-4 col-lg-2 back-button mb-5">
        <a href="{{ route('admin.eap-online.actions') }}">{{__('common.back-to-list')}}</a>
    </div>
</div>
</div>
@endsection
