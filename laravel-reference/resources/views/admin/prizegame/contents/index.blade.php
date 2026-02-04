@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/master.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">

    <style>
        button.edit-workshop {
            border: 0;
            background: rgb(0, 87, 95);
            color: white;
            text-transform: uppercase;
        }

        button.edit-workshop:hover {
            text-decoration: underline;
        }
    </style>
@endsection

@section('extra_js')
    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script>
        $(function () {
            $('.datepicker').datepicker({
                'format': 'yyyy-mm-dd'
            });
        });

        @if(session()->has('duplicate_content_warning'))
            Swal.fire(
                '{{__('prizegame.duplicate_content_warning')}}!',
                '',
                'error'
            );
        @endif

        function openModal(id, content_id, company_id = null, country_id = null, language_id = null, type_id = null) {
            $(`#${id}`).modal("show");
            $('input[name="content-id"]').val(content_id);
            $('input[name="content-language"]').val(language_id);
            $('input[name="content-type"]').val(type_id);
            $('input[name="specific_company_id"]').val(company_id);
            $('input[name="specific_country_id"]').val(country_id);
        }

        async function isPrizegameCreatable(company_id, country_id) {
            try {
                return await $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: "{{route('admin.prizegame.games.is_creatable')}}",
                    data: {
                        company_id,
                        country_id,
                    }
                });
            } catch (e) {
                return false;
            }
        }

        async function hasContentLike(language_id, type_id, company_id, country_id) {
            try {
                return await $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: "{{route('admin.prizegame.pages.has_content_like')}}",
                    data: {
                        company_id,
                        country_id,
                        language_id,
                        type_id
                    }
                });
            } catch (e) {
                return false;
            }
        }

        async function saveAsNormalContent() {
            const content_id = $('input[name="content-id"]').val();
            const company_id = $('select[name="content-company"]').val();
            const country_id = $('select[name="content-country"]').val();
            const language_id = $('input[name="content-language"]').val();
            const type_id = $('input[name="content-type"]').val();
            const from_date = $('input[name="normal_from"]').val();
            const to_date = $('input[name="normal_to"]').val();
            const isCreatable = await isPrizegameCreatable(company_id, country_id);
            const contentExist = await hasContentLike(language_id, type_id, company_id, country_id);

            if (!from_date || !to_date) {
                Swal.fire({
                    title: 'Dátum megadása kötelező!',
                    icon: 'error'
                });
                return;
            }

            if (contentExist) {
                Swal.fire({
                    title: 'Ilyen tartalom már létezik!',
                    icon: 'error'
                });
                return;
            }

            if (!isCreatable) {
                Swal.fire({
                    title: 'Nyereményjáték létrehozása sikertelen!',
                    text: 'Van jelenleg futó nyereményjáték!',
                    icon: 'error'
                });
                return;
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{route('admin.prizegame.games.create_from_normal')}}",
                data: {
                    content_id,
                    company_id,
                    country_id,
                    from_date,
                    to_date
                },
                success: function (data) {
                    location.reload();
                },
                error: function (error) {
                    location.reload();
                }
            });
        }

        async function saveAsSpecificContent() {
            const content_id = $('input[name="content-id"]').val();
            const company_id = $('input[name="specific_company_id"]').val();
            const country_id = $('input[name="specific_country_id"]').val();
            const from_date = $('input[name="specific_from"]').val();
            const to_date = $('input[name="specific_to"]').val();
            const isCreatable = await isPrizegameCreatable(company_id, country_id);

            if (!from_date || !to_date) {
                Swal.fire({
                    title: 'Dátum megadása kötelező!',
                    icon: 'error'
                });
                return;
            }

            if (!isCreatable) {
                Swal.fire({
                    title: 'Nyereményjáték létrehozása sikertelen!',
                    text: 'Van jelenleg futó nyereményjáték!',
                    icon: 'error'
                });
                return;
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{route('admin.prizegame.games.create_from_specific')}}",
                data: {
                    content_id,
                    from_date,
                    to_date
                },
                success: function (data) {
                    location.reload();
                },
                error: function (error) {
                    location.reload();
                }
            });

        }

        function saveAsContent() {
            const content_id = $('input[name="content-id"]').val();
            const type_id = $('select[name="type-id"]').val();
            const language_id = $('select[name="language-id"]').val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{route('admin.prizegame.pages.save-as')}}",
                data: {
                    content_id,
                    type_id,
                    language_id
                },
                success: function (data) {
                    location.reload();
                },
                error: function (error) {
                    location.reload();
                }
            });
        }

        function deleteContent(id) {
            Swal.fire({
                title: 'Biztos, hogy törölni szeretné?',
                text: "A művelet nem visszavonható!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Igen, törlöm!',
                cancelButtonText: 'Mégsem',
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: "{{route('admin.prizegame.pages.delete')}}" + `/${id}`,
                        success: function (data) {
                            location.reload();
                        },
                        error: function (error) {
                            location.reload();
                        }
                    });
                }
            });
        }
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12  mb-5 d-flex flex-column align-items-start">
            <div class="w-100 flex flex-col align-items-center col-12 p-0">
                <div>
                    {{ Breadcrumbs::render('prizegame.pages.list') }}
                    <h1>{{__('prizegame.pages.menu')}}</h1>
                    <a href="{{route('admin.prizegame.pages.create')}}">{{__('prizegame.pages.new')}}</a>
                </div>
            </div>
        </div>
        <div class="col-12" id="articles_holder">
            @foreach($contents as $content)
                <div class="list-element col-12">
                    <span class="data mr-0">
                        {{$content->language->name}}
                        - {{$content->type->name}}
                        @if(!empty($content->country)) - {{$content->country->name}} @endif
                        @if(!empty($content->company)) - {{$content->company->name}} @endif
                    </span>
                    <a class="edit-workshop btn-radius" style="--btn-margin-left: var(--btn-margin-x)"
                       href="{{route('admin.prizegame.pages.edit', $content)}}">
                       <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                       {{__('common.select')}}
                    </a>
                    @if(empty($content->country) && empty($content->company))
                        <button class="edit-workshop btn-radius"
                                onclick="openModal('modal-save-as-normal-content', '{{$content->id}}', null,null, '{{$content->language->id}}', '{{$content->type_id}}')">
                            <img src="{{asset('assets/img/start.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span>{{__('prizegame.pages.save_as_template')}}</span>
                        </button>
                        <button class="edit-workshop btn-radius"
                                onclick="openModal('modal-save-as-content', '{{$content->id}}')">
                            <img src="{{asset('assets/img/save.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span>{{__('myeap.pages.save_as')}}</span>
                        </button>
                        <button class="edit-workshop btn-radius d-flex"
                                onclick="deleteContent({{$content->id}})">
                            <img src="{{asset('assets/img/delete.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                            <span class="">{{__('common.delete')}}</span>
                        </button>
                    @else
                        @if (!$content->hasContentLike())
                            <button id="save_as_assigned_{{$content->id}}" class="edit-workshop btn-radius" onload="alert(1);"
                                onclick="openModal('modal-save-as-specific-content', '{{$content->id}}', '{{$content->company->id}}', '{{$content->country->id}}')">
                                <img src="{{asset('assets/img/start.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
                                <span>{{__('prizegame.pages.save_as_assigned')}}</span>
                            </button>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    <div class="row col-4 col-lg-2 back-button mb-5">
        <a href="{{ route('admin.prizegame.actions') }}">{{__('common.back-to-list')}}</a>
    </div>
