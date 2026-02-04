@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/master.css?v={{time()}}">
@endsection

@section('content')
    <div class="row col-12">
        {{ Breadcrumbs::render('eap-online.translation-statistics') }}
        <h1>{{__('eap-online.translation_statistics.menu')}}</h1>
    </div>
    <div class="row d-flex">
        <div class="col-4">
            <h1>{{__('eap-online.articles.articles')}} ({{$data['article_count']}}):</h1>
            <ul>
                @foreach($data['articles'] as $language => $count)
                    <li>{{$language}}: {{$count}}</li>
                @endforeach
            </ul>
        </div>
        <div class="col-4">
            <h1>{{__('eap-online.videos.menu')}}
                @if(array_key_exists('Magyar', $data['videos']))({{$data['videos']['Magyar']}}) @endif
                :</h1>
            <ul>
                @foreach($data['videos'] as $language => $count)
                    <li>{{$language}}: {{$count}}</li>
                @endforeach
            </ul>
        </div>
        <div class="col-4">
            <h1>{{__('eap-online.quizzes.menu')}} ({{$data['quiz_count']}}):</h1>
            <ul>
                @foreach($data['quizzes'] as $language => $count)
                    <li>{{$language}}: {{$count}}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="row col-4 col-lg-2 back-button mb-5">
        <a href="{{ route('admin.eap-online.actions') }}">{{__('common.back-to-list')}}</a>
    </div>
@endsection
