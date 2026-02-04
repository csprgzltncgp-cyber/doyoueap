<div class="bg-yellow py-10 shadow flex flex-col items-center z-50">
    <style>
        [type="checkbox"]:checked{
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e") !important;
        }
        input:checked + .toggle-bg{
            background-color: #04575F !important;
            border: #04575F !important;
        }
    </style>


    <div class="w-4/5 relative mb-24 mt-5">
        <div class="h-2.5 bg-green rounded-lg w-full"></div>
        <div class="w-full h-full absolute top-0 items-start grid grid-cols-4">
            @for($i = 1; $i <= get_last_quarter(); $i++)
                @if($type != 'normal_riport')
                    @if(has_eap_riport_in_quarter($i))
                        <div class="flex flex-col items-center justify-between -mt-5 group cursor-pointer" wire:click="setQuarter({{$i}})">
                            <div class="h-8 w-8 rounded-full bg-green mb-2 mt-2.5 flex justify-center items-center">
                                @if(in_array($i, $quarter))
                                    <div class="w-6 h-6 rounded-full bg-white border border-green"></div>
                                @endif
                            </div>
                            <p class="text-green">{{__('riport.'. $i .'_quarter')}}</p>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-between -mt-5 group cursor-pointer">
                            <div class="h-8 w-8 rounded-full bg-green mb-2 mt-2.5 flex justify-center items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <p class="text-green">{{__('riport.'. $i .'_quarter')}}</p>
                        </div>
                    @endif
                @else
                    @if(has_riport_in_quarter($i))
                        <div class="flex flex-col items-center justify-between -mt-5 group cursor-pointer" wire:click="setQuarter({{$i}})">
                            <div class="h-8 w-8 rounded-full bg-green mb-2 mt-2.5 flex justify-center items-center">
                                @if(in_array($i, $quarter))
                                    <div class="w-6 h-6 rounded-full bg-white border border-green"></div>
                                @endif
                            </div>
                            <p class="text-green">{{__('riport.'. $i .'_quarter')}}</p>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-between -mt-5 group cursor-pointer">
                            <div class="h-8 w-8 rounded-full bg-green mb-2 mt-2.5 flex justify-center items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <p class="text-green">{{__('riport.'. $i .'_quarter')}}</p>
                        </div>
                    @endif
                @endif
            @endfor
        </div>
    </div>

    @if(!(in_array(1, $quarter) && count($quarter) == 1))
        <div class="flex items-center relative mb-10 {{(!$cumulate) ? 'opacity-50' : ''}}">
            <span class="uppercase text-2xl font-bold mr-3 text-green">
            {{__('riport.cumulate')}}</span>
            <label class="flex items-center cursor-pointer relative">
                <input type="checkbox" class="sr-only" wire:model='cumulate'>
                <div class="toggle-bg bg-green bg-opacity-20 border-2 border-green border-opacity-0 h-6 w-11 rounded-full"></div>
            </label>
        </div>
    @endif

    <button wire:click='download' {{(count($quarter) == 0) ? 'disabled' : ''}} class="transition duration-300 ease-in-out rounded-full px-10 py-2 uppercase bg-green text-white mt-5 {{(count($quarter) == 0) ? 'opacity-50 pointer-events-none' : ''}}"
    >
        <div wire:loading.delay>
            <svg class="animate-spin h-5 w-5 -mb-1 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
        </div>

        <span wire:loading.delay.remove>{{__('expert-data.download')}}</span>
    </button>
</div>
