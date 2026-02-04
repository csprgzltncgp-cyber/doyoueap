@php
    $index_url = route(auth()->user()->type . '.affiliate_searches.index');
    $issued_url = route(auth()->user()->type . '.affiliate_searches.issued');
    $filter_url = route(auth()->user()->type . '.affiliate_searches.filter');
    $all_url = route(auth()->user()->type . '.affiliate_searches.all');
    $statistics_url = route(auth()->user()->type . '.affiliate_searches.statistics');

    $unread_from_comments = \App\Models\AffiliateSearch::query()->where('from_id', auth()->id())->get()->map(function($search){
        return $search->has_new_comments();
    })->sum();

    $unread_to_comments = \App\Models\AffiliateSearch::query()->where('to_id', auth()->id())->get()->map(function($search){
        return $search->has_new_comments();
    })->sum();
@endphp


<a href="{{route(auth()->user()->type . '.affiliate_searches.create')}}">{{__( 'affiliate-search-workflow.create')}}</a>

<div class="row">
    <div class="col-10">
        <a class="button-link btn-radius float-left mt-3 {{url()->current() == $index_url ? 'active' : ''}}" href="{{$index_url}}">
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 5px;" class="mr-1"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            {{__( 'affiliate-search-workflow.index')}}
            @if($unread_to_comments)
                <span style="width: 20px; height:20px; text-align: center; background-color: #007bff; border-radius: 50%; float: right; color: white; margin-top:2px;"
                    class="unread_counter ml-2">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 14px; height:14px" class="mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </span>
            @endif
        </a>

        <a class="button-link btn-radius float-left mt-3 {{url()->current() == $issued_url ? 'active' : ''}}" href="{{$issued_url}}">
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 5px;" class="mr-1"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
            </svg>
            {{__( 'affiliate-search-workflow.issued')}}
            @if($unread_from_comments)
                <span style="width: 20px; height:20px; text-align: center; background-color: #007bff; border-radius: 50%; float: right; color: white; margin-top:2px;"
                    class="unread_counter ml-2">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 14px; height:14px" class="mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </span>
            @endif
        </a>

        @if(Auth::user()->type == 'admin')
            <a class="button-link btn-radius float-left  mt-3 {{url()->current() == $all_url ? 'active' : ''}}"
                href="{{$all_url}}">
                    <svg xmlns="http://www.w3.org/2000/svg"  style="width: 20px; height: 20px; margin-bottom: 5px;" class="mr-1"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                {{__('task.all_task')}}
            </a>

            <a class="button-link btn-radius float-left {{url()->current() == $statistics_url ? 'active' : ''}}"
                href="{{$statistics_url}}">
                    <svg xmlns="http://www.w3.org/2000/svg"  style="width: 20px; height: 20px; margin-bottom: 5px;" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    {{__('task.statistics')}}
            </a>
        @endif
    </div>

    <div class="col-2">
        <a class="button-link btn-radius float-right mr-0 mt-3 {{url()->current() == $filter_url ? 'active' : ''}}"
            href="{{$filter_url}}">
             <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
             {{__('common.filter')}}
         </a>
    </div>
</div>
