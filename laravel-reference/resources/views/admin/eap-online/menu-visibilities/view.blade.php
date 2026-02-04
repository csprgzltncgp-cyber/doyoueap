@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <style>
        .list-element button, .list-element a {
            margin-right: 10px;
            display: inline-block;
        }

        .list-element button.delete-button {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            border: 0px solid black;
            color: #007bff;
            outline: none;
        }

        .list-element {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        button[type="submit"] {
            outline: none !important;
            background: rgb(89, 198, 198);
            color: white !important;
            font-weight: bold;
            padding: 10px 15px;
            border: 0px solid black;
            display: inline-block;
            text-decoration: none !important;
        }

        #customer-satisfaction-not-possible .checkmark {
            background-color: rgb(89, 198, 198) !important;
        }
    </style>
@endsection

@section('extra_js')
    <script src="/assets/js/eap-online/translations.js?v={{time()}}" charset="utf-8"></script>
    <script>
        $('input[type="checkbox"]').on('change', function (e) {
            const company_id = this.getAttribute('name').split('-')[0];
            const menu_item_id = this.getAttribute('name').split('-')[1];
            const visible = this.checked;

            if ((!(menu_item_id == 9 || menu_item_id == 1 || menu_item_id == 2) && visible)) {
                $(`input[name="${company_id}-9"]`).prop('checked', false);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{route('admin.eap-online.menu-visibilities.store')}}',
                    data: {
                        company_id,
                        'menu_item_id': 9,
                        'visible': false
                    },
                })
            }

            if (menu_item_id == 9 && visible){
                $(`input[name="${company_id}-6"]`).prop('checked', false);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{route('admin.eap-online.menu-visibilities.store')}}',
                    data: {
                        company_id,
                        'menu_item_id': 6,
                        'visible': false
                    },
                })
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{route('admin.eap-online.menu-visibilities.store')}}',
                data: {
                    company_id,
                    menu_item_id,
                    visible
                },
                success: function (data) {
                    if (data.status == 'ok') {
                        Swal.fire(
                            '{{__('common.edit-successful')}}',
                            '',
                            'success'
                        );
                    }
                }
            })
        });

        function toggleCompanies(contractHolderId, element) {
            if ($(element).hasClass('active')) {
                $(element).removeClass('active');
                $('.list-element .caret-left').html(`<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                    </svg>`);
                $('.list-element').each(function () {
                    if ($(this).data('contract_holder') && $(this).data('contract_holder') == contractHolderId) {
                        $(this).addClass('d-none');
                    }
                });
            } else {
                $(element).addClass('active')
                $(element).find('button.caret-left').html(`<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                                    </svg>`);
                $('.list-element').each(function () {
                    if ($(this).data('contract_holder') && $(this).data('contract_holder') == contractHolderId) {
                        $(this).removeClass('d-none');
                    } else if (!$(this).hasClass('group')) {
                        $(this).addClass('d-none');
                    }
                });
            }
        }


    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="col-12 mb-3">
                {{ Breadcrumbs::render('eap-online.menu-visibilities') }}
                <h1>{{__('eap-online.menu-visibilities.menu')}}</h1>
            </div>
            <div class="col-12">
                @foreach($contract_holders as $contract_holder)
                    <div class="list-element col-12 group" onClick="toggleCompanies({{$contract_holder->id}}, this)">
                        {{$contract_holder->name}}
                        <div class="d-flex align-items-center">
                            <button class="caret-left float-right">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @foreach($contract_holder->companies as $company)
                        <div class="list-element col-12 d-none flex-row justify-content-between align-items-center"
                             data-contract_holder="{{$contract_holder->id}}"
                             onclick="toggleTranslationSection('company-{{$company->id}}-visibilities', this)"
                        >
                            <p>{{$company->name}}</p>

                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="d-none col-12 mt-3 pl-0" id="company-{{$company->id}}-visibilities">
                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.home_page')}}
                                <input type="checkbox" name="{{$company->id}}-8"
                                       @if(in_array(8, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.intake')}}
                                <input type="checkbox" name="{{$company->id}}-16"
                                       @if(in_array(16, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.mail')}}
                                <input type="checkbox" name="{{$company->id}}-1"
                                       @if(in_array(1, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.operator')}}
                                <input type="checkbox" name="{{$company->id}}-2"
                                       @if(in_array(2, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.self_help')}}
                                <input type="checkbox" name="{{$company->id}}-3"
                                       @if(in_array(3, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.burnout')}}
                                <input type="checkbox" name="{{$company->id}}-12"
                                       @if(in_array(12, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                    id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.domestic_violence')}}
                                <input type="checkbox" name="{{$company->id}}-15"
                                        @if(in_array(15, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.assessment')}}
                                <input type="checkbox" name="{{$company->id}}-4"
                                       @if(in_array(4, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.theme_of_the_month')}}
                                <input type="checkbox" name="{{$company->id}}-5"
                                       @if(in_array(5, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.all_articles')}}
                                <input type="checkbox" name="{{$company->id}}-6"
                                       @if(in_array(6, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.old_articles')}}
                                <input type="checkbox" name="{{$company->id}}-9"
                                       @if(in_array(9, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.all_videos')}}
                                <input type="checkbox" name="{{$company->id}}-7"
                                       @if(in_array(7, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                    id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.all_webinars')}}
                                <input type="checkbox" name="{{$company->id}}-13"
                                        @if(in_array(13, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.all_podcasts')}}
                                <input type="checkbox" name="{{$company->id}}-10"
                                       @if(in_array(10, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.onsite_consultation')}}
                                <input type="checkbox" name="{{$company->id}}-14"
                                       @if(in_array(14, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.minka')}}
                                <input type="checkbox" name="{{$company->id}}-17"
                                       @if(in_array(17, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>

                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.search')}}
                                <input type="checkbox" name="{{$company->id}}-18"
                                       @if(in_array(18, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>
                            <label class="container checkbox-container mb-3"
                                   id="customer-satisfaction-not-possible">{{__('eap-online.menu-visibilities.live_webinar')}}
                                <input type="checkbox" name="{{$company->id}}-19"
                                       @if(in_array(19, $company->getEapMenuVisibilities()))checked="checked" @endif>
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    @endforeach
                @endforeach
            </div>
            <div class="col-4 col-lg-2 back-button mb-5">
                <a href="{{ route('admin.eap-online.actions') }}">{{__('common.back-to-list')}}</a>
            </div>
        </div>
    </div>
@endsection
