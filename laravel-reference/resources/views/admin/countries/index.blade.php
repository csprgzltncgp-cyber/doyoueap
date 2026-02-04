@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <style>
        .list-element button, .list-element a {
            margin-right: 10px;
            display: inline-block;
        }

        .list-element button.delete-button {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: transparent;
            border: 0px solid black;
            color: #007bff;
            outline: none;
        }

        .list-element {
            cursor: pointer;;
        }
    </style>
@endsection

@section('title')
    Admin Dashboard
@endsection

@section('extra_js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
@endsection

@section('content')
    <div class="row m-0">
        {{ Breadcrumbs::render('countries') }}
        <h1 class="col-12 pl-0">{{__('common.list_of_countries')}}</h1>
        <a href="{{route(\Auth::user()->type.'.countries.create')}}"
           class="col-12 d-block pl-0">{{__('common.add-new-country')}}</a>
        @foreach($countries->sortBy('name') as $country)
            <div class="list-element col-12">
                <span>{{$country->name}}</span>
                <form class="float-right" action="{{route(\Auth::user()->type.'.countries.delete',['country' => $country])}}" method="post">
                    @csrf
                    @method('delete')
                    <button type="submit" class="float-right delete-button">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{__('common.delete')}}
                    </button>
                </form>
                <a class="float-right" href="{{route(\Auth::user()->type.'.countries.edit',['country' => $country])}}">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{__('common.edit')}}</a>
            </div>
        @endforeach
    </div>
@endsection
