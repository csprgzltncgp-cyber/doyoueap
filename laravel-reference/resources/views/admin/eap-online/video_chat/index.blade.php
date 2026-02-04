<script>
    const room_id = '{{$room_id}}';
    const endTherapyTitle = '{{__("eap-online.video_therapy.therapy_end")}}';
    const endTherapyText = '{{__("eap-online.video_therapy.therapy_end_alt")}}';
    const endTherapyConfirm = '{{__("eap-online.video_therapy.end_therapy_yes")}}';
    const endTherapyCancel = '{{__("common.cancel")}}';
    const no_device_found_msg = "{{ __('eap-online.video_therapy.video_connection_no_device_found') }}"
    const no_device_permission_msg = "{{ __('eap-online.video_therapy.video_connection_no_device_permission') }}"
</script>
<script src="{{asset('js/video-chat.js')}}?v={{time()}}" defer></script>
<link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/css/master.css')}}?v={{time()}}">
<link rel="stylesheet" href="{{asset('assets/css/eap-online/video-chat.css')}}?v={{time()}}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row col-12 flex align-items-center justify-content-center mt-4">
    <div>
        <h1 class="text-center">
            {{__('eap-online.video_therapy.client')}} : {{ $client->username }}
        </h1>

        <div class="video-container text-center position-relative">
            <div id="userVideo"></div>
            <div id="partnerVideo"></div>
        </div>

        {{-- If loading or waiting for client --}}
        <div id="spinner" class="d-flex flex-column justify-content-center align-items-center mx-auto center">
            <img src="{{asset('assets/img/spinner.svg')}}" alt="spinner" class="w-32" />
        </div>
        {{-- If loading or waiting for client --}}

        <div class="d-flex justify-content-end">
            {{-- muted call ? --}}
            <button id="unmute_button" class="button btn-radius d-none align-items-center" style="margin-right: 0px!important">
                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" style="height: 25px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="pt-1">{{__('eap-online.video_therapy.connect')}}</span>
            </button>

            {{-- mute call button --}}
            <button id="mute_button" class="button btn-radius d-none align-items-center" style="margin-right: 0px!important">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 25px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="pt-1">{{__('eap-online.video_therapy.disconnect')}}</span>
            </button>
            {{-- mute call button --}}

            {{-- end call button --}}
            <button id="end_button" class="button btn-radius d-none align-items-center d-none mr-0" style="margin-right: 0px!important">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 25px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                </svg>
                <span class="pt-1">{{__("eap-online.video_therapy.end_therapy")}}</span>
            </button>
            {{-- end call button --}}
        </div>
    </div>
</div>
