<div id="prizegame_container">
    <div class="mt-5 fix-activity">
        <div class="header">
            <h2>{{__('common.prize_game')}}</h2>
        </div>

        <div class="mt-3">
            @foreach ($games as $game)
                <x-prizegame.list-item :game="$game"/>
            @endforeach

            @if(!$games->count())
                <center>{{__('data.no_data')}}</center>
            @endif
        </div>
    </div>
</div>
