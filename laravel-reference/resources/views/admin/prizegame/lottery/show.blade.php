@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/list.css?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/prizegame/lottery.css')}}?v={{time()}}">
@endsection

@section('extra_js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script>
        let runCounter = false;
        const count_vote = {{$total_guess_count}};
        let rand = 0;

        log({{$total_guess_count}});

        function log(val) {
            const n1 = Math.floor((val % 10000) / 1000);
            const n2 = Math.floor((val % 1000) / 100);
            const n3 = Math.floor((val % 100) / 10);
            const n4 = Math.floor((val % 10));

            $('p#n1').html(n1)
            $('p#n2').html(n2)
            $('p#n3').html(n3)
            $('p#n4').html(n4)
        }

        function startCounter() {
            $("#startBtn").removeClass('active').addClass('inactLoive')
            $("#prize_link").removeClass('inactive').addClass('active')
            $(".stop").removeClass('duplicated').addClass('active')
            $(".numTop").removeClass('duplicated')
            $(".numBottom").removeClass('duplicatedBottom')
            $(".lottery-result-box").removeClass('duplicated')
            $("#alert").removeClass('active-alert')

            runCounter = true;
            changeCounter();
        }

        function changeCounter() {
            rand = Math.floor(Math.random() * count_vote) + 1;

            log(rand);
            setTimeout(function () {
                changeCounter();
            }, 100)
        }

        function loadMore(elem){
            $('#more_container').removeClass('d-none').addClass('d-flex');
            $('#load_more_button').addClass('d-none').removeClass('d-flex');
        }
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
    <div class="container" style="padding: 0px">
        <div class="row m-0">
            <h1 class="col-12 pl-0">{{__('prizegame.menu')}}</h1>

            <div class="d-flex flex-column">
                @if(isset($games) && $games->count() > 0)
                    @if($games->count() > 3)
                        <a id="load_more_button" onclick="loadMore()" class="list-element group d-flex justify-content-center align-items-center"
                           style="padding: 10px 13px 10px 10px;">
                            <span class="date">
                                {{__('common.load-more')}}
                            </span>
                        </a>

                        @php
                            $viewable_games = $games->splice($games->count() - 3);
                        @endphp
                        <div id="more_container" class="d-none flex-column">
                            @foreach($games as $listed_game)
                                <a href="{{route('client.prizegame.lottery.show', ['game' => $listed_game])}}"
                                   class="list-element group"
                                   style="padding: 10px 13px 10px 10px; @if($listed_game->id != $game->id) opacity: 50%; @endif margin: 0;">
                                    <p style="font-family: CalibriI; font-weight: normal;"
                                       class="test"> {{__('prizegame.lottery.period')}}:
                                        <span class="date">{{\Carbon\Carbon::parse($listed_game->from)->format('Y-m-d')}}</span>
                                        -
                                        <span class="date">{{\Carbon\Carbon::parse($listed_game->to)->format('Y-m-d')}}</span>
                                    </p>
                                </a>
                            @endforeach
                        </div>

                        @foreach($viewable_games as $listed_game)
                            <a href="{{route('client.prizegame.lottery.show', ['game' => $listed_game])}}"
                               class="list-element group"
                               style="padding: 10px 13px 10px 10px; @if($listed_game->id != $game->id) opacity: 50%; @endif">
                                <p style="font-family: CalibriI; font-weight: normal;"
                                   class="test"> {{__('prizegame.lottery.period')}}:
                                    <span class="date">{{\Carbon\Carbon::parse($listed_game->from)->format('Y-m-d')}}</span>
                                    -
                                    <span class="date">{{\Carbon\Carbon::parse($listed_game->to)->format('Y-m-d')}}</span>
                                </p>
                            </a>
                        @endforeach
                    @else
                        @foreach($games as $listed_game)
                            <a href="{{route('client.prizegame.lottery.show', ['game' => $listed_game])}}"
                               class="list-element group"
                               style="padding: 10px 13px 10px 10px; @if($listed_game->id != $game->id) opacity: 50%; @endif">
                                <p style="font-family: CalibriI; font-weight: normal;"
                                   class="test"> {{__('prizegame.lottery.period')}}:
                                    <span class="date">{{\Carbon\Carbon::parse($listed_game->from)->format('Y-m-d')}}</span>
                                    -
                                    <span class="date">{{\Carbon\Carbon::parse($listed_game->to)->format('Y-m-d')}}</span>
                                </p>
                            </a>
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
    <div class="border-box">
        <div class="container">
            <div class="row">
                <div class="container">
                    <p id="demo"></p>
                    <div class="row numBer">
                        <div class="numBox col-lg-3 col-md-6 col-xs-12">
                            <div class="numContainer">
                                <div class="numTop"></div>
                                <div class="numMiddle">
                                    <p id="n1" class="num"></p>
                                </div>
                                <div class="numBottom"></div>
                            </div>
                        </div>
                        <div class="numBox col-lg-3 col-md-6 col-xs-12">
                            <div class="numContainer">
                                <div class="numTop"></div>
                                <div class="numMiddle">
                                    <p id="n2" class="num"></p>
                                </div>
                                <div class="numBottom"></div>
                            </div>
                        </div>
                        <div class="numBox col-lg-3 col-md-6 col-xs-12">
                            <div class="numContainer">
                                <div class="numTop"></div>
                                <div class="numMiddle">
                                    <p id="n3" class="num"></p>
                                </div>
                                <div class="numBottom"></div>
                            </div>
                        </div>
                        <div class="numBox col-lg-3 col-md-6 col-xs-12">
                            <div class="numContainer">
                                <div class="numTop"></div>
                                <div class="numMiddle">
                                    <p id="n4" class="num"></p>
                                </div>
                                <div class="numBottom"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="buttonBox">
                    <a href="#"
                       @if($game->status == \App\Models\PrizeGame\Game::STATUS_CLOSED || $game->status == \App\Models\PrizeGame\Game::STATUS_DRAWN)
                       onclick="startCounter()"
                       class="start btn-radius active"
                       @else
                       class="start btn-radius inactive"
                       @endif
                       id="startBtn">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{__('prizegame.lottery.prize_start')}}</a>
                    <form action="{{route('admin.prizegame.lottery.store',['game' => $game]) }}" method="post">
                        @csrf
                        <button type="submit" id="prize_link"
                                class="stop btn-radius inactive">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                            </svg>
                            {{__('prizegame.lottery.prize_stop')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="lottery-result">

            @if($winners->count() > 0)
                <p class="lottery-title">{{__('prizegame.lottery.prize_result')}}:</p>
                @foreach($winners as $winner)
                    <div id="{{$loop->index + 1}}" class="lottery-result-box">
                        <p>{{ $loop->index + 1}} - {{ $winner->guess->username }}
                            - {{ $winner->guess->email }}</p>
                    </div>
                @endforeach
                <form action="{{route('admin.prizegame.lottery.export',['id' => $game->id]) }}" method="post">
                    @csrf
                    <button style="border: none" type="submit"
                            class="export-excel">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height:20px; margin-bottom: 3px"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        {{__('prizegame.lottery.excel_download')}}
                    </button>
                </form>

            @endif
        </div>
    </div>
@endsection
