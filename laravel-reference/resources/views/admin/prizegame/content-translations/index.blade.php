@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/translations.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5 p-0">
            {{ Breadcrumbs::render('prizegame.translations.pages') }}
            <h1>{{__('prizegame.pages.menu')}}</h1>
        </div>
        <div class="col-12 row w-100">
            <div class="col-12 row">
                @foreach ($contents->sortBy('language.name') as $content)
                    <div class="col-12 row">
                        <div class="col-12 pl-3 d-flex justify-content-between align-items-center mb-3 line">
                            <p class="m-0 mr-3">
                                {{$content->language->name}} -
                                {{$content->type->name}} -
                                {{$content->country->name}} -
                                {{$content->company->name}}
                            </p>
                            <a href="{{route('admin.prizegame.translation.pages.show', $content->id)}}">
                                {{__('common.select')}}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="row col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.prizegame.actions') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
    </div>
@endsection