@endsection

@section('modal')
    <div class="modal" tabindex="-1" id="modal-save-as-normal-content" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if ($list == 'template')
                            {{__('prizegame.pages.save_as_template')}}
                        @else
                            {{__('prizegame.pages.save_as_title')}}
                        @endif
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select name="content-country" required>
                        @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                    <select name="content-company" required>
                        @foreach($companies as $company)
                            <option value="{{$company->id}}">{{$company->name}}</option>
                        @endforeach
                    </select>
                    <div class="d-flex w-100 justify-content-between">
                        <input type="text" name="normal_from" class="datepicker"
                               placeholder="{{ __('common.from') }}" style="width:40%;margin-right:4%;" required/>
                        <input type="text" name="normal_to" class="datepicker"
                               placeholder="{{ __('common.to') }}" style="width:40%;" required/>
                    </div>
                    <input type="hidden" name="content-id" value="">
                    <input type="hidden" name="content-language">
                    <input type="hidden" name="content-type">
                    <button class="button btn-radius float-right m-0 text-uppercase" style="--btn-margin-right: 0px;" onclick="saveAsNormalContent()">
                        <img class="mr-1" style="width:20px;" src="{{asset('assets/img/select.svg')}}">
                        <span class="mt-1">{{__('common.select')}}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="modal-save-as-specific-content" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('prizegame.pages.save_as_title')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex w-100 justify-content-between">
                        <input type="text" name="specific_from" class="datepicker"
                               placeholder="{{ __('common.from') }}" style="width:40%;margin-right:4%;" required/>
                        <input type="text" name="specific_to" class="datepicker"
                               placeholder="{{ __('common.to') }}" style="width:40%;" required/>
                    </div>
                    <input type="hidden" name="content-id" value="">
                    <input type="hidden" name="specific_company_id" value="">
                    <input type="hidden" name="specific_country_id" value="">
                    <button class="button btn-radius float-right m-0 text-uppercase" style="--btn-margin-right: 0px;" onclick="saveAsSpecificContent()">
                        <img class="mr-1" style="width:20px;" src="{{asset('assets/img/select.svg')}}">
                        <span class="mt-1">{{__('common.select')}}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="modal-save-as-content" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('myeap.pages.save_as')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select name="type-id">
                        @foreach($types as $type)
                            <option value="{{$type->id}}">{{$type->name}}</option>
                        @endforeach
                    </select>
                    <select name="language-id">
                        @foreach($languages as $language)
                            <option value="{{$language->id}}">{{$language->name}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="content-id" value="">
                    <button class="button btn-radius float-right m-0 text-uppercase" style="--btn-margin-right: 0px;" onclick="saveAsContent()">
                        <img class="mr-1" style="width:20px;" src="{{asset('assets/img/select.svg')}}">
                        <span class="mt-1">{{__('common.select')}}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
