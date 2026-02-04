@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <style>
        .list-elem {
            background: rgb(222, 240, 241);
            color: black;
            margin-right: 10px;
            min-width: 200px;
        }

        a.button {
            padding: 20px 40px;
            background: rgb(0, 87, 95);
            border: none;
            color: white;
            text-transform: uppercase;
            margin-right: 10px;
        }

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

        .unread_counter {
            float: right;
            background-color: rgb(89, 198, 198);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            padding: 8px;
            text-align: center;
            color: white;
        }

        .unread {
            background-color: rgb(89, 198, 198);
            color: white;
        }

        .unread a {
            color: white;
        }

        .deleted {
            background: rgba(195, 203, 207, 1);
        }

        a#filter {
            color: white;
            font-weight: bold;
            background-color: rgb(89, 198, 198);
            border-radius: 0px;
            text-transform: uppercase;
            padding: 10px 40px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 2px solid transparent;
            outline: none !important;
        }

    </style>
@endsection

@section('extra_js')
    <script>
        function toggleCompanies(countryId, element) {
            if ($(element).hasClass('active')) {
                $(element).removeClass('active');
                $('.list-element .caret-left').html(`<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                    </svg>`);
                $('.list-element').each(function () {
                    if ($(this).data('country') && $(this).data('country') == countryId) {
                        $(this).addClass('d-none');
                    }
                });
            } else {
                $(element).addClass('active')
                $(element).find('button.caret-left').html(`<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                                    </svg>`);
                $('.list-element').each(function () {
                    if ($(this).data('country') && $(this).data('country') == countryId) {
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
        <div class="col-12 ">
            {{ Breadcrumbs::render('eap-online.mails') }}
            <h1>{{__('eap-online.mails.menu')}}</h1>
            <a href="{{route('admin.eap-online.mails.filter.view')}}" id="filter" class="mb-4 mt-3 btn-radius btn-max-width">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>{{__('workshop.apply_filter')}}</a>
        </div>
        <div class="col-12">
            @if(!empty($mails) && count($mails) > 0)
                @foreach($countries as $country)
                    <div class="list-element col-12 group" onClick="toggleCompanies({{$country->id}}, this)">
                        <div class="d-flex align-items-center">
                            <p class="mr-3">{{$country->code}}</p>
                            @if(array_key_exists($country->code, $unread_mails))
                                <span class="unread_counter">{{$unread_mails[$country->code]}}</span>
                            @endif
                        </div>

                        <button class="caret-left float-right">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                    @foreach($mails->filter(function ($mail) use ($country){
                        return $country->id == $mail->country_id;
                      })->sortByDesc('date') as $mail)

                        <div class="@if(isset($mail->is_new)) unread @endif @if(!empty($mail->deleted_at)) deleted @endif list-element col-12 d-none"
                             data-country="{{$country->id}}">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div class="@if(isset($mail->is_new)) unread @endif  @if(!empty($mail->deleted_at)) deleted @endif list-elem">
                                    <span>{{$mail->eap_user->username}}</span>
                                    -
                                    <span>{{\Illuminate\Support\Str::limit($mail->subject, 20)}}</span>
                                    -
                                    <span>{{$mail->category}}</span>
                                    -
                                    <span>{{\Illuminate\Support\Str::limit($mail->eap_user->company->name, 20)}}</span>
                                    -
                                    <span>{{$mail->date}}</span>
                                </div>
                                <div class="d-flex">
                                    @if(!$mail->is_new && empty($mail->deleted_at))
                                        <form method="post"
                                              class="mr-1"
                                              action="{{route('admin.eap-online.mails.restore_notification')}}">
                                            @csrf
                                            <input type="hidden" name="mail_id" value="{{$mail->id}}">
                                            <button class="link-button">{{__('eap-online.mails.restore_notification')}}</button>
                                        </form>
                                    @endif
                                    <a href="{{route('admin.eap-online.mails.view', ['id' => $mail->id])}}">{{__('common.select')}}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            @else
                <p>{{__('eap-online.mails.no_mails')}}</p>
            @endif
        </div>
        <div class="col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.eap-online.actions') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
@endsection
