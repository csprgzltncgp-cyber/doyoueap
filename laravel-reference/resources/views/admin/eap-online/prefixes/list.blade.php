@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/workshops.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
@endsection

@section('extra_js')
    <script>
        let indexForNewPrefixes = 0;

        function newPrefixSection() {
            const html = `
            <div class="row col-12 d-flex">
                <select name="new_prefixes[${indexForNewPrefixes}][language_id]" class="w-25 mr-5">
                @foreach($languages as $language)
                    <option value="{{$language->id}}">{{$language->name}}</option>
                @endforeach
                </select>
                <input class="w-25" type="text" placeholder="{{__('eap-online.prefix.placeholder')}}"
                    name="new_prefixes[${indexForNewPrefixes}][name]" required>
                <button onclick="deletePrefix(${indexForNewPrefixes})" class="text-center w-auto h-100 ml-5 btn-radius" type="button"
                style="--btn-min-width:auto; --btn-height:48px; --btn-padding-x: 15px">
                    <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
            `;
            $('#prefixes_holder').append(html);
            indexForNewPrefixes++;
        }
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-5">
            {{ Breadcrumbs::render('eap-online.perfixes') }}
            <h1>EAP online - {{__('eap-online.prefix.menu')}}</h1>
        </div>

        <div class="row col-12">
            <div class="col-12 d-flex">
                <h1 class="w-25 mr-5">{{__('eap-online.actions.language')}}</h1>
                <h1 class="w-25">{{__('eap-online.prefix.name')}}</h1>
            </div>
            <div class="col-12">
                <form class="mw-100" action="{{route('admin.eap-online.prefixes.update')}}" method="post">
                    {{csrf_field()}}
                    <div id="prefixes_holder">
                        @foreach($prefixes as $prefix)
                            @component('components.eap-online.prefix_line_component',['prefix' => $prefix, 'index' => $loop->index])@endcomponent
                        @endforeach
                    </div>
                    <div class="row col-12 d-flex">
                        <div class="mr-3">
                            <button class="text-center btn-radius" style="--btn-margin-right: 0px;" type="button" onclick="newPrefixSection()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; width: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="mt-1">
                                    {{__('common.add')}}
                                </span>
                            </button>
                        </div>
                        <div>
                            <button class="text-center btn-radius" type="submit">
                                <img class="mr-1" src="{{asset('assets/img/save.svg')}}" style="height: 20px; width: 20px" alt="">
                                <span class="mt-1">{{__('common.save')}}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="col-4 col-lg-2 back-button mb-5">
            <a href="{{ route('admin.eap-online.actions') }}">{{__('common.back-to-list')}}</a>
        </div>
    </div>
@endsection
