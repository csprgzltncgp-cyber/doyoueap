@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">

    <style>
        .category-button {
            background: rgb(89, 198, 198);
            padding: 20px 20px !important;
            color: white !important;
            margin-right: 20px;
        }

        #customer-satisfaction-not-possible .checkmark {
            background-color: rgb(89, 198, 198);
        }
    </style>
@endsection

@section('extra_js')
    <script>
        let index = 0;

        function newContactInfoSection() {
            const html = `
                 <div class="row col-12 d-flex align-items-center" style="margin-top: 35px">
    <div class="d-flex flex-column col-12 p-0 row pb-10">
        <div class="col-12 d-flex">
            <input name="new[${index}][company_id]" class="mr-3" type="hidden"
                   readonly>
            <div class="form-group w-25 mr-3">
                <label>{{__('workshop.company_name')}}</label>
                <select id="company_selector_${index}" index="${index}" class="mb-0 btn-input-field-height" name="new[${index}][company_id]">
                <option value="null" selected>{{__('eap-online.contact_information.no_company')}}</option>
                    @foreach($contract_holders as $contract_holder)
            <optgroup label="{{$contract_holder->name}}">
                                        @foreach($contract_holder->companies as $company)
            <option value="{{$company->id}}">{{$company->name}}</option>
                                        @endforeach
            </optgroup>
@endforeach
            </select>
        </div>
        <input name="new[${index}][country_id]" class="mr-3" type="hidden"
                   readonly>
            <div class="form-group w-25 mr-3">
                <label>{{__('eap-online.languages.country')}}</label>
                <select id="country_selector_${index}" class="mb-0 btn-input-field-height" name="new[${index}][country_id]">
                    @foreach(\App\Models\Country::all() as $country)
            <option value="{{$country->id}}">{{$country->code}}</option>
                    @endforeach
            </select>
        </div>
        <div class="form-group w-25 mr-3">
            <label>{{__('common.email')}}</label>
                <input name="new[${index}][email]" class="mr-3 mb-0 btn-input-field-height" type="email"
                       placeholder="{{__('common.email')}}"
                >
            </div>
            <div class="form-group w-25 mr-3">
                <label>{{__('common.phone')}}</label>
                <input name="new[${index}][phone]" class="mr-3 mb-0 btn-input-field-height" type="text"
                       placeholder="{{__('common.phone')}}"

                >
            </div>
            <div class="form-group w-25 justify-content-end">
            </div>
        </div>
        <label class="pl-3">{{__('eap-online.articles.appearance')}}</label>
        <div class="col-12 d-flex">
            <div class="form-group w-25 mr-3">
                <label class="container checkbox-container"
                       id="customer-satisfaction-not-possible">{{__('eap-online.contact_information.phone_card')}}
            <input type="hidden" name="new[${index}][phone_card]" value="disabled">
                    <input type="checkbox" name="new[${index}][phone_card]"
>
                    <span class="checkmark"></span>
                </label>
            </div>
            <div class="form-group w-25 mr-3">
                <label class="container checkbox-container"
                       id="customer-satisfaction-not-possible">{{__('eap-online.contact_information.email_card')}}
            <input type="hidden" name="new[${index}][email_card]" value="disabled">
                    <input type="checkbox" name="new[${index}][email_card]"
                    >
                    <span class="checkmark"></span>
                </label>
            </div>
            <div class="form-group w-25 mr-3">
                <label class="container checkbox-container"
                       id="customer-satisfaction-not-possible">{{__('eap-online.contact_information.chat_card')}}
            <input type="hidden" name="new[${index}][chat_card]" value="disabled">
                    <input type="checkbox" name="new[${index}][chat_card]"
                    >
                    <span class="checkmark"></span>
                </label>
            </div>
        </div>
    </div>
</div>
            `;

            $('#contact_info_holder').append(html);

            $(`#company_selector_${index}`).change(function () {
                const company_id = $(this).val();
                const selectorIndex = $(this).attr('index');
                $.ajax({
                    type: 'GET',
                    url: '/ajax/get-countries/' + company_id,
                    success: function (data) {
                        let html = '';
                        data.forEach((country) => {
                            html += `
                            <option value="${country.id}">${country.code}</option>
                            `;
                        });
                        console.log(selectorIndex)
                        $(`#country_selector_${selectorIndex}`).html(html);
                    }
                });
            })

            index++;
        }
    </script>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        {{ Breadcrumbs::render('eap-online.contact-information') }}
        <h1 class="">{{__('eap-online.contact_information.menu')}}</h1>
        <form style="max-width: fit-content;" action="{{route('admin.eap-online.contact_information.store')}}" method="post">
            {{csrf_field()}}
            <div id="contact_info_holder">
                <div class="row col-12 d-flex align-items-center mt-5">
                    <div class="d-flex flex-column col-12 p-0 row pb-10">
                        <div class="col-12 d-flex">
                            <div class="form-group w-25 mr-3">
                                <label>{{__('common.email')}}</label>
                                <input name="default[email]" class="mr-3 mb-0 btn-input-field-height btn-input-field-height" type="email"
                                        placeholder="{{__('common.email')}}"
                                        value="{{$default->email}}"
                                >
                            </div>
                            <div class="form-group w-25 mr-3">
                                <label>{{__('common.phone')}}</label>
                                <input name="default[phone]" class="mr-3 mb-0 btn-input-field-height btn-input-field-height" type="text"
                                        placeholder="{{__('common.phone')}}"
                                        value="{{$default->phone}}"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($country_infos as $country_info)
                    @component('components.eap-online.contact_info_country_line_component',[
                        'country' => $country_info->country()->first(),
                        'contact_info' => $country_info
                    ])@endcomponent
                @endforeach
                @foreach($company_infos as $company_info)
                    @component('components.eap-online.contact_info_company_line_component',[
                        'country' => $company_info->country()->first(),
                        'company' => $company_info->company()->first(),
                        'contact_info' => $company_info
                    ])@endcomponent
                @endforeach
            </div>
            <div class="row col-12 d-flex flex-row" style="margin-top: 50px;">
                <div>
                    <button class="text-center btn-radius" type="button" onclick="newContactInfoSection()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                          </svg>
                        <span>
                            {{__('common.add')}}
                        </span>
                    </button>
                </div>
                <div>
                    <button class="text-center button btn-radius d-flex align-items-center" type="submit">
                        <img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                        {{__('common.save')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
