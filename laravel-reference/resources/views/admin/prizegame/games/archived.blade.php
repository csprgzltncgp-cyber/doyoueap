@extends('layout.master')
@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/eap-online/articles.css?v={{time()}}">

@endsection

@section('extra_js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="/assets/js/datetime.js" charset="utf-8"></script>
    <script>
        $(function () {
            $('.datepicker').datepicker({
                'format': 'yyyy-mm-dd'
            });
        });
    </script>
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
        {{ Breadcrumbs::render('prizegame.games.archived') }}
        <h1 class="col-12 pl-0">{{__('prizegame.games.archived_menu')}}</h1>
        @foreach($games as $game)
            <div class="list-element col-12 group">
                <div class="container-fluid">
                    <div class="row" style="cursor: default">
                        <div class="col-4 d-flex align-items-center">
                            <p class="mr-5">{{$game->content->company->name}} <br>
                                <small>({{$game->content->country->name}} - {{$game->content->language->name}}
                                    - {{$game->content->type->name}})</small>
                            </p>
                        </div>
                        <div class="col-8 d-flex align-items-center justify-content-end">
                                <span class="mr-3" style="cursor: pointer">
                                     <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                                          style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                          stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                      <span id="{{$game->id}}_from"
                                            date="{{\Carbon\Carbon::parse($game->from)->format('Y-m-d')}}">{{\Carbon\Carbon::parse($game->from)->format('Y-m-d')}}</span>
                                    -
                                    <span id="{{$game->id}}_to"
                                          date="{{\Carbon\Carbon::parse($game->to)->format('Y-m-d')}}">{{\Carbon\Carbon::parse($game->to)->format('m-d')}}</span>
                                </span>
                            <p class="mr-3">
                                <span style="color: rgb(235,126,48);" class="mr-0">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    {{__('prizegame.lottery.drawn')}}
                                </span>
                            </p>
                            
                            <form method="POST" action="{{route('admin.prizegame.lottery.export', ['id' => $game->id])}}" class="pl-0">
                                @csrf
                                <button type="submit" style="background:transparent !important; border: none; color:#007bff" class="pl-0">
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                    style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    {{__('prizegame.download_results')}}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
