@extends('layout.master')

@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/cases/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/workshops.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/cases/list_in_progress.css')}}?v={{time()}}">
@endsection

@section('extra_js')
    <script>
        function yearOpen(id) {
            $("div#" + id).toggleClass("active");
            $("div#" + id).prev('.case-list-in').toggleClass("active");
            $("#y" + id).toggleClass("rotated-icon");
        }

        function monthOpen(id) {
            $("div#" + id).toggleClass("active");
            $("div#" + id).prev('.case-list-in').toggleClass("active");
            $("#m" + id).toggleClass("rotated-icon");
        }

        function delete_webinar(id, element) {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.yes-delete-it')}}',
                cancelButtonText: '{{__('common.cancel')}}',
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: 'POST',
                        type: 'DELETE',
                        url: '/ajax/delete-live-webinar/' + id,
                        success: function (data) {
                            $(element).closest('.list-element').remove();
                        }
                    });
                }
            });

        }

        const copyTextToClipboard = async (value) => {
            if (!value) {
                throw new Error('Missing value');
            }

            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(value);
                return;
            }

            const tempInput = document.createElement('input');
            tempInput.value = value;
            tempInput.setAttribute('readonly', '');
            tempInput.style.position = 'absolute';
            tempInput.style.left = '-9999px';
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
        };

        $(document).on('click', '[data-copy-value]', async function() {
            const value = $(this).data('copy-value');

            try {
                await copyTextToClipboard(value);
                Swal.fire({
                    title: 'Másolva!',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });
            } catch (error) {
                Swal.fire({
                    title: 'Nincs mit másolni',
                    text: '',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('eap-online.live-webinars') }}
            <h1>{{ __('eap-online.live-webinars.menu') }}</h1>
            <a href="{{route(auth()->user()->type . '.eap-online.live-webinar.create')}}">{{ __('eap-online.live-webinars.add') }}</a><br>
        </div>
        <div class="col-12 case-list-holder">
            @foreach($years as $year)
                <div class="case-list-in col-12 group" onclick="yearOpen({{ $year }})">
                    {{ $year}}
                    <button class="caret-left float-right">
                        <svg id="y{{$year}}" xmlns="http://www.w3.org/2000/svg"
                            style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                <div class="lis-element-div" id="{{$year}}">
                    @foreach($months as $month)
                        @if (\Carbon\Carbon::parse($month)->year === $year)
                            <div class="workshop-list-holder">
                                <div class="case-list-in col-12 group" onclick="monthOpen('{{$month}}')">
                                    {{$month}}
                                    <button class="caret-left float-right">
                                        <svg id="m{{$month}}" xmlns="http://www.w3.org/2000/svg"
                                            style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="lis-element-div-c" id="{{$month}}">
                                    @foreach($live_webinars->sortByDesc('from') as $live_webinar)
                                        @if($live_webinar->from->format('Y-m') === $month)
                                            <div class="list-element col-12">
                                                <span class="data mr-0">
                                                    #{{ $live_webinar->activity_id }} - 
                                                    {{ \App\Models\EapOnline\EapLanguage::query()->find($live_webinar->language_id)?->name }} - 
                                                    {{ $live_webinar->permission->translation->value }} -
                                                    {{ $live_webinar->expert->name }} -
                                                    {{ $live_webinar->from->format('Y-m-d H:i') }}
                                                </span>
                                                <a class="edit-workshop btn-radius" style="--btn-margin-left: 15px;"
                                                    href="{{route('admin.eap-online.live-webinar.edit', $live_webinar)}}">
                                                    <img class="mr-1" style="width:20px;" src="{{asset('assets/img/select.svg')}}">
                                                    {{__('workshop.select_button')}}
                                                </a>
                                                @if($live_webinar?->zoom_join_url)
                                                    <button class="delete-button-from-list btn-radius"
                                                        style="--btn-min-width: var(--btn-func-width); cursor: pointer;"
                                                        data-copy-value="{{ $live_webinar->zoom_join_url }}"
                                                        {{ empty($live_webinar->zoom_join_url) ? 'disabled' : '' }}>
                                                        {{ __('eap-online.live-webinars.copy_link') }}
                                                    </button>
                                                @endif

                                                <button class="delete-button-from-list btn-radius" style="--btn-min-width: var(--btn-func-width)" onclick="delete_webinar({{ $live_webinar->id }}, this)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-0" style="height: 20px; width: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endsection
