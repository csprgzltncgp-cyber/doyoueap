@section('extra_js')
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

            document.getElementById('n1').innerHTML = n1;
            document.getElementById('n2').innerHTML = n2;
            document.getElementById('n3').innerHTML = n3;
            document.getElementById('n4').innerHTML = n4;
        }

        function startCounter() {
            document.getElementById('startBtn').classList.add('opacity-50');
            document.getElementById('startBtn').classList.remove('hover:bg-opacity-30');
            document.getElementById('startBtn').classList.remove('hover:text-green-light');
            document.getElementById('startBtn').setAttribute('disabled', 'disabled');
            document.getElementById('prize_link').classList.remove('opacity-50');
            document.getElementById('prize_link').classList.add('hover:bg-opacity-30');
            document.getElementById('prize_link').classList.add('hover:text-green-light');
            document.getElementById('prize_link').removeAttribute('disabled');

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
    </script>
@endsection

@extends('layout.client.master', ['bg' => 'prizegame'])

@section('content')
    @if($games->count() > 1)
        <div class="flex justify-center py-2 space-x-5 text-black uppercase bg-white bg-opacity-60">
            @foreach($games as $game_group)
                <a href="{{route('client.prizegame.show', ['country' => $game_group->first()->content->country])}}"
                   class="cursor-pointer @if($game->content->country->id == $game_group->first()->content->country->id) underline @endif">
                    {{$game_group->first()->content->country->name}}
                </a>
            @endforeach
        </div>
    @endif
    <div class="bg-green-light bg-opacity-70 text-black uppercase p-12 text-2xl font-bold flex justify-center">
        <p class="mx-auto break-words text-center">{{__('prizegame.lottery.guess_count')}} {{$game->guesses()->count()}}</p>
    </div>
    <div class="flex items-center justify-center py-12 text-white bg-purple bg-opacity-70">
        <p class="w-3/4 break-words">{{__('riport.prize_game_desc')}}</p>
    </div>
@endsection

@section('extra_content')
    <div class="bg-purple pt-20 pb-3 flex flex-col items-center justify-center">
        @if($game->status == \App\Models\PrizeGame\Game::STATUS_CLOSED || $game->status == \App\Models\PrizeGame\Game::STATUS_DRAWN)
            <div class="flex space-x-5 items-center justify-center mt-2 mb-5">
                <button onclick="startCounter()" id="startBtn" type="button"
                        class="2xl:text-xl outline-none mx-auto uppercase font-light text-white px-16 py-3 bg-green-light rounded-full translation-all duration-300 hover:bg-opacity-30 hover:text-green-light">
                    {{__('prizegame.lottery.prize_start')}}
                </button>
                <form action="{{route('client.prizegame.store',['game' => $game]) }}" method="post">
                    @csrf
                    <button disabled="disabled" id="prize_link" type="submit"
                            class="2xl:text-xl outline-none mx-auto uppercase font-light text-white px-16 py-3 bg-green-light rounded-full translation-all duration-300 opacity-50">
                        {{__('prizegame.lottery.prize_stop')}}
                    </button>
                </form>
            </div>
        @endif
        <div class="flex justify-center align-items-center space-x-20 text-white -mt-10 font-lores"
             style="font-size: 200px;">
            <p id="n1">0</p>
            <p id="n2">0</p>
            <p id="n3">0</p>
            <p id="n4">0</p>
        </div>
    </div>
    @if($winners->count() > 0)
        <div class="py-20 bg-purple bg-opacity-20 flex flex-col justify-center items-center">
            <div class="grid gap-10 mb-20 justify-center"
                 style="grid-template-columns: repeat({{min($winners->count(), 3)}}, minmax(0, 1fr));">
                @foreach($winners as $winner)
                    <div class="p-10 bg-white shadow rounded-lg flex flex-col items-center justify-center">
                        <img class="h-10 mb-7" src="{{asset('assets/img/client/prizegame_winner_badge.svg')}}"
                             alt="prizegame_winner_badge">
                        <h1 class="text-3xl uppercase font-bold mb-2">{{__('common.winner')}}
                            #{{$loop->index  + 1}}</h1>
                        <p>
                            <span class="font-bold">{{__('prizegame.lottery.username')}}:</span> {{$winner->guess->username}}
                        </p>
                        <p>
                            <span class="font-bold">{{__('prizegame.lottery.email')}}:</span> {{$winner->guess->email}}
                        </p>
                    </div>
                @endforeach
            </div>
            <form action="{{route('client.prizegame.export',['game' => $game]) }}" method="post">
                @csrf
                <button type="submit"
                        class="mt-4 transition duration-300 ease-in-out border border-black rounded-full px-10 py-2 uppercase  hover:bg-opacity-20 hover:bg-black border-black">
                    {{__('prizegame.lottery.excel_download')}}
                </button>
            </form>

        </div>
    @endif
@endsection
