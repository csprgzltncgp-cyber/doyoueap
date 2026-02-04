@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <style>
        .list-elem {
            padding: 20px 40px;
            background: rgb(0, 87, 95);
            color: white;
            text-transform: uppercase;
        }

        .list-elem:hover {
            color: white;
        }

        .list-holder {
            display: grid;
            grid-template-columns: 2fr 2fr 2fr;
            grid-gap: 20px;
        }

        .list-elem {
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5">
             {{ Breadcrumbs::render('compnay-website') }}
            <h1>{{__('company-website.actions.title')}}</h1>
        </div>
        <div class="col-12 mb-5">

            <div class="d-flex flex-column">
                <h1>{{__('eap-online.actions.settings')}}</h1>
                <div class="list-holder">
                    <a class="list-elem" href="{{route('admin.company-website.articles.index')}}">{{__('company-website.actions.articles.menu')}}</a>
                </div>
            </div>

            <div class="d-flex flex-column">
                <h1>{{__('eap-online.actions.translation')}}</h1>
                <div class="list-holder">
                    <a class="list-elem" href="{{route('admin.company-website.articles.translation.index')}}">{{__('eap-online.articles.articles')}}</a>
                </div>
            </div>

        </div>
    </div>
@endsection
