@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">
@endsection

@section('extra_js')
    <script>
        function openModal(id) {
            $(`#${id}`).modal("show");
        }

        function saveLanguage() {
            const language_id = $('select[name="article_language"]').val();
            const url = "{{route('admin.eap-online.video_therapy.actions.psychology.timetable_edit', ['language_id' => ':id'])}}";
            location.replace(url.replace(':id', language_id));
        }
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5">
            {{ Breadcrumbs::render('eap-online.video-therapy.schedule') }}
            <h1 class="row col-12">{{ __('eap-online.video_therapy.video_chat_appointments') }}</h1>
        </div>

        <div class="row col-12">
            <div class="col-12 d-flex">
                <h1 class="w-25 mr-5">{{__('eap-online.actions.language')}}</h1>
                <h1 class="w-25">{{__('riport.record_problem_type')}}</h1>
            </div>
            <div class="col-12">
                    <form class="mw-100" action="{{route(auth()->user()->type . '.eap-online.video_therapy.actions.psychology.timetable_edit')}}" method="get">
                        {{csrf_field()}}
                        <div id="prefixes_holder">
                            <div class="row col-12 d-flex">
                                <select class="w-25 mr-5" name="language" required>
                                    @foreach ($languages as $language)
                                        <option value={{$language->id}}>{{$language->name}}</option>
                                    @endforeach
                                </select>

                                <select class="w-25" name="permission" required>
                                    @foreach ($permissions as $permission)
                                        <option value={{$permission->id}}>{{$permission->slug}}</option>
                                    @endforeach
                                </select>

                                <button class="text-center w-auto h-100 ml-5 btn-radius" type="submit">
                                   {{__('expert-data.next')}}
                                </button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
@endsection
