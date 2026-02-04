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
            {{ Breadcrumbs::render('eap-online') }}
            <h1>{{__('eap-online.actions.title')}}</h1>
        </div>
        <div class="col-12 mb-5">
            @if(Auth::user()->type == 'admin'|| Auth::user()->type == 'production_admin' ||  Auth::user()->type == 'eap_admin' || Auth::user()->type == 'account_admin')
                <div class="d-flex flex-column">
                    <h1>{{__('eap-online.actions.settings')}}</h1>
                    <div class="list-holder">
                        @if(Auth::user()->type == 'admin'|| Auth::user()->type == 'production_admin' || Auth::user()->type == 'account_admin')
                            <a class="list-elem"
                               href="{{route('admin.eap-online.languages.view')}}">{{__('eap-online.actions.language')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.users.list')}}">{{__('eap-online.actions.users')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.categories.list')}}">{{__('eap-online.categories.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.prefixes.list')}}">{{__('eap-online.prefix.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.articles.list')}}">{{__('eap-online.articles.articles')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.videos.list')}}">{{__('eap-online.videos.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.webinars.list')}}">{{__('eap-online.webinars.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.podcasts.list')}}">{{__('eap-online.podcasts.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.quizzes.list')}}">{{__('eap-online.quizzes.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.contact_information.list')}}">{{__('eap-online.contact_information.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.theme-of-the-month.view')}}">{{__('eap-online.theme_of_the_month.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.translation-statistics')}}">{{__('eap-online.translation_statistics.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.video_therapy.actions')}}">{{__('eap-online.video_therapy.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.footer.menu.index')}}">{{__('eap-online.footer.menu_points.menu')}}</a>
                           <a class="list-elem"
                               href="{{route('admin.eap-online.onsite-consultation.index')}}">{{__('eap-online.onsite_consultation.menu')}}</a>
                           <a class="list-elem"
                               href="{{route('admin.eap-online.live-webinar.index')}}">{{__('eap-online.live-webinars.menu')}}</a>
                        @endif

                        @if(Auth::user()->type == 'admin' || Auth::user()->type == 'eap_admin'|| Auth::user()->type == 'production_admin' || Auth::user()->type == 'account_admin')
                            <a class="list-elem"
                               href="{{route('admin.eap-online.mails.list')}}">{{__('eap-online.mails.mails')}}</a>
                        @endif

                        @if(Auth::user()->type == 'admin' || Auth::user()->type == 'account_admin' || Auth::user()->type == 'production_admin' || Auth::user()->type == 'account_admin')
                            <a class="list-elem"
                               href="{{route('admin.eap-online.menu-visibilities.view')}}">{{__('eap-online.menu-visibilities.menu')}}</a>
                        @endif

                        @if(Auth::user()->type == 'eap_admin')
                            <a class="list-elem"
                                href="{{route('admin.eap-online.video_therapy.actions')}}">{{__('eap-online.video_therapy.menu')}}</a>
                           <a class="list-elem"
                               href="{{route('admin.eap-online.users.list')}}">{{__('eap-online.actions.users')}}</a>
                        @endif
                    </div>
                </div>
            @endif
            @if(Auth::user()->type == 'admin'|| Auth::user()->type == 'production_admin' ||  Auth::user()->type == 'production_translating_admin' || Auth::user()->type == 'account_admin')
                <div class="d-flex flex-column">
                    <h1>{{__('eap-online.actions.translation')}}</h1>
                    <div class="list-holder">
                        @if(Auth::user()->type == 'production_translating_admin')
                            <a class="list-elem"
                               href="{{route('production_translating_admin.eap-online.translation.system.view')}}">{{__('eap-online.actions.system')}}</a>
                           <a class="list-elem"
                              href="{{route('production_translating_admin.eap-online.articles.translate.list')}}">{{__('eap-online.articles.articles')}}</a>
                        @else
                            <a class="list-elem"
                               href="{{route('admin.eap-online.articles.translate.list')}}">{{__('eap-online.articles.articles')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.videos.translate.list')}}">{{__('eap-online.videos.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.quizzes.translate.list')}}">{{__('eap-online.quizzes.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.translation.system.view')}}">{{__('eap-online.actions.system')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.categories.translate.view')}}">{{__('eap-online.categories.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.prefixes.translate.view')}}">{{__('eap-online.prefix.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.theme-of-the-month.translate.view')}}">{{__('eap-online.theme_of_the_month.menu')}}</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.translation.assessment.view')}}">Assessment</a>
                            <a class="list-elem"
                               href="{{route('admin.eap-online.translation.well-being.view')}}">Well-Being</a>
                            <a href="{{route('admin.eap-online.footer.menu.translate.view')}}"
                               class="list-elem">{{__('eap-online.footer.menu_points.menu')}}</a>
                            <a href="{{route('admin.eap-online.footer.document.translate.list')}}"
                               class="list-elem">{{__('eap-online.footer.documents.menu')}}</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
