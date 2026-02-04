@extends('layout.client.master', ['bg' => 'volume-request'])

@section('extra_js')
    <script>
        function show_volume_request_closed(){
            document.querySelector('body').insertAdjacentHTML('afterbegin', `
                <x-volume_request_closed title="{{trans('riport.volume_request_closed')}}" message="" />
            `);
        }
    </script>
@endsection

@section('content')
<div class="bg-white bg-opacity-50 py-16 flex justify-center items-center">
    <div class="flex flex-col w-[100%] items-center justify-center">
        <h2 class="mb-5 text-white text-xl sm:hidden">{{ \Carbon\Carbon::now()->year}}.</h2>
        <div class="w-[90%] relative">
            <div class="h-2.5 bg-white rounded-lg w-full"></div>
            <div class="w-full h-full absolute top-0 items-start grid grid-cols-12">
                @foreach ($dates as $date)
                    @if ($date->isSameMonth($selected_date))
                        <div class="flex flex-col items-center justify-between -mt-5 group">
                            <div class="h-8 w-8 rounded-full bg-white mb-2 mt-2.5 flex justify-center items-center">
                                <div class="w-6 h-6 rounded-full bg-purple border border-white"></div>
                            </div>
                            <p class="text-purple hidden sm:block">{{$date->format('Y-m')}}</p>
                            <p class="text-purple sm:hidden">{{$date->format('m')}}.</p>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-between -mt-5 group">
                            @if (in_array($date, $volume_requests_in_year))
                                <a href="{{route('client.volume-request', ['date' => $date->format('Y-m')])}}" class="h-8 w-8 rounded-full bg-white mb-2 mt-2.5 flex justify-center items-center">
                                    <div class="w-6 h-6 rounded-full bg-white hover:bg-purple"></div>
                                </a>
                            @else
                                <div class="h-8 w-8 rounded-full bg-white mb-2 mt-2.5 flex justify-center items-center">
                                    <div class="w-6 h-6 rounded-full bg-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>                                          
                                    </div>
                                </div>
                            @endif
                            <p class="text-white hidden sm:block">{{$date->format('Y-m')}}</p>
                            <p class="text-white sm:hidden">{{$date->format('m')}}.</p>
                        </div>
                    @endif
                    
                @endforeach
            </div>
        </div>
    </div>
</div>


@if ($volume_requests->isEmpty())
    <div class="flex justify-center w-[100%] items-center p-10 bg-white bg-opacity-50 text-purple font-bold uppercase">
        {{__('riport.no_data_to_enter')}}
    </div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1">
        @foreach($volume_requests as $volume_request)
            <livewire:client.volume-request.card :volume_request="$volume_request" />
        @endforeach
    </div>
@endif
@endsection