@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/master.css?v={{time()}}">
    <link href="/assets/css/chosen.css" rel="stylesheet" type="text/css">
@endsection

@section('extra_js')
    <script>
        @if(session()->has('permission-saved'))
            Swal.fire(
                '{{__('common.case_input_edit.successful_save')}}',
                '',
                'success'
            );
        @endif
    </script>
@endsection

@section('content')
    <div class="col-12">
        {{ Breadcrumbs::render('eap-online.video-therapy.actions.permissions') }}
        <h1>{{__('eap-online.video_therapy.permissions')}}</h1>

        <form class="col-12 row d-flex flex-column"
            action="{{route('admin.eap-online.video_therapy.actions.permissions.store')}}"
            method="post"
        >
            {{ csrf_field() }}
            @foreach ($countries as $country)
                <livewire:admin.eap-online.online-therapy-country-toggle-component :country="$country"/>
            @endforeach
            <button class="btn-radius mt-5" style="--btn-max-width: var(--btn-min-width);">
                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                {{__('common.save')}}
            </button>
        </form>
    </div>
@endsection
