@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/invoices.css?v={{time()}}">
@endsection

@section('extra_js')
    <script type="text/javascript">
        const invoice_id = {{$invoice->id}};
        const is_filtered_result = {{request()->filtered ?: 0}};
    </script>
    <script src="/assets/js/invoice/master.js?v={{time()}}" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
@endsection

@section('content')
    {{ Breadcrumbs::render('invoices.view', $invoice->id) }}

    <h1 class="w-100">{{__('common.view-invoice')}}</h1>
    <p id="main-infos">{{$invoice->name}} - {{$invoice->payment_deadline}}</p>
    <ul id="invoice-list">
        <li class="@if($invoice->data_changes && $invoice->data_changes->firstWhere('attribute','name') && $invoice->data_changes->firstWhere('attribute','name')->count()) invoice-changed-input @endif">
            {{__('common.name-of-supplier')}}: {{$invoice->name}}</li>
        @if($invoice->expert->isHungarian())
            <li class="@if($invoice->data_changes && $invoice->data_changes->firstWhere('attribute','tax_number') && $invoice->data_changes->firstWhere('attribute','tax_number')->count()) invoice-changed-input @endif">
                {{__('common.tax_number')}}: {{$invoice->tax_number}}</li>
        @endif
        <li class="@if($invoice->data_changes && $invoice->data_changes->firstWhere('attribute','email') && $invoice->data_changes->firstWhere('attribute','email')->count()) invoice-changed-input @endif">
            {{__('common.suppliers-email-address')}}: {{$invoice->email}}</li>
        <li class="@if($invoice->data_changes && $invoice->data_changes->firstWhere('attribute','account_number') &&$invoice->data_changes->firstWhere('attribute','account_number')->count()) invoice-changed-input @endif">@if($invoice->expert->isHungarian())
                {{__('common.account-number')}}
            @else
                {{__('common.iban-number')}}
            @endif: {{$invoice->account_number}}</li>
        @if(!$invoice->expert->isHungarian())
            <li class="@if($invoice->data_changes && $invoice->data_changes->firstWhere('attribute','swift') && $invoice->data_changes->firstWhere('attribute','swift')->count()) invoice-changed-input @endif">
                {{__('common.swift-code')}}: {{$invoice->swift}}</li>
        @endif

        @if(!empty($invoice->international_tax_number))
            <li class="@if($invoice->data_changes && $invoice->data_changes->firstWhere('attribute','international_tax_number') && $invoice->data_changes->firstWhere('attribute','international_tax_number')->count()) invoice-changed-input @endif">
                {{__('common.international-tax-number')}}: {{$invoice->international_tax_number}}</li>
        @endif

        <li class="@if($invoice->data_changes && $invoice->data_changes->firstWhere('attribute','bank_name') && $invoice->data_changes->firstWhere('attribute','bank_name')->count()) invoice-changed-input @endif">
            {{__('common.name-of-bank')}}: {{$invoice->bank_name}}</li>
        <li class="@if($invoice->data_changes && $invoice->data_changes->firstWhere('attribute','bank_address') && $invoice->data_changes->firstWhere('attribute','bank_address')->count()) invoice-changed-input @endif">
            {{__('common.address-of-bank')}}: {{$invoice->bank_address}}</li>
        <li class="@if($invoice->data_changes && $invoice->data_changes->firstWhere('attribute','destination_country') && $invoice->data_changes->firstWhere('attribute','destination_country')->count()) invoice-changed-input @endif">
            {{__('common.country')}}: {{optional($invoice->country())->code}}</li>
        <li class="@if($invoice->data_changes && $invoice->data_changes->firstWhere('attribute','currency') && $invoice->data_changes->firstWhere('attribute','currency')->count()) invoice-changed-input @endif">
            {{__('common.currency')}}: {{strtoupper($invoice->currency)}}</li>
        <li>{{__('common.invoice-number')}}: {{$invoice->number}}</li>
        <li>{{__('common.invoice-upload-date')}}: {{Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d')}}</li>
        <li>{{__('common.date-of-issue')}}: {{$invoice->date_of_issue}}</li>
        <li>{{__('common.due-date')}}: {{$invoice->payment_deadline}}</li>

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

        @if(!empty($invoice->workshop_total))
            <li>{{__('invoice.workshops_sum')}}: {{$invoice->workshop_total}} {{strtoupper($invoice->currency)}}</li>
        @endif

        @if(!empty($invoice->crisis_total))
            <li>{{__('invoice.crisis_sum')}}: {{$invoice->crisis_total}} {{strtoupper($invoice->currency)}}</li>
        @endif

        @if(!empty($invoice->other_activity_total))
            <li>{{__('invoice.other_activity_sum')}}: {{$invoice->other_activity_total}} {{strtoupper($invoice->currency)}}</li>
        @endif

        @if(!empty($invoice->cases_total))
            <li>{{__('invoice.cases_sum')}}: {{$invoice->cases_total}} {{strtoupper($invoice->currency)}}</li>
        @endif

        @if(!empty($invoice->expert->invoice_datas->hourly_rate_50))
            <li>{{__('invoice.hourly_rate_50')}}: {{$invoice->expert->invoice_datas->hourly_rate_50}} {{strtoupper($invoice->expert->invoice_datas->currency)}}</li>
        @endif

        @if(!empty($invoice->expert->invoice_datas->hourly_rate_30))
            <li>{{__('invoice.hourly_rate_30')}}: {{$invoice->expert->invoice_datas->hourly_rate_30}} {{strtoupper($invoice->expert->invoice_datas->currency)}}</li>
        @endif

        <!-- Show custom invoice items, but only for invoices created after this feature was implemented,
        as to not cause confusion when viewing older invoices and seeing these items -->
        @if(!empty($custom_items) && \Carbon\Carbon::parse($invoice->created_at)->gte('2023-12-01'))
            @foreach($custom_items as $item)
                <li>{{$item->name}}: {{$item->amount}} {{strtoupper($invoice->currency)}}</li>
            @endforeach
        @endif

        @if(!empty($additional_items))
            @foreach($additional_items as $item)
                <li class="additional-invoice-items">{{$item->name}}: {{$item->price}} {{strtoupper($invoice->currency)}}</li>
            @endforeach
        @endif

        <li>{{__('common.total-amount-due')}}: {{$invoice->grand_total}} {{strtoupper($invoice->currency)}}</li>

        @if ($consultation_count)
            <li>{{__('common.month-name_'.\Carbon\Carbon::parse($invoice->date_of_issue)->subMonthNoOverflow()->format('m'))}} {{__('common.in_month_consultation')}}: {{$consultation_count->count}}</li>
        @endif

        <li>
            <a href="{{route('admin.invoices.downloadInvoice',['id' => $invoice->id])}}" target="_blank">
                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg"
                    style="height:20px; margin-bottom: 3px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                {{__('common.download-view-invoice')}}
            </a>
        </li>

    </ul>
    <div id="invoice-view-link-holder">
        <a id="back-to-list" @if(!request()->filtered) href="{{route('admin.invoices.index')}}"
           @else  onclick="history.back();" @endif>{{__('common.back-to-list')}}</a>

        <a id="email-send" href="mailto:{{$invoice->expert->email}}">{{__('common.send-email-to-councelor')}}</a>
    </div>
@endsection
