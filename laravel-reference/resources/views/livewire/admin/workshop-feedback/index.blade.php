@section('extra_css')
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/invoicing.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/cgp-card.css') }}">
@endsection

@section('extra_js')
    <script src="{{asset('assets/js/toggleActive.js')}}?v={{time()}}" charset="utf-8"></script>
@endsection

@section('title')
    Admin Dashboard
@endsection

<div>
    {{ Breadcrumbs::render('workshop-feedbacks') }}
    <h1 class="col-12 pl-0">{{__('common.workshop_feedback')}}</h1>

    <x-asset.search desc="{{ __('asset.ascending_by_date') }}"
            asc="{{ __('asset.descending_by_date') }}" />

    <div class="mt-4">
        @if ($search === '')
            @foreach ($workshops as $date => $items)
                <div>
                    <div class="date-list case-list-in col-12 group @if(in_array($date, $visible_months)) active @endif" wire:click="show_month('{{$date}}')">
                        {{$date}}
                        <button class="caret-left date-icon float-right @if(in_array($date, $visible_months)) rotated-icon @endif">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                    <div class="@if(in_array($date, $visible_months)) d-flex flex-column @else d-none @endif">
                        @if(in_array($date, $visible_months))
                            @foreach ($items as $index => $workshop)
                                <div class="col-12 p-0" style="margin-bottom: 10px">
                                    <x-workshop-feedback.list-item
                                        :workshop="$workshop"
                                        :index="$index"
                                    />
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            @foreach ($workshops as $index => $workshop)
                <div class="col-12 p-0" style="margin-bottom: 10px" wire:key="{{ $index }}">
                    <x-workshop-feedback.list-item
                        :workshop="$workshop"
                        :index="$index"
                    />
                </div>
            @endforeach
        @endif
    </div>
</div>
