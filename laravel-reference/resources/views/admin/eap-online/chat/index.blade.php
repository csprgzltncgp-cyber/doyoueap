<script>
    const createMessageRoute = "{{route('admin.eap-online.online_chat_store')}}";
    const expertTypingRoute = "{{route('admin.eap-online.online_chat_typing')}}";
    const room_id = '{{$room_id}}';
    const endTherapyTitle = '{{__("eap-online.video_therapy.therapy_end")}}';
    const endTherapyText = '{{__("eap-online.video_therapy.therapy_end_alt")}}';
    const endTherapyConfirm = '{{__("eap-online.video_therapy.end_therapy_yes")}}';
    const endTherapyCancel = '{{__("common.cancel")}}';
</script>
<script src="{{asset('js/chat.js')}}?v={{time()}}" defer></script>
<link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/css/master.css')}}?v={{time()}}">
<link rel="stylesheet" href="{{asset('assets/css/eap-online/chat.css')}}?v={{time()}}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    textarea {
        padding: 10px 30px 10px 33px!important;
    }
</style>

<div class="row col-12 flex align-items-center justify-content-center mt-4">
    <div>
        <h1 class="text-center">
            {{__('eap-online.video_therapy.client')}} : {{ $client->username }}
        </h1>

        <div class="chat-container d-flex flex-column justify-content-between" style="font-family: CalibriI; font-weight: normal;">
            <div class="chat" data-chat-id="{{$room_id}}">
                @foreach ($messages as $message)
                    @if($message->is_from_expert())
                        <div class="expertMessage align-items-end">
                            <span>{{$message->created_at->format('Y.m.d H:i')}}</span>
                            <p class="text-right">
                                {{$message->message}}
                            </p>
                        </div>
                    @else
                        <div class="clientMessage">
                            <span>{{$message->created_at->format('Y.m.d H:i')}}</span>
                            <p class="text-left">
                                {{$message->message}}
                            </p>
                        </div>
                    @endif
                @endforeach
            </div>
            <span id="client-is-typing"></span>
        </div>

        <form id="message-form" name="message" style="font-family: CalibriI; font-weight: normal;">
            @csrf
            <textarea maxlength="750" rows="1" id="message" name="message" placeholder="{{__('eap-online.chat_therapy.message_text')}}"
            style=""></textarea>
        </form>

        <div class="d-flex justify-content-between">

            {{-- end call button --}}
            <button id="end_button" class="button btn-radius align-items-center mr-0" style="margin-right: 0px!important; margin-left: 0px!important">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 25px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                </svg>
                <span class="pt-1">{{__("eap-online.video_therapy.end_therapy")}}</span>
            </button>
            {{-- end call button --}}

            <button id="send_message" class="button btn-radius align-items-center" style="margin-right: 0px!important;">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 25px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                  </svg>
                  <span class="pt-1">{{__("common.send")}}</span>
            </button>
        </div>
    </div>
</div>
