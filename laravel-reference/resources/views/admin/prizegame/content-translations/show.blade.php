@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/translations.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <style>
        form{
            max-width: max-content !important;
            margin-top: 0 !important;
        }

        button[type="submit"]{
            width: auto !important;
            margin-bottom: 40px !important;
        }
    </style>
@endsection

@section('extra_js')
<script src="/assets/js/eap-online/translations.js?v={{time()}}" charset="utf-8"></script>
<script>
    @if(session()->has('success'))
    Swal.fire(
        '{{session()->get("success")}}',
        '',
        'success'
    );
    @endif
</script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5 p-0">
            {{ Breadcrumbs::render('prizegame.translations.pages') }}
            <h1>{{__('prizegame.pages.menu')}}</h1>
        </div>

        <div class="col-12 row w-100">
            @foreach ($content->sections as $section)
                @component('components.prizegame.translation-line', [
                    'id' => $section->id,
                    'model' => $section,
                    'languages' => $languages,
                ])@endcomponent

                @if($section->documents)
                @component('components.prizegame.translation-line', [
                    'id' => $section->documents->id,
                    'model' => $section->documents,
                    'languages' => $languages,
                ])@endcomponent
                @endif
            @endforeach

            @foreach ($content->questions as $question)
            @component('components.prizegame.translation-line', [
                'id' => $question->id,
                'model' => $question,
                'languages' => $languages,
            ])@endcomponent

            @foreach ($question->answers as $answer)
            @component('components.prizegame.translation-line', [
                'id' => $answer->id,
                'model' => $answer,
                'languages' => $languages,
            ])@endcomponent
            @endforeach
            @endforeach
        </div>

        <div class="row col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.prizegame.translation.pages.index') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
@endsection
