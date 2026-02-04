@extends('layout.master')

@section('title')
    Expert Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/invoices.css?v={{time()}}">
@endsection

@section('extra_js')
    <script src="/assets/js/invoice/master.js?v={{time()}}" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    <script type="text/javascript">
        const invoice_id = {{$invoice->id}};
    </script>
@endsection

@section('content')
    <h1 class="w-100">{{__('common.view-invoice')}}</h1>
    <p id="main-infos">{{$invoice->name}} - {{$invoice->payment_deadline}}</p>
    <ul id="invoice-list">
        <li>{{__('common.name-of-supplier')}}: {{$invoice->name}}</li>
        @if($invoice->expert->isHungarian())
            <li>{{__('common.tax_number')}}: {{$invoice->tax_number}}</li>
        @endif
        <li>{{__('common.suppliers-email-address')}}: {{$invoice->email}}</li>
        <li>@if($invoice->expert->isHungarian())
                {{__('common.account-number')}}
            @else
                {{__('common.iban-number')}}
            @endif
            : {{$invoice->account_number}}</li>
        @if(!$invoice->expert->isHungarian())
            <li>{{__('common.swift-code')}}: {{$invoice->swift}}</li>
        @endif

        @if(!empty($invoice->international_tax_number))
            <li class="@if($invoice->data_changes && $invoice->data_changes->firstWhere('attribute','international_tax_number') && $invoice->data_changes->firstWhere('attribute','international_tax_number')->count()) invoice-changed-input @endif">
                {{__('common.international-tax-number')}}: {{$invoice->international_tax_number}}</li>
        @endif

        <li>{{__('common.name-of-bank')}}: {{$invoice->bank_name}}</li>
        <li>{{__('common.address-of-bank')}}: {{$invoice->bank_address}}</li>
        <li>{{__('common.country')}}: {{optional($invoice->country)->code}}</li>
        <li>{{__('common.currency')}}: {{strtoupper($invoice->currency)}}</li>
        <li>{{__('common.invoice-number')}}: {{$invoice->number}}</li>
        <li>{{__('common.date-of-issue')}}: {{$invoice->date_of_issue}}</li>
        <li>{{__('common.due-date')}}: {{$invoice->payment_deadline}}</li>

        @if (auth()->user()->invoice_datas()->first()->invoicing_type !== \App\Enums\InvoicingType::TYPE_CUSTOM)

            @if(auth()->user()->invoice_datas()->first()->invoicing_type == \App\Enums\InvoicingType::TYPE_FIXED)
                @if ($consultation_count)
                    <li>{{__('common.month-name_'.\Carbon\Carbon::parse($invoice->date_of_issue)->subMonthNoOverflow()->format('m'))}} {{__('common.in_month_consultation')}}: {{$consultation_count->count}}</li>
                @endif
            @else
                @if($invoice_case_datas->groupBy('permission_id')->count())
                    <li>
                        {{__('common.number_of_consultations')}}:
                        </br></br>
                        @foreach ($invoice_case_datas as $permission_id => $invoice_case_datas)
                            {{ $case_data_periods->where('permission_id', $permission_id)->first()['permission_name'] }} ({{$invoice_case_datas->sum('consultations_count')}}) -
                            {{$case_data_periods->where('permission_id', $permission_id)->first()['period']->implode(', ')}}: {{$invoice_case_datas->map(function ($item){return $item->case_identifier . '/' . $item->consultations_count;})->implode(', ')}}
                            @if(!$loop->last)</br></br> @endif
                        @endforeach
                    </li>
                @endif
            @endif

            @if($workshop_case_datas->count())
                <li>
                    {{__('common.number_of_workshops')}}: {{count($workshop_case_datas)}} ({{$workshop_data_periods->implode(', ')}}: {{$workshop_case_datas->map(function ($item){return '#' . $item->activity_id;})->implode(', ')}})
                </li>
            @endif

            @if($crisis_case_datas->count())
                <li>
                    {{__('common.number_of_crisis')}}: {{count($crisis_case_datas)}} ({{$crisis_data_periods->implode(', ')}}: {{$crisis_case_datas->map(function ($item){return '#' . $item->activity_id;})->implode(', ')}})
                </li>
            @endif

            @if($other_activity_case_datas->count())
                <li>
                    {{__('common.number_of_other_activities')}}: {{count($other_activity_case_datas)}} ({{$other_activity_data_periods->implode(', ')}}: {{$other_activity_case_datas->map(function ($item){return $item->activity_id;})->implode(', ')}})
                </li>
            @endif

            @if($live_webinar_case_datas->count())
                <li>
                    {{__('common.number_of_live_webinars')}}: {{count($live_webinar_case_datas)}} ({{$live_webinar_data_periods->implode(', ')}}: {{$live_webinar_case_datas->map(function ($item){return '#' . $item->activity_id;})->implode(', ')}})
                </li>
            @endif

            @if(!empty($invoice->workshop_total))
                <li>{{__('invoice.workshops_sum')}}: {{$invoice->workshop_total}} {{strtoupper($invoice->currency)}}</li>
            @endif

            @if(!empty($invoice->crisis_total))
                <li>{{__('invoice.crisis_sum')}}: {{$invoice->crisis_total}} {{strtoupper($invoice->currency)}}</li>
            @endif

            @if(!empty($invoice->other_activity_total))
                <li>{{__('invoice.other_activity_sum')}}: {{$invoice->other_activity_total}} {{strtoupper($invoice->currency)}}</li>
            @endif

            @if(!empty($invoice->live_webinar_total))
                <li>{{__('invoice.live_webinars_sum')}}: {{$invoice->live_webinar_total}} {{strtoupper($invoice->currency)}}</li>
            @endif

            @if(!empty($invoice->cases_total))
                @if (auth()->user()->invoice_datas()->first()->invoicing_type === \App\Enums\InvoicingType::TYPE_FIXED)
                    <li>{{__('invoice.fixed_wage_consultation')}}: {{auth()->user()->invoice_datas()->first()->fixed_wage}} {{strtoupper($invoice->currency)}}</li>
                @else
                    <li>{{__('invoice.cases_sum')}}: {{$invoice->cases_total}} {{strtoupper($invoice->currency)}}</li>
                @endif
            @endif
        @endif

        <!-- Show custom invoice items, but only for invoices created after this feature was implemented,
        as to not cause confusion when viewing older invoices and seeing these items -->
        @if(!empty($custom_items) && \Carbon\Carbon::parse($invoice->created_at)->gte('2023-11-01'))
            @foreach($custom_items as $item)
                <li>{{$item->name}}: {{$item->amount}} {{strtoupper($invoice->currency)}}</li>
            @endforeach
        @endif

        @if(!empty($additional_items))
            @foreach($additional_items as $item)
                <li>{{$item->name}}: {{$item->price}} {{strtoupper($invoice->currency)}}</li>
            @endforeach
        @endif

        <li>{{__('common.total-amount-due')}}: {{$invoice->grand_total}} {{strtoupper($invoice->currency)}}</li>

        <li><a href="{{route('expert.invoices.downloadInvoice',['id' => $invoice->id])}}" target="_blank">
                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                     style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                {{__('common.download-view-invoice')}}</a></li>
    </ul>
    <div id="invoice-view-link-holder">
        <a id="back-to-list" href="{{route('expert.invoices.index')}}">{{__('common.back-to-list')}}</a>
    </div>
@endsection
