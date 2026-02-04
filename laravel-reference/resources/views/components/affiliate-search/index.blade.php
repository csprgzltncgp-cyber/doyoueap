@props([
    'lineColor' => 'rgba(226,239,241,1)',
    'onlyShowDays' => false,
    'showToUser' => false,
    'showDelete' => false,
    'affiliateSearch'
])

<style>
    .closed-task-line{
        opacity: 0.2 !important;
    }

    .closed-task-line:hover{
        opacity: 1 !important;
        transition: all;
        transition-duration: 0.5s;
    }

    .delete-button{
        background: rgb(89, 198, 198) !important;
        color:white !important;
        margin-left: 0 !important;
        opacity: 0.3;
    }

    .delete-button:hover{
        opacity: 1;
        transition: all;
        transition-duration: 0.5s;
    }

    .status-notification{
        background: rgb(102, 16, 242) !important;
        color:white !important;
        margin-left: 0 !important;
    }

    .status-notification-yellow{
        background: rgb(257, 190, 17) !important;
        color:white !important;
        margin-left: 0 !important;
    }

    .sos{
        background: transparent !important;
        border: 2px solid rgb(219, 11, 32) !important;
        color:rgb(219, 11, 32) !important;
        margin-left: 0 !important;
    }
</style>

<div class="row col-12 case-list-in-progress task-admin-component mb-0
    {{
        ($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_COMPLETED && $affiliateSearch->completed) ||
        ($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_COMPLETED && !$showDelete && !$affiliateSearch->has_new_comments())
        ? 'closed-task-line' : ''
    }}"
