@props([
    'game'
])

<div class="prizegame-list-element col-12 group" style="cursor: default">
    <div class="container-fluid">
        <div class="row" style="cursor: default">
            <div class="col-2 d-flex align-items-center">
                <p class="mr-5">{{$game->content->company->name}} <br>
                    <small>({{$game->content->country->name}} - {{$game->content->language->name}}
                        - {{$game->content->type->name}})</small>
                </p>
            </div>
            <div class="col-10 d-flex align-items-center justify-content-end">
                @if($game->status != \App\Models\PrizeGame\Game::STATUS_DRAWN)
                    <span class="mr-3" style="cursor: pointer">
                         <svg onclick="setDate({{$game->id}})" xmlns="http://www.w3.org/2000/svg" class="mr-1"
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
                @endif
                <a class="mr-3"
                   href="{{route('admin.prizegame.pages.edit', ['content' => $game->content->id])}}"
                   style="cursor: pointer; text-decoration: none; color: black;">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    {{__('common.edit')}}
                </a>
                <span class="mr-3" onclick="deleteGame({{$game->id}})"
                      style="cursor: pointer;">
                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                         style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    {{__('common.delete')}}
                </span>

                @if($game->status == \App\Models\PrizeGame\Game::STATUS_CLOSED || $game->status == \App\Models\PrizeGame\Game::STATUS_ACTIVE)
                    <a class="mr-3" href="{{route('admin.prizegame.lottery.show', ['game' => $game])}}"
                       style="cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1"
                             style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                        {{__('prizegame.games.lottery')}}
                    </a>
                @endif

                <p class="mr-3">
                    @switch($game->status)
                        @case(\App\Models\PrizeGame\Game::STATUS_ACTIVE)
                        <span style="color: rgb(115,144,52);">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{__('crisis.active')}}
                        </span>
                        @break
                        @case(\App\Models\PrizeGame\Game::STATUS_CLOSED)
                        <span style="color:rgb(127,64,116);">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{__('crisis.closed')}}
                        </span>
                        @break
                        @case(\App\Models\PrizeGame\Game::STATUS_PENDING)
                        <span style=" color: rgb(212, 199, 31)">
                            <svg class="mr-1"
                                 xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{__('common.pending')}}
                        </span>
                        @break
                    @endswitch
                </p>
                <p>
                    @if($game->is_viewable)
                        <span onclick="setViewable({{$game->id}}, false)"
                              style="color: rgb(115,144,52); cursor: pointer">
                             <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                  style="height:20px; margin-bottom: 3px" fill="none"
                                  viewBox="0 0 24 24"
                                  stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            {{__('prizegame.games.viewable')}}
                        </span>
                    @else
                        <span onclick="setViewable({{$game->id}},true)"
                              style="color:rgb(127,64,116); cursor: pointer; ">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                 style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{__('prizegame.games.not_viewable')}}
                        </span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
