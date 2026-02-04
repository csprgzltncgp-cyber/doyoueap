@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/list.css?v={{time()}}">
@endsection

@section('extra_js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
<script>
    function warningBeforeVideoTherapy(url) {
        Swal.fire({
            title: '{{ __('common.system-message') }}!',
            text: '{{ __('eap-online.video_therapy.expert-waring') }}',
            imageUrl: '/assets/img/info.png',
            imageHeight: 78,
            confirmButtonText: '{{ __('eap-online.video_therapy.join_therapy')}}',
        }).then(function() {
            window.open(url,'name', 'width=1000,height=800');
        });
    }
</script>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h1>{{ __('common.onsite-consultation') }}</h1>
    </div>

    <div class="col-12">
        @if($appointments->isEmpty())
            <p>No onsite consultations scheduled.</p>
        @else
            <div class="row col-12 case-list-holder">
                @foreach($appointments as $appointment)
                    <div class="case-list">
                        <p><strong>Client:</strong> {{ $appointment->user->username ?? 'N/A' }}</p>
                        <p><strong>From:</strong> {{ $appointment->from ? Carbon\Carbon::parse($appointment->date->date->format('Y-m-d') . ' ' . $appointment->from->format('H:i'))->format('Y-m-d H:i') : 'N/A' }}</p>
                        <p><strong>To:</strong> {{ $appointment->to ? Carbon\Carbon::parse($appointment->date->date->format('Y-m-d') . ' ' . $appointment->to->format('H:i'))->format('Y-m-d H:i') : 'N/A' }}</p>
                        <button
                            target="popup"
                            onclick="warningBeforeVideoTherapy('{{ route('admin.eap-online.video_chat', ['client_id' => $appointment->user->id, 'room_id' => 'onsite-consultation-' . $appointment->user->id . '-' . $appointment->expert->id . '-' . $appointment->id]) }}')"
                            class="mt-4 p-3"
                            style="background-color: rgb(89,198,198); color: white; outline: 0; border: 0; border-radius: 12px;"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px; margin-right: 5px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>

                            {{ __('eap-online.video_therapy.join_therapy') }}
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
