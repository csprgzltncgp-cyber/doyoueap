@extends('layout.master')

@section('title', 'Admin Dashboard')

@section('extra_css')
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/invoicing.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/invoice-helper/cgp-card.css') }}">
    <link rel="stylesheet" href="{{asset('assets/css/list.css')}}?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/invoices.css')}}?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/form.css?v={{time()}}">
    <link rel="stylesheet" href="/assets/css/perfix-input.css?v={{time()}}">
    <link rel="stylesheet" href="{{asset('assets/css/bordered-checkbox.css')}}?v={{time()}}">
    <style>
        .lis-element-div {
            width: 100%;
            display: none;
        }

        .lis-element-div.active {
            display: block !important;
        }

        .lis-element-div-c {
            display: none;
        }

        .lis-element-div-c.active {
            display: block !important;
        }

        .list-element.col-12 {
            display: inline-block !important;
        }
    </style>
@endsection

@section('extra_js')
    <script>
        const spinner = document.getElementById('invoices_loading_spinner');
        const months_container = document.getElementById('months_container');
        
        function downloadAllEnvelopes(date){
            $('.loading_indicator').removeClass('d-none');
            $('.print_all_text').hide();


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                xhrFields: {
                    responseType: "blob",
                },
                url: '/admin/invoice-helper/envelope/all/download/' + date,
                success: function (response) {
                    $('.loading_indicator').addClass('d-none');
                    $('.print_all_text').show();

                    let a = document.createElement("a")
                    a.href = URL.createObjectURL(response)
                    a.download = 'envelopes.pdf'
                    a.style.display = "none"
                    document.body.appendChild(a)
                    a.click()
                }
            });
        }
    </script>
@endsection

@section('content')
<div>
    {{ Breadcrumbs::render('invoices.direct-invoices') }}

    <h1>
        {{__('invoice-helper.invoice-helper')}}
    </h1>

    <x-invoice-helper.menu :invoicing_years="$invoicing_years" :selected_year="$selected_year" />

    <img id="invoices_loading_spinner" class="d-none spinner" src="{{asset('assets/img/spinner.svg')}}"
    alt="spinner">

    <div id="months_container">
        @foreach(collect($dates)->reverse() as $date)
            <div class="case-list-in col-12 group">
                {{$date->format('Y-m')}}
                <div class="caret-left float-right">
                    <button class="mr-2" style="color: #306bff;" onclick="Livewire.emit('openDate', '{{$date->format('Y-m-d')}}')">
                        <span>{{__('invoice-helper.show-all-companies')}}</span>
                    </button>

                    <img class="loading_indicator d-none" style="width: 20px; height: 20px" src="{{asset('assets/img/spinner.svg')}}" alt="spinner" >

                    <a onclick="downloadAllEnvelopes('{{$date}}')" class="print_all_text float-right" style="{{all_enevelopes_printed_in_month($date) ? 'color:#a33095;' : 'color: #59c6c6;'}} margin-left:10px">
                        @if(all_enevelopes_printed_in_month($date))
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; width:20px; margin-right:5px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; width:20px; margin-right:5px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                        @endif
                    <span>{{__('invoice-helper.envelope.print-all')}}</span>
                    </a>
                </div>
            </div>
            @livewire('admin.invoice-helper.envelope.month', ['date' => $date->format('Y-m-d')], key('date-' . $date->format('Y-m-d')))
        @endforeach
    </div>
</div>
@endsection
