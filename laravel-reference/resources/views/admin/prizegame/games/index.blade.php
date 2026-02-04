@extends('layout.master')
@section('extra_css')
    {{-- <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}"> --}}
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/prizegame.css')}}?v={{time()}}">

@endsection

@section('extra_js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="{{asset('assets/js/datetime.js')}}" charset="utf-8"></script>
    <script src="{{asset('assets/js/prizegame.js')}}" charset="utf-8"></script>
@endsection

@section('title')
    @if(Auth::user()->type == 'admin')
        Admin Dashboard
    @else
        Client Dashboard
    @endif
@endsection

@section('content')
    <div class="row m-0">
        {{ Breadcrumbs::render('prizegame.games') }}
        <h1 class="col-12 pl-0">{{__('prizegame.games.running_menu')}}</h1>
        @foreach($games as $game)
            <x-prizegame.list-item :game="$game"/>
        @endforeach

        <div class="row col-4 col-lg-2 back-button mt-4">
            <a href="{{ route('admin.prizegame.actions') }}">{{__('common.back-to-list')}}</a>
        </div>
@endsection

@section('modal')
<div class="modal" tabindex="-1" id="modal-set-date" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('eap-online.video_therapy.set_date')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.prizegame.games.set-date')}}" method="post">
                    {{csrf_field()}}
                    <div class="d-flex w-100 justify-content-between">
                        <input type="text" name="from" class="datepicker"
                                placeholder="{{ __('common.from') }}" style="width:40%;margin-right:4%;"
                                required/>
                        <input type="text" name="to" class="datepicker"
                                placeholder="{{ __('common.to') }}" style="width:40%;" required/>
                    </div>
                    <input type="hidden" name="game_id" value="">
                    <button class="button btn-radius float-right m-0">
                        {{__('common.select')}}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
