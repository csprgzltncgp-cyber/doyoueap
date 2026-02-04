
<div class="row m-0">
    {{ Breadcrumbs::render('companies') }}
    <h1 class="col-12 pl-0">{{__('common.list_of_companies')}}</h1>

    <a class="col-12 pl-0 d-block" href="{{route(auth()->user()->type . '.companies.new')}}">{{__('common.add-new-company')}}</a>

    <x-invoice-helper.search />

    <div wire:loading.delay class="w-100">
        <img style="width: 40px; height: 40px; margin:0 auto; width:100%;" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
    </div>
    <div wire:loading.delay.remove class="w-100">
        @if(empty($this->search))
            @foreach($countries as $country)
                <div class="list-element case-list-in mb-0 col-12 group" onClick="toggleList({{$country->id}}, this, event)">
                    {{$country->code}}
                    <button class="caret-left float-right">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                @foreach($companies->filter(function ($company) use ($country){
                        return in_array($country->id, $company->countries->pluck('id')->toArray());
                    }) as $company)
                        <div class="list-element col-12 d-none" data-country="{{$country->id}}">
                            @if($company->is_connected)
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px;  margin-right: -7px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 14px; height:14px;" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                            @endif
                            <span>{{$company->name}}</span>
                            <button class="float-right delete-button" onClick="deleteCompany({{$company->id}}, this)">
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                    style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                {{__('common.delete')}}
                            </button>
                            <a class="float-right" href="{{route(auth()->user()->type .'.companies.edit',['company' => $company])}}">
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                    style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{__('common.edit')}}
                            </a>
                            <a class="float-right" href="{{route(auth()->user()->type .'.companies.inputs',['company' => $company])}}">
                                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                                    style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{__('common.edit-of-inputs')}}
                            </a>
                        </div>
                @endforeach
            @endforeach
        @else
            @foreach($companies as $company)
                <div class="list-element col-12">
                    @if($company->is_connected)
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px;  margin-right: -7px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 14px; height:14px;" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                    @endif
                    <span>{{$company->name}}</span>
                    <button class="float-right delete-button" onClick="deleteCompany({{$company->id}}, this)">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                            style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{__('common.delete')}}
                    </button>
                    <a class="float-right" href="{{route(auth()->user()->type .'.companies.edit',['company' => $company])}}">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                            style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{__('common.edit')}}
                    </a>
                    <a class="float-right" href="{{route(auth()->user()->type .'.companies.inputs',['company' => $company])}}">
                        <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                            style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{__('common.edit-of-inputs')}}
                    </a>
                </div>
            @endforeach
        @endif
    </div>
</div>