>

    <p
            @if(($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_COMPLETED && $affiliateSearch->completed) || ($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_COMPLETED && !$showDelete))
                style="background: rgb(127, 64, 116) !important; color: white;"
            @elseif($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_COMPLETED && $showDelete)
                style="height: 64px;
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 100%,{{$lineColor}} 100%, {{$lineColor}} 100%);
                background: -webkit-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 100%, {{$lineColor}} 100%, {{$lineColor}} 100%);
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 100%, {{$lineColor}} 100%, {{$lineColor}} 100%);
                background: -ms-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 100%, {{$lineColor}} 100%, {{$lineColor}} 100%);
                background: linear-gradient(to right, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 100%, {{$lineColor}}) 100%, {{$lineColor}} 100%);
                "
            @elseif($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_ACTIVE_ON_DASBOARD)
                style="height: 64px;
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 85%,{{$lineColor}} 85%, {{$lineColor}} 100%);
                background: -webkit-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 85%, {{$lineColor}} 85%, {{$lineColor}} 100%);
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 85%, {{$lineColor}} 85%, {{$lineColor}} 100%);
                background: -ms-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 85%, {{$lineColor}} 85%, {{$lineColor}} 100%);
                background: linear-gradient(to right, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 85%, {{$lineColor}}) 85%, {{$lineColor}} 100%);
                "
            @elseif($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_CONTRACT_SIGNED)
                style="height: 64px;
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 71%,{{$lineColor}} 71%, {{$lineColor}} 100%);
                background: -webkit-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 71%, {{$lineColor}} 71%, {{$lineColor}} 100%);
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 71%, {{$lineColor}} 71%, {{$lineColor}} 100%);
                background: -ms-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 71%, {{$lineColor}} 71%, {{$lineColor}} 100%);
                background: linear-gradient(to right, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 71%, {{$lineColor}}) 71%, {{$lineColor}} 100%);
                "
            @elseif($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_CONTRACT_SENT)
                style="height: 64px;
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 57%,{{$lineColor}} 57%, {{$lineColor}} 100%);
                background: -webkit-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 57%, {{$lineColor}} 57%, {{$lineColor}} 100%);
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 57%, {{$lineColor}} 57%, {{$lineColor}} 100%);
                background: -ms-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 57%, {{$lineColor}} 57%, {{$lineColor}} 100%);
                background: linear-gradient(to right, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 57%, {{$lineColor}}) 57%, {{$lineColor}} 100%);
                "
            @elseif($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_AFFILIATE_CONTACTED)
                style="height: 64px;
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 42%,{{$lineColor}} 42%, {{$lineColor}} 100%);
                background: -webkit-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 42%, {{$lineColor}} 42%, {{$lineColor}} 100%);
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 42%, {{$lineColor}} 42%, {{$lineColor}} 100%);
                background: -ms-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 42%, {{$lineColor}} 42%, {{$lineColor}} 100%);
                background: linear-gradient(to right, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 42%, {{$lineColor}}) 42%, {{$lineColor}} 100%);
                "
            @elseif($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_AFFILIATE_FOUND)
                style="height: 64px;
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 28%,{{$lineColor}} 28%, {{$lineColor}} 100%);
                background: -webkit-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 28%, {{$lineColor}} 28%, {{$lineColor}} 100%);
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 28%, {{$lineColor}} 28%, {{$lineColor}} 100%);
                background: -ms-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 28%, {{$lineColor}} 28%, {{$lineColor}} 100%);
                background: linear-gradient(to right, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 28%, {{$lineColor}}) 28%, {{$lineColor}} 100%);
                "
            @elseif($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_SEARCH_STARTED)
                style="height: 64px;
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 14%,{{$lineColor}} 14%, {{$lineColor}} 100%);
                background: -webkit-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 14%, {{$lineColor}} 14%, {{$lineColor}} 100%);
                background: -o-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 14%, {{$lineColor}} 14%, {{$lineColor}} 100%);
                background: -ms-linear-gradient(left, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 14%, {{$lineColor}} 14%, {{$lineColor}} 100%);
                background: linear-gradient(to right, rgba(195,203,207,0.7) 0%, rgba(195,202,207,0.7) 14%, {{$lineColor}}) 14%, {{$lineColor}} 100%);
                "
            @else
                style="height: 64px; background: {{$lineColor}};"
            @endif
    >
        #AS{{$affiliateSearch->id}} -

        @if($onlyShowDays)
            {{Str::title(\Carbon\Carbon::parse($affiliateSearch->deadline)->translatedFormat('l'))}} -
        @else
            {{\Carbon\Carbon::parse($affiliateSearch->deadline)->format('Y-m-d')}} -
        @endif

        {{$affiliateSearch->affiliate_type->translation->value}} -

        {{$affiliateSearch->country->name}} -

        {{optional($affiliateSearch->city)->name}} -

        @if($showToUser)
            {{$affiliateSearch->to->name}}
        @else
            {{$affiliateSearch->from->name}}
        @endif
    </p>

    @if(!$showDelete)
        <a href="{{route(auth()->user()->type . '.affiliate_searches.show', ['affiliateSearch' => $affiliateSearch])}}" class="btn-radius">
            <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
            {{__('common.select')}}
        </a>
    @else
        <a href="{{route(auth()->user()->type . '.affiliate_searches.edit', ['affiliateSearch' => $affiliateSearch])}}" class="btn-radius">
            <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
            {{__('common.select')}}
        </a>
    @endif

    @if($showDelete)
        <a href="#" onclick="deleteAffiliateSearch({{$affiliateSearch->id}})" class="delete-button btn-radius" style="--btn-min-width: var(--btn-func-width);">
            <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </a>
    @endif


    {{-- Notifications --}}
        @if($affiliateSearch->is_new())
            <p class="closeable" style="height: 64px;">
                {{__('common.new')}}
            </p>
        @endif

        @if($affiliateSearch->deadline_type == App\Models\AffiliateSearch::DEADLINE_TYPE_SOS)
            <p class="sos" style="height: 64px;">
                SOS
            </p>
        @endif

        @if($affiliateSearch->status == App\Models\AffiliateSearch::STATUS_SEARCH_STARTED)
            <p class="status-notification">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                {{__('affiliate-search-workflow.status.search_started')}}
            </p>
        @endif

        @if($affiliateSearch->status == App\Models\AffiliateSearch::STATUS_AFFILIATE_FOUND)
            <p class="status-notification">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>

                {{__('affiliate-search-workflow.status.affiliate_found')}}
            </p>
        @endif

        @if($affiliateSearch->status == App\Models\AffiliateSearch::STATUS_AFFILIATE_CONTACTED)
            <p class="status-notification">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 3h5m0 0v5m0-5l-6 6M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z" />
                </svg>

                {{__('affiliate-search-workflow.status.affiliate_contacted')}}
            </p>
        @endif

        @if($affiliateSearch->status == App\Models\AffiliateSearch::STATUS_CONTRACT_SENT)
            <p class="status-notification">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                </svg>

                {{__('affiliate-search-workflow.status.contract_sent')}}

            </p>
        @endif

        @if($affiliateSearch->status == App\Models\AffiliateSearch::STATUS_CONTRACT_SIGNED)
            <p class="status-notification-yellow">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>

                {{__('affiliate-search-workflow.status.contract_signed')}}
            </p>
        @endif

        @if($affiliateSearch->status == App\Models\AffiliateSearch::STATUS_ACTIVE_ON_DASBOARD)
            <p class="status-notification-yellow">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                {{__('affiliate-search-workflow.status.active_on_dashboard')}}
            </p>
        @endif

        @if($showDelete)
            @if($affiliateSearch->status == \App\Models\AffiliateSearch::STATUS_COMPLETED && !$affiliateSearch->completed)
                <p class="closeable" style="background: rgb(127, 64, 116) !important">
                    {{__('task.completed')}}
                </p>
            @endif
        @endif

        @if($affiliateSearch->has_new_comments())
            <p class="_2month" style="background-color: #007bff !important">
                {{__('task.new_comment')}}!
            </p>
        @endif

        @if($affiliateSearch->is_over_deadline())
            <p class="_3month">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{__('task.over_deadline')}}
                : {{\Carbon\Carbon::parse($affiliateSearch->deadline)->diffInDays(\Carbon\Carbon::now())}} {{__('task.day')}}!
            </p>
        @endif

        @if($affiliateSearch->is_last_day())
            <p class="_2month">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{__('task.last_day')}}
            </p>
        @endif


    {{-- Notifications --}}
</div>
