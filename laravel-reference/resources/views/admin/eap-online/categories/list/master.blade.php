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
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            {{ Breadcrumbs::render('eap-online.categories') }}
            <h1>EAP online - {{__('eap-online.categories.edit')}}</h1>
        </div>
        <div class="col-12 mb-3 row ml-0">
            <a href="{{route('admin.eap-online.categories.list.all-articles')}}" class="category-button btn-radius">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                  </svg>
                <span style="padding-top: 2px;">
                    {{__('eap-online.articles.all_articles')}}
                </span>
            </a>
            <a href="{{route('admin.eap-online.categories.list.all-videos')}}" class="category-button btn-radius">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z" />
                  </svg>
                <span style="padding-top: 2px;">
                    {{__('eap-online.videos.category_title')}}
                </span>
            </a>
            <a href="{{route('admin.eap-online.categories.list.all-webinars')}}" class="category-button btn-radius">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-1" style="width:20px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                  </svg>

                <span style="padding-top: 2px;">
                    {{__('eap-online.webinars.category_title')}}
                </span>
            </a>
            <a href="{{route('admin.eap-online.categories.list.all-podcasts')}}" class="category-button btn-radius">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                  </svg>
                <span style="padding-top: 2px;">
                    {{__('eap-online.podcasts.category_title')}}
                </span>
            </a>
            <a href="{{route('admin.eap-online.categories.list.self-help')}}" class="category-button btn-radius">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width:20px" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                  </svg>
                <span style="padding-top: 2px;">
                    {{__('eap-online.articles.categorize_self-help')}}
                </span>
            </a>
        </div>

        @yield('category_list')

        <div class="col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.eap-online.actions') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
@endsection
