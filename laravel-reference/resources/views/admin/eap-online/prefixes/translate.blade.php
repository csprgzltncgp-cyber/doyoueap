@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/translations.css?t={{time()}}">
@endsection
@section('extra_js')
    <script src="/assets/js/eap-online/translations.js?v={{time()}}" charset="utf-8"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5 p-0">
            {{ Breadcrumbs::render('eap-online.translate-prefixes') }}
            <h1>{{__('eap-online.prefix.translate')}}</h1>
        </div>
        <form method="post" class="row w-100" action="{{route('admin.eap-online.prefixes.translate.store')}}">
            <div class="col-12">
                {{csrf_field()}}
                @foreach($prefixes as $prefix)
                    <div class="col-12 input">
                        <div class="row">
                            <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line"
                                 onclick="toggleTranslationSection('prefix-{{$prefix->id}}-translations', this)"
                            >
                                <p class="m-0 mr-3">{{ucfirst($prefix->name)}}</p>
                                <div class="d-flex align-items-center">
                                    <div class='d-flex flex-wrap'>
                                        @foreach($languages as $language)
                                            @if(!empty($prefix->get_translation($language->id)))
                                                <div style="background-color:rgb(145,183,82);"
                                                    class="px-2 text-white mr-3 mb-2">
                                                    {{$language->code}}
                                                </div>
                                            @else
                                                <div style="background-color:rgb(219, 11, 32);"
                                                    class="px-2 text-white mr-3 mb-2">
                                                    {{$language->code}}
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" style="min-width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                </div>
                                <input type="hidden" name="prefixes[{{$loop->index}}][id]" value="{{$prefix->id}}"/>
                            </div>
                        </div>
                        <div class="d-none" id="prefix-{{$prefix->id}}-translations">
                            @foreach($languages as $language)
                                <div class="row translation">
                                    <div class="col-1 text-center" style="padding-top:15px;">
                                        {{$language->code}}
                                    </div>
                                    <div class="col-8 pl-0">
                                        <textarea name="prefixes[{{$loop->parent->index}}][text][{{$language->id}}]"
                                                  placeholder="{{__('eap-online.system.translation')}}">{{$prefix->get_translation($language->id)->value ?? ''}}</textarea>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-12 mt-1 mb-4">
                                <button class="button btn-radius btn-max-width d-flex justify-content-center align-items-center" type="submit">
                                    <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                                    {{__('common.save')}}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
        <div class="row col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.eap-online.actions') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
    </div>
@endsection
