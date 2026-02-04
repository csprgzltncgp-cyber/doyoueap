@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="{{ asset('/assets/css/activity-plan.css') }}">
    <link rel="stylesheet" href="{{asset('assets/css/workshop-feedback.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/workshops.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/crisis.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/prizegame.css')}}?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/datetime.css?t={{time()}}">
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/toggle_year_month_list.js')}}"></script>
    <script src="{{asset('assets/js/toggleActive.js')}}?v={{time()}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="{{asset('assets/js/datetime.js')}}" charset="utf-8"></script>
    <script src="{{asset('assets/js/prizegame.js')}}" charset="utf-8"></script>

    <script>
        function toggleActivityPlanMember(element, activity_plan_id, model_id, model_class) {
            let formData = new FormData();
            formData.append('model_id', model_id);
            formData.append('model_class', model_class);
            formData.append('activity_plan_id', activity_plan_id);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{route(auth()->user()->type . '.activity-plan.toggle-activity-plan-member')}}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    if(element.classList.contains('active')) {
                        element.classList.remove('active');
                    }else {
                        element.classList.add('active');
                    }

                    Livewire.emit('refresh_activity_plan');
                }
            });
        }
    </script>
@endsection

@section('content')
    {{ Breadcrumbs::render('activity-plan.index') }}

    <div class="year-selector mt-4 mb-1">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor"style="heigth:20px; width:20px;" class="mr-1 mb-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
        </svg>

        <span class="mr-2">{{__('data.year')}}:</span>
        <select id="year-selector">
            <option>2024</option>
        </select>
    </div>

    @livewire('admin.activity-plan.show', ['activity_plan' => $activity_plan])
@endsection

@section('modal')
{{-- modal for prizegame date change --}}
<div class="modal" tabindex="-1" id="modal-set-date" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('eap-online.video_therapy.set_date')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.prizegame.games.set-date')}}" method="post">
                    {{csrf_field()}}
                    <div class="d-flex w-100 justify-content-between">
                        <input type="text" name="from" class="datepicker"
                               placeholder="{{ __('common.from') }}" style="width:40%;margin-right:4%;"
                               required/>
                        <input type="text" name="to" class="datepicker"
                               placeholder="{{ __('common.to') }}" style="width:40%;" required/>
                    </div>
                    <input type="hidden" name="game_id" value="">
                    <button class="button btn-radius float-right m-0">
                        {{__('common.select')}}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- modal for prizegame date change --}}
@endsection
