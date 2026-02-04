@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
    <style>
        .button {
            padding: 20px 40px;
            background: rgb(0, 87, 95);
            border: none;
            color: white;
            text-transform: uppercase;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5">
            {{ Breadcrumbs::render('eap-online.translate-footer-documents') }}
            <h1>EAP online - {{__('eap-online.footer.documents.translate')}}</h1>
        </div>
        <div class="col-12 d-flex flex-column">
            @foreach($menu_points as $menu_point)
                <div class="list-element mt-3">
                    <span class="data mr-0">
                        {{$menu_point->firstTranslation->value}}
                    </span>

                    <a href="{{route('admin.eap-online.footer.document.translate.view', ['id' => $menu_point->id])}}" class="button btn-radius"
                        style="cursor: pointer; color: white; --btn-margin-left: var(--btn-margin-x)">
                        <img src="{{asset('assets/img/select.svg')}}" style="height: 20px; width: 20px" alt="">
                        {{__('common.select')}}
                    </a>

                    @foreach($menu_point->get_ready_languages() as $language_code => $ready)
                        @if($ready)
                            <div style="background-color:rgb(145,183,82); margin-right: 10px"
                                 class="px-3 text-white">
                                {{$language_code}}
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.eap-online.actions') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
@endsection
