<div>
    @if($opened)
        <div class="list-element-div" style="display: block">
            <x-invoice-helper.search />

            @foreach($companies as $company)
                <div class="invoice-list-holder" wire:key='company-{{$company->id}}'>
                    <div class="case-list-in col-12 group" wire:click='toggleOpenCompany({{$company->id}})'>
                        <span>{{$company->name}}</span>
                        <div class="float-right" style="{{optional($company->direct_invoices()->with('completion_certificate')->whereMonth('to', Carbon\Carbon::parse($date)->format('m'))->first()->completion_certificate)->printed_at ? 'color:#a33095;' : 'color: #59c6c6;'}}">
                            @if(optional($company->direct_invoices()->with('completion_certificate')->whereMonth('to', Carbon\Carbon::parse($date)->format('m'))->first()->completion_certificate)->printed_at)
                                <svg wire:loading.delay.remove xmlns="http://www.w3.org/2000/svg" style="height: 20px; width:20px; margin-right:5px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <svg wire:loading.delay.remove xmlns="http://www.w3.org/2000/svg" style="height: 20px; width:20px; margin-right:5px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            @endif

                            <div wire:loading.delay>
                                <img style="width: 20px; height: 20px" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >
                            </div>
                           <span wire:loading.delay.remove wire:click='download_completion_certificate({{$company->id}}, "{{$date}}")'>{{__('invoice-helper.completion-certificate.print')}}</span>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($companies->hasMorePages())
                <div class="load-more-container" style="gap:0px;">
                    <div class="green-box load-more-cases button-c btn-radius" wire:click="loadMore">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        {{__('invoice-helper.load-more')}}
                    </div>

                    <div class="green-box load-more-cases button-c btn-radius" wire:click="loadAll">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                        </svg>
                        {{__('invoice-helper.load-all')}}
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
