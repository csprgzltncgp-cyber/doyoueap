<div class="row">
    <div class="col-12">
        {{ Breadcrumbs::render('cases-summary') }}
        <h1>{{ __('common.case_summaries') }}</h1>
    </div>
    <div class="col-12 case-list-holder">
        <div class="search-container">
            <div class="search-holder">
                <input placeholder="{{__("common.search")}}" wire:model='search'>
                <div class="green-box button-c" wire:click="resetSearch">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{__('common.delete-filter')}}
                </div>
            </div>
        </div>
        @if(empty($search))
            @foreach($filtered_year_months as $filtered_year_month)
                <div class="case-list-in col-12 group" wire:click="toggle('{{$filtered_year_month}}')">
                    {{$filtered_year_month}}
                    <button class="caret-left float-right">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             style="width: 20px; height: 20px; @if(in_array($filtered_year_month, $opened_year_months)) transform: rotateZ(180deg); @endif" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                <div class="case-summary-list @if(!in_array($filtered_year_month, $opened_year_months)) d-none @endif">
                    @foreach($cases as $case)
                        @if (substr($case->values->where('case_input_id', 1)->first()->value, 0, -3) == $filtered_year_month)
                            <div class="list-element col-12">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <p>{{$case->case_identifier}}: <span style="font-family: CalibriI; font-weight: normal;">{{$case->values->where('case_input_id', 64)->first()->value}}</span></p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        @else
            <div class="case-summary-list">
                @foreach($cases as $case)
                    <div class="list-element col-12">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <p>{{$case->case_identifier}}: <span style="font-family: CalibriI; font-weight: normal;">{{$case->values->where('case_input_id', 64)->first()->value}}</span></p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
